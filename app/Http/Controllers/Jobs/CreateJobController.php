<?php

namespace App\Http\Controllers\Jobs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateJobController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        /** @var \App\Entities\User $user */
        $user = Auth::user();

        if (!empty($failedFields = $user->hasAddress())) {
            $request->validate($failedFields);
        }
        dd('chego');
    }
}
