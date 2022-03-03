<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiResponse
{
    private static array $response = [];

    public static array $responseData = [];

    public static int $statusCode = Response::HTTP_NOT_IMPLEMENTED;

    /**
     * @param string ...$errors
     * @return void
     */
    public static function addError(...$errors): void
    {
        foreach ($errors as $key => $error) {
            self::$response['errors'][$key] = $error;
        }
    }

    public static function response(): Response
    {
        $response = self::$response;

        if (self::$responseData) {
            $response['results'] = self::$responseData;
        }

        return new JsonResponse($response, self::$statusCode);
    }
}
