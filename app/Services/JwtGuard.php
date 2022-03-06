<?php

namespace App\Services;

use App\Entities\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class JwtGuard implements Guard
{
    use GuardHelpers;

    public function __construct(
        private Request $request
    ) {
    }

    public function validate(array $credentials = [])
    {
        return false;
    }

    public function user()
    {
        try {
            if (!($authorization = $this->request->header('Authorization'))) {
                return;
            }

            $authorization = \str_replace('Bearer ', '', $authorization);

            $jwtData = (array) JWT::decode($authorization, new Key(env('JWT_KEY'), 'HS256'));

            return new User($jwtData);
        } catch (\Throwable) {
        }
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }
}
