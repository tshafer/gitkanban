<?php

namespace App\Services;

use Stripe\Exception\ApiErrorException;
use Stripe\PaymentMethod as StripePaymentMethod;

class Stripe
{
    /**
     * Verify that the given token origin country matches the given country.
     *
     *
     * @throws ApiErrorException
     */
    public function tokenIsForCountry(string $token, string $country): bool
    {
        return $this->countryForToken($token) === $country;
    }

    /**
     * Get the country code for the given Stripe token.
     *
     *
     * @throws ApiErrorException
     */
    public function countryForToken(string $token): string
    {
        return StripePaymentMethod::retrieve($token, config('cashier.secret'))->card->country;
    }
}
