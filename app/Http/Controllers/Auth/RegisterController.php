<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Entities\User;
use App\Entities\UserPermissions;
use App\Services\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function __invoke(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $password = Hash::make($request->password);
        $user->password = $password;
        $user->save();

        ApiResponse::$statusCode = Response::HTTP_CREATED;
        ApiResponse::$responseData['Authorization'] = User::generateJwt($user->id);
        return ApiResponse::response();
    }
}
