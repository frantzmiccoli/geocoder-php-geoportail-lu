<?php

use Geocoder\IntegrationTest\BaseTestCase;

use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;

use Geocoder\Geocoder;
use FrantzMiccoli\GeoportailLu\GeoportailProvider;


class GeoportailProviderTest extends BaseTestCase
{

    protected function getCacheDir()
    {
        return __DIR__.'/.cached_responses';
    }
	
    public function testGeocodeQuery()
    {
        $geocoder = $this->getGeocoder();

        $addressText = '81 rue de bonnevoie, L-1260 Luxembourg';
        $query = GeocodeQuery::create($addressText);
        $addresses = $geocoder->geocodeQuery($query);

        $oneAddress = null;
        foreach($addresses as $address) {
            $oneAddress = $address;
        }

        $coordinates = $oneAddress->getCoordinates();
        $latitude = $coordinates->getLatitude();
        $longitude = $coordinates->getLongitude();

        $this->assertLessThan(49.7, $latitude);
        $this->assertGreaterThan(49.5, $latitude);
        $this->assertLessThan(6.14, $longitude);
        $this->assertGreaterThan(6.13, $longitude);
    }

    public function testFailingGeocodeQuery() {
        $geocoder = $this->getGeocoder();

        $addressText = '8';
        $query = GeocodeQuery::create($addressText);
        $addresses = $geocoder->geocodeQuery($query);
        $this->assertEquals(0, $addresses->count());


        $addressText = 'r';
        $query = GeocodeQuery::create($addressText);
        $addresses = $geocoder->geocodeQuery($query);
        $this->assertEquals(0, $addresses->count());
    }

    public function testReverseQuery()
    {
        $geocoder = $this->getGeocoder();

        $latitude = 49.599913792216;
        $longitude = 6.1373225376094;

        $query = ReverseQuery::fromCoordinates($latitude, $longitude);
        $addresses = $geocoder->reverseQuery($query);

        $oneAddress = null;
        foreach($addresses as $address) {
            $oneAddress = $address;
        }

        $streetName = $address->getStreetName();
        $pos = strpos($streetName, 'onnevoie');
        $this->assertNotEquals(-1, $pos);
    }

    /**
     * @return Geocoder
     */
    private function getGeocoder()
    {
        $httpClient = new \Http\Client\Curl\Client();
        $provider = new GeoportailProvider($httpClient);
        $geocoder = new \Geocoder\StatefulGeocoder($provider, 'en');
        return $geocoder;
    }
  

}
