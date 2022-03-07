<?php

namespace App\Exceptions;

use App\Services\ApiResponse;
use Illuminate\Http\Request;

class ApiError extends \Exception
{
    public function __construct(protected string|array $errors = '', int $code)
    {
        $this->code = $code;
    }

    public function render(Request $request)
    {
        ApiResponse::$statusCode = $this->code;

        if (is_string($this->errors)) {
            ApiResponse::addError($this->errors);
        } else {
            ApiResponse::addError(...$this->errors);
        }

        return ApiResponse::response();
    }
}
