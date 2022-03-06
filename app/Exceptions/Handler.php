<?php

namespace App\Exceptions;

use App\Services\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
        });
    }

    public function render($request, Throwable $exception)
    {
        if (false !== stripos($request->getPathInfo(), '/api')) {
            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                ApiResponse::$statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
                ApiResponse::addError(...$exception->validator->getMessageBag()->getMessages());

                return ApiResponse::response();
            }
            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                ApiResponse::$statusCode = Response::HTTP_UNAUTHORIZED;
                ApiResponse::addError('Não autorizado.');

                return ApiResponse::response();
            }
        }

        return parent::render($request, $exception);
    }
}
