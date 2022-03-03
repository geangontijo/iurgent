<?php

namespace App\Exceptions\Auth;

use App\Services\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UnkownUserException extends \Exception
{
    public function render(Request $request)
    {
        ApiResponse::$statusCode = Response::HTTP_NOT_FOUND;
        ApiResponse::addError('Usuário não encontrado');
        return ApiResponse::response();
    }
}
