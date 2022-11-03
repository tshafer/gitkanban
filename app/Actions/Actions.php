<?php

namespace App\Actions;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Actions\Concerns\AsController;

abstract class Actions
{
    use AsAction;
    use AsController;

    /**
     * Get the middleware the action needs.
     *
     * @return array
     */
    public function getControllerMiddleware(): array
    {
        $middleware = ['auth:sanctum', 'api'];

        // if (! app()->runningUnitTests()) {
        //     $middleware[] = 'verified';
        // }

        return $middleware;
    }

    /**
     * Return the validator failure messages.
     *
     * @param  Validator  $validator The validator
     * @return void
     *
     * @throws HttpResponseException
     */
    public function getValidationFailure(Validator $validator): void
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Throw an exception if the action is not authorized.
     *
     * @param  mixed  $message
     * @return void
     *
     * @throws HttpResponseException
     */
    public function unauthorizedResponse(string $message = 'You are not authorized to perform this action.')
    {
        throw new HttpResponseException(
            response()->json(['unauthorized' => $message], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
