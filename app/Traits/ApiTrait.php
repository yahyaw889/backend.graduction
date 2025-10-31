<?php
/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Millat <[millat.techvill@gmail.com]>
 *
 * @created 17-08-2021
 */

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiTrait
{
    /**
     * Prepare response.
     *
     * @return array
     */
    public function response($data = [], int $statusCode = Response::HTTP_OK, string $message = ''): JsonResponse
    {
        if (empty($message)) {
            $message = Response::$statusTexts[$statusCode];
        }

        return response()->json([
            
                'status' => [
                    'code' => $statusCode,
                    'message' => $message,
                ],
                'records' => $data,
            ], $statusCode);
    }

    /**
     * Success Response
     *
     * @param  array  $data
     */
    public function successResponse($data = [], int $statusCode = Response::HTTP_OK, string $message = '')
    {
        return $this->response($data, $statusCode, $message);
    }

    /**
     * Error Response
     *
     * @param  array  $errors
     */
    public function errorResponse($errors = [], int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR, string $message = '')
    {
        return $this->response($errors, $statusCode, $message);
    }

    
    public function okResponse($data = [], string $message = '')
    {
        return $this->successResponse($data, Response::HTTP_OK, $message);
    }

    /**
     * Response with status code 201.
     *
     * @param  array  $data
     */
    public function createdResponse($data = [], string $message = '')
    {
        return $this->successResponse($data, Response::HTTP_CREATED, $message);
    }

    /**
     * Response with status code 400.
     *
     * @param  array  $data
     */
    public function badRequestResponse($data = [], string $message = '')
    {
        return $this->errorResponse($data, Response::HTTP_BAD_REQUEST, $message);
    }

    /**
     * Response with status code 401.
     *
     * @param  array  $data
     */
    public function unauthorizedResponse($data = [], string $message = '')
    {
        return $this->errorResponse($data, Response::HTTP_UNAUTHORIZED, $message);
    }

    /**
     * Response with status code 403.
     *
     * @param  array  $data
     */
    public function forbiddenResponse($data = [], string $message = '')
    {
        return $this->errorResponse($data, Response::HTTP_FORBIDDEN, $message);
    }

    /**
     * Response with status code 404.
     *
     * @param  array  $data
     */
    public function notFoundResponse($data = [], string $message = '')
    {
        return $this->errorResponse($data, Response::HTTP_NOT_FOUND, $message);
    }

    /**
     * Response with status code 409.
     *
     * @param  array  $data
     */
    public function conflictResponse($data = [], string $message = '')
    {
        return $this->errorResponse($data, Response::HTTP_CONFLICT, $message);
    }

    /**
     * Response with status code 422.
     *
     * @param  array  $data
     */
    public function unprocessableResponse($data = [], string $message = '')
    {
        return $this->errorResponse($data, Response::HTTP_UNPROCESSABLE_ENTITY, $message);
    }

    /**
     * Response with status code 405.
     *
     * @param  array  $data
     */
    public function methodNotAllowedResponse($data = [], string $message = '')
    {
        return $this->errorResponse($data, Response::HTTP_METHOD_NOT_ALLOWED, $message);
    }

    /**
     * Response with status code 503.
     *
     * @param  array  $data
     */
    public function serviceUnavailableResponse($data = [], string $message = '')
    {
        return $this->errorResponse($data, Response::HTTP_SERVICE_UNAVAILABLE, $message);
    }
}
