<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseHelpers
{
    /**
     * Return not found response.
     *
     * @param  string  $message
     * @return JsonResponse
     */
    public function respondNotFound(string|Exception $message, ?string $key = 'error'): JsonResponse
    {
        $message = $message instanceof Exception ? $message->getMessage() : $message;

        return $this->apiResponse([$key => $message], Response::HTTP_NOT_FOUND);
    }

    /**
     * Api Response.
     *
     * @param  mixed  $data
     * @param  int  $code
     * @return JsonResponse
     */
    private function apiResponse(mixed $data, int $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }

    /**
     * Reponse OK
     *
     * @param  mixed  $message
     * @return JsonResponse
     */
    public function respondOk(mixed $message): JsonResponse
    {
        return $this->respondWithSuccess(['success' => $message]);
    }

    /**
     * Response with success message.
     *
     * @param  mixed  $contents
     * @return JsonResponse
     */
    public function respondWithSuccess(mixed $contents): JsonResponse
    {
        $data = [] === $contents ? ['success' => true] : $contents;

        return $this->apiResponse($data);
    }

    /**
     * Respond unathenticated
     *
     * @param  string  $message
     * @return JsonResponse
     */
    public function respondUnAuthenticated(?string $message = null): JsonResponse
    {
        return $this->apiResponse(
            ['error' => $message ?? 'Unauthenticated'],
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Respond with forbidden response.
     *
     * @param  string  $message
     * @return JsonResponse
     */
    public function respondForbidden(?string $message = null): JsonResponse
    {
        return $this->apiResponse(['error' => $message ?? 'Forbidden'], Response::HTTP_FORBIDDEN);
    }

    /**
     * Respond with error message.
     *
     * @param  string  $message
     * @return JsonResponse
     */
    public function respondError(?string $message = null): JsonResponse
    {
        return $this->apiResponse(['error' => $message ?? 'Error'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Respond with created response.
     *
     * @param  array  $data
     * @return JsonResponse
     */
    public function respondCreated(?array $data = []): JsonResponse
    {
        return $this->apiResponse($data, Response::HTTP_CREATED);
    }

    /**
     * Respond with failed validation.
     *
     * @param  string|Exception  $message
     * @return string $key
     * @return JsonResponse
     */
    public function respondFailedValidation(
        string|Exception $message,
        ?string $key = 'message'
    ): JsonResponse {
        $message = $message instanceof Exception ? $message->getMessage() : $message;

        return $this->apiResponse(
            [$key => filled($message) ? $message : 'Validation errors'],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * Response with teapot.
     *
     * @return JsonResponse
     */
    public function respondTeapot(): JsonResponse
    {
        return $this->apiResponse(['message' => 'I\'m a teapot'], Response::HTTP_I_AM_A_TEAPOT);
    }

    /**
     * Respond with no content.
     *
     * @param  array|null  $data

     * @return JsonResponse
     */
    public function respondNoContent(?array $data = []): JsonResponse
    {
        return $this->apiResponse($data, Response::HTTP_NO_CONTENT);
    }
}
