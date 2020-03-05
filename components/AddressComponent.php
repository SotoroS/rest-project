<?php
 
namespace micro\components;
 
use Yii;
use yii\base\Component;
 
class AddressComponent extends Component {

    public function getAddress($searchText) {
        // Create query params for get info from API HERE maps
		$param = http_build_query(array(
			'apiKey' => Yii::$app->params['here_api_key'],
			'searchtext' => $searchText,
		));

		// Get info about address
		$searchResult = json_decode(file_get_contents("https://geocoder.ls.hereapi.com/6.2/geocode.json?$param"));
		
		return $searchResult->Response->View[0]->Result[0]->Location;
    }
 
}