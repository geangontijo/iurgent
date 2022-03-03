<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Entities\User;
use App\Services\ApiResponse;
use Illuminate\Http\Request;
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

        $userData = $request->only('name', 'email', 'password');

        $userData['password'] = Hash::make($userData['password']);
        $user = User::create($userData);
        ApiResponse::$statusCode = Response::HTTP_CREATED;
        ApiResponse::$responseData['Authorization'] = User::generateJwt($user->id);
        return ApiResponse::response();
    }
}
