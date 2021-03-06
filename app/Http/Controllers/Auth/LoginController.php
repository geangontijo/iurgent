<?php

namespace App\Http\Controllers\Auth;

use App\Entities\User;
use App\Exceptions\Auth\UnkownUserException;
use App\Http\Controllers\Controller;
use App\Services\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        $user = User::findUserToLogin($request->email);

        if (empty($user)) {
            throw new UnkownUserException();
        }

        if (!Hash::check($request->password, $user->password)) {
            throw new UnkownUserException();
        }

        User::generateJwt($user->id);
        ApiResponse::$statusCode = Response::HTTP_OK;
        ApiResponse::$responseData['Authorization'] = User::generateJwt($user->id);

        return ApiResponse::response();
    }
}
