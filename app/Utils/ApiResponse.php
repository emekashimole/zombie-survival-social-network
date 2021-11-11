<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{

    // Response 200 with data and message
    static function ofData($data, ?string $message = null)
    {
        return response([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], Response::HTTP_OK);
    }

    // Response 200 with only message
    static function ofMessage(string $message)
    {
        return response([
            'success' => true,
            'message' => $message
        ], Response::HTTP_OK);
    }

    static function ofPaginatedData($data)
    {
        $metaData = [
            'currentPage' => $data->currentPage(),
            'hasMorePages' => $data->hasMorePages(),
            'lastPage' => $data->lastPage(),
            'nextPageUrl' => $data->nextPageUrl(),
            'perPage' => $data->perPage(),
            'previousPageUrl' => $data->previousPageUrl(),
            'total' => $data->total(),
            'url' => $data->url()
        ];

        $responseData = array_merge(['success' => true], $metaData, ['data' => $data]);
        return response($responseData, Response::HTTP_OK);
    }

    static function ofUnauthorised(string $message = "Unauthorised")
    {
        return response([
            'success' => false,
            'message' => $message
        ], Response::HTTP_UNAUTHORIZED);
    }

    static function ofClientError(string $message = "Client Error", $errors = null)
    {
        return response([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], Response::HTTP_BAD_REQUEST);
    }

    static function ofNotFound(string $message = "Not Found")
    {
        return response([
            'success' => false,
            'message' => $message
        ], Response::HTTP_NOT_FOUND);
    }

    static function ofInternalServerError(string $message = "Internal Server Error")
    {
        return response([
            'success' => false,
            'message' => $message
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    static function ofForbidden(string $message = "Forbidden")
    {
        return response([
            'success' => false,
            'message' => $message
        ], Response::HTTP_FORBIDDEN);
    }

    static function ofMethodNotAllowed(string $message = "Method Not Allowed")
    {
        return response([
            'success' => false,
            'message' => $message
        ], Response::HTTP_METHOD_NOT_ALLOWED);
    }

}