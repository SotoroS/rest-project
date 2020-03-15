<?php

namespace micro\components;

use Yii;
use yii\base\Component;

use \yii\base\Exception as Exception;

/**
 * HereMapsComponent
 * 
 * Componet for work with API Here Maps
 * 
 * @author Fokin Danila
 */
class HereMapsComponent extends Component
{
	/**
	 * Api key for eork with API Here Maps
	 * 
	 * @var string 
	 */
	public $apiKey;	

	/**
	 * Get address by search text
	 * 
	 * @param mixed $searchText
	 * 
	 * More info - https://developer.here.com/documentation/examples/rest/geocoder
	 * 
	 * @return object
	 */
	public function findAddressByText($searchText)
	{
		if (is_null($this->apiKey)) {
			throw new Exception("Here Maps api key not set");
		}

		// Create query params for get info from API HERE maps
		$param = http_build_query(array(
			'apiKey' => $this->apiKey,
			'searchtext' => $searchText,
		));

		// Get info about address
		$searchResult = json_decode(file_get_contents("https://geocoder.ls.hereapi.com/6.2/geocode.json?$param"));

		return $searchResult->Response;
	}

	/**
	 * Find station nearby station by geo-location
	 * 
	 * @param float $lt lantitude of center point search
	 * @param float $lg longitude of center point search
	 * @param int $radius radius of search in meters
	 * 
	 * More info - https://developer.here.com/documentation/examples/rest/public_transit/station-search-proximity
	 * 
	 * @return object
	 */
	public function findStatationsNearby(float $lt, float $lg, int $radius = 500)
	{
		if (is_null($this->apiKey)) {
			throw new Exception("Here Maps api key not set");
		}

		// Create query params for get nearby station from API HERE maps
		$param = http_build_query(array(
			'apiKey' => $this->apiKey,
			'center' => $lt . ',' . $lg,
			'radius' => $radius,
			'max' => 1,
		));

		// Get info about mentro by address
		$searchResult = json_decode(file_get_contents("https://transit.ls.hereapi.com/v3/stations/by_geocoord.json?$param"));

		return $searchResult->Res;
	}
}
