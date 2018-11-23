<?php

declare(strict_types=1);

use FrantzMiccoli\GeoportailLu\GeoportailProvider;
use Geocoder\IntegrationTest\BaseTestCase;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;

class GeoportailProviderTest extends BaseTestCase
{
    protected function getCacheDir()
    {
        return __DIR__.'/.cached_responses';
    }

    public function testGeocodeQuery()
    {
        $provider = new GeoportailProvider($this->getHttpClient());
        $results = $provider->geocodeQuery(GeocodeQuery::create('81 rue de bonnevoie, L-1260 Luxembourg'));

        $this->assertInstanceOf('\Geocoder\Model\AddressCollection', $results);
        $this->assertCount(1, $results);

        /** @var \Geocoder\Model\Address $result */
        $result = $results->first();
        $this->assertInstanceOf('\Geocoder\Model\Address', $result);
        $this->assertEquals(49.599913, $result->getCoordinates()->getLatitude(), '', 0.00001);
        $this->assertEquals(6.137322, $result->getCoordinates()->getLongitude(), '', 0.00001);
        $this->assertEquals('81', $result->getStreetNumber());
        $this->assertEquals('Rue de Bonnevoie', $result->getStreetName());
        $this->assertEquals('1260', $result->getPostalCode());
        $this->assertEquals('Luxembourg', $result->getLocality());
    }

    public function testReverseQuery()
    {
        $provider = new GeoportailProvider($this->getHttpClient());
        $results = $provider->reverseQuery(ReverseQuery::fromCoordinates(49.599913792216, 6.1373225376094));

        $this->assertInstanceOf('\Geocoder\Model\AddressCollection', $results);
        $this->assertCount(1, $results);

        /** @var \Geocoder\Location $result */
        $result = $results->first();
        $this->assertInstanceOf('\Geocoder\Model\Address', $result);
        $this->assertEquals(49.599913, $result->getCoordinates()->getLatitude(), '', 0.00001);
        $this->assertEquals(6.137322, $result->getCoordinates()->getLongitude(), '', 0.00001);
        $this->assertEquals('81', $result->getStreetNumber());
        $this->assertEquals('Rue de Bonnevoie', $result->getStreetName());
        $this->assertEquals('1260', $result->getPostalCode());
        $this->assertEquals('Luxembourg', $result->getLocality());
    }
}
