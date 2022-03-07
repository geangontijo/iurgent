<?php

namespace App\Http\Controllers\Jobs;

use App\Entities\Job;
use App\Entities\JobOpeningHour;
use App\Http\Controllers\Controller;
use App\Services\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CreateJobController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $user = Auth::user();

        // if (!empty($failedFields = $user->hasAddress())) {
        //     $request->validate($failedFields);
        // }
        // dd('chego');

        $request->validate([
            'category_id' => ['required', 'numeric', 'exists:jobs_categories,id'],
            'opening_hours' => ['required', 'json'],
            'details' => ['nullable'],
        ]);

        $openingHours = \json_decode($request->opening_hours, true);

        Validator::make(['opening_hours' => $openingHours], [
            'opening_hours' => ['required', 'array', 'min:2'],
        ])->validate();

        $openingHours = \array_reduce($openingHours, function (array $total, array $day) {
            $existsInArray = \array_filter($total, function (array $item) use ($day) {
                return $item['day_name'] === $day['day_name'];
            });

            if (0 === \count($existsInArray)) {
                $total[] = $day;
            }

            return $total;
        }, []);

        $openingHoursModels = [];
        foreach ($openingHours as $day) {
            Validator::make($day, [
                'day_name' => ['required', 'string'],
                'will_open_in' => ['required', 'date_format:H:i'],
                'will_close_in' => ['required', 'date_format:H:i'],
            ])->validate();

            $openingHour = new JobOpeningHour();
            $openingHour->day_name = $day['day_name'];
            $openingHour->will_open_in = $day['will_open_in'];
            $openingHour->will_close_in = $day['will_close_in'];
            $openingHoursModels[] = $openingHour;
        }

        $job = new Job();
        $job->details = $request->details;
        $job->user()->associate($user->id);
        $job->category()->associate($request->category_id);

        DB::transaction(function () use ($job, $openingHoursModels) {
            $job->save();
            $job->openingHours()->saveMany($openingHoursModels);
        });

        ApiResponse::$responseData[] = $job;
        ApiResponse::$statusCode = Response::HTTP_CREATED;

        return ApiResponse::response();
    }
}
