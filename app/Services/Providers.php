<?php

namespace App\Services;

use App\Services\Providers\DigitalOcean;
use App\Services\Providers\Linode;
use App\Services\Providers\Vultr;
use Exception;

class Providers
{
    public object $api;

    /**
     * @return Exception|void
     *
     * @throws Exception
     */
    public function __construct(string $provider)
    {
        if (! $provider) {
            return new Exception('Please provide a provider');
        }
        switch (strtolower($provider)) {
            case 'linode':
                $this->api = new Linode(config('services.providers.linode'));
                break;
            case 'ocean2':
                $this->api = new DigitalOcean(config('services.providers.digital_ocean'));
                break;
            case 'vultr':
                $this->api = new Vultr(config('services.providers.vultr'));
                break;
        }
    }

    /**
     * Return the plans for the selected provider.
     */
    public function plans(): mixed
    {
        return $this->api->formattedPlans();
    }

    /**
     * Return the regions for the provided plan.
     */
    public function regions(string $plan = null): mixed
    {
        return $this->api->formattedRegions($plan);
    }

    /**
     * Return the regions for the provided plan.
     */
    public function support(string $region, string $plan = null): mixed
    {
        return $this->api->support($region, $plan);
    }
}
