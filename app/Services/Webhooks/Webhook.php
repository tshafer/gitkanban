<?php

namespace App\Services\Webhooks;

use Illuminate\Http\Request;

/**
 * Generic Webhook class.
 */
abstract class Webhook
{
    /**
     * Webhook constructor.
     */
    public function __construct(protected Request $request)
    {
    }

    /**
     * Determines whether the request is from a particular service.
     */
    abstract public function isRequestOrigin(): bool;

    /**
     * Parses the request for a push webhook body.
     *
     * @return mixed Either an array of parameters for the deployment config, or false if it is invalid.
     */
    abstract public function handlePush(): mixed;
}
