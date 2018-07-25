<?php

namespace FrantzMiccoli\GeoportailLu;


use Geocoder\Collection;
use Geocoder\Exception\InvalidServerResponse;
use Geocoder\Http\Provider\AbstractHttpProvider;
use Geocoder\Model\Address;
use Geocoder\Model\AddressBuilder;
use Geocoder\Model\AddressCollection;
use Geocoder\Provider\Provider;
use Http\Client\HttpClient;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;


class GeoportailProvider extends AbstractHttpProvider implements Provider {

    const GEOCODE_URL_TEMPLATE =
        'https://apiv3.geoportail.lu/geocode/search?queryString=%s';

    const REVERSE_GEOCODE_URL_TEMPLATE =
        'https://api.geoportail.lu/geocoder/reverseGeocode?lon=%s&lat=%s';

    /**
     * @param HttpClient $client
     */
    public function __construct(HttpClient $client)
    {
        parent::__construct($client);
    }

    /**
     * {@inheritdoc}
     */
    public function geocodeQuery(GeocodeQuery $query): Collection
    {
        $queryText = $query->getText();
        $url = sprintf(self::GEOCODE_URL_TEMPLATE, urlencode($queryText));

        $resultData = $this->executeQuery($url);

        $results = $resultData['results'] ?? [];
        $addresses = [];

        foreach ($results as $result) {
            # from what we have observed only one address is returned
            $addresses[] = $this->getAddressFromResultArray($result);
        }

        return new AddressCollection($addresses);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseQuery(ReverseQuery $query): Collection
    {
        $longitude = $query->getCoordinates()->getLongitude();
        $latitude = $query->getCoordinates()->getLatitude();
        $url = sprintf(self::REVERSE_GEOCODE_URL_TEMPLATE,
            urlencode($longitude), urlencode($latitude));

        $resultData = $this->executeQuery($url);

        $results = $resultData['results'] ?? [];
        $addresses = [];

        foreach ($results as $result) {
            # from what we have observed only one address is returned
            $addresses[] = $this->getAddressFromResultArray($result);
        }

        return new AddressCollection($addresses);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'geoportail.lu';
    }

    /**
     * @param string $url
     *
     * @return array
     */
    private function executeQuery(string $url): array
    {
        $content = $this->getUrlContents($url);
        $json = json_decode($content, true);

        if (!isset($json)) {
            throw InvalidServerResponse::create($url);
        }
        return $json;
    }

    /**
     * @param array $resultArray
     * @return Address
     */
    private function getAddressFromResultArray($resultArray): Address {
        $addressDetails = $resultArray['AddressDetails'] ?? [];
        $builder = new AddressBuilder($this->getName());

        if (array_key_exists('street', $addressDetails)) {
            $streetName = $addressDetails['street'];
            $builder->setStreetName($streetName);
        }

        if (array_key_exists('postnumber', $addressDetails)) {
            $streetNumber = $addressDetails['postnumber'];
            $builder->setStreetNumber($streetNumber);
        }

        if (array_key_exists('locality', $addressDetails)) {
            $locality = $addressDetails['locality'];
            $builder->setLocality($locality);
        }

        if (array_key_exists('zip', $addressDetails)) {
            $postalCode = $addressDetails['zip'];
            $builder->setPostalCode($postalCode);
        }

        $geomLongLat = $resultArray['geomlonlat'] ?? [];
        $geomLongLatType = $geomLongLat['type'] ?? '';

        if ($geomLongLatType == 'Point') {
            // it doesn't seem that any other type is used
            $coordinates = $geomLongLat['coordinates'];

            // note here that 7 billion people are stating latitude first ...
            $longitude = $coordinates[0];
            $latitude = $coordinates[1];
            $builder->setCoordinates($latitude, $longitude);
        }

        return $builder->build();
    }

}