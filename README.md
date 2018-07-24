[![Build Status](https://secure.travis-ci.org/frantzmiccoli/geocoder-php-geoportail-lu.png)](http://travis-ci.org/frantzmiccoli/geocoder-php-geoportail-lu)

This project is a provider for 
[Geocoder-PHP](https://github.com/geocoder-php/Geocoder) to 
enable the consumption of the geoportail.lu API detailed in the
[Geoportail documentation](https://wiki.geoportail.lu/doku.php?id=en:api:rest).
 
The API always returns an address, I recommend that you double check as 
[some values give strange results](http://apiv3.geoportail.lu/geocode/search?queryString=rue%20de%20bonnevoie).