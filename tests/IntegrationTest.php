<?php

declare(strict_types=1);

use FrantzMiccoli\GeoportailLu\GeoportailProvider;
use Geocoder\IntegrationTest\ProviderIntegrationTest;
use Http\Client\HttpClient;

class IntegrationTest extends ProviderIntegrationTest
{
    protected $testAddress = true;

    protected $testReverse = true;

    protected $testIpv4 = false;

    protected $testIpv6 = false;

    protected $skippedTests = [
        'testGeocodeQuery' => 'Geocoding an address in the UK is not supported by this provider.',
        'testReverseQueryWithNoResults' => 'The API always returns an address.',
    ];

    protected function createProvider(HttpClient $httpClient)
    {
        return new GeoportailProvider($httpClient);
    }

    protected function getCacheDir()
    {
        return __DIR__.'/.cached_responses';
    }

    protected function getApiKey()
    {
        return null;
    }
}
