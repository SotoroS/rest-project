<?php

namespace micro\controllers;

use Yii;

use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;

use micro\models\EstateObject;
use micro\models\Address;
use micro\models\Metro;

/**
 * Class SiteController
 * @package micro\controllers
 */
class ObjectController extends Controller
{
	public function behaviors()
	{
		// удаляем rateLimiter, требуется для аутентификации пользователя
		$behaviors = parent::behaviors();

		// Возвращает результаты экшенов в формате JSON  
		$behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON; 
		// OAuth 2.0
		$behaviors['authenticator'] = ['class' => HttpBearerAuth::className()];

		return $behaviors;
	}

	/**
	 * Create new object
	 *
	 * @return array|bool
	 */
	public function actionNew()
	{
        $model = new EstateObject();
	$request = Yii::$app->request;

        if ($model->load($request->post(), '')) {
    			$model->user_id = Yii::$app->user->identity->getId();
            			
			// Get address info by search address
			$infoObject = static::getAddress($model->address);

			// Find address by coordinates
			$address = Address::findByCoordinates(
				$infoObject->DisplayPosition->Latitude,
				$infoObject->DisplayPosition->Longitude
			);

			// If address no exsist create new address
			if (is_null($address)) {
				$address = new Address();

				$address->lt = $infoObject->DisplayPosition->Latitude;
				$address->lg = $infoObject->DisplayPosition->Longitude;

				$address->streetName = $infoObject->Address->Street;
				$address->cityAreaName = $infoObject->Address->District;
				$address->cityName = $infoObject->Address->City;
				$address->regionName = $infoObject->Address->County;

				// Save address & object
				if ($address->save()) {
					// Get nearby station info
					$metroInfo = static::getStation($address->lt, $address->lg);
					
					// Create metro station
					$metro = new Metro();

					$metro->name = $metroInfo->Res->Stations->Stn[0]->name;

					// Save and link metro with object if no problem with save
					if ($metro->save()) {
						$model->metro_id = $metro->id;
					}
					
					$model->ln = $address->lt;
					$model->lt = $address->lg;

					if ($model->save()) {
						return ["id" => $model->id];
					} else {
						return ["errors" => $model->errors];
					}
				} else { // Return error if not save address
					return ["error" => $address->errors];
				}
			} else { // if exist address model
				// Link address to model
				$model->address_id = $address->id;
				
				$model->ln = $address->lt;
				$model->lt = $address->lg;

				if ($model->save()) {
					return ["id" => $model->id];
				} else {
					return ["error" => $model->errors];
				}
			}
        } else {
            return ['error' => 'empty request'];
        }
	}

	/**
	 * Update object by id
	 *
	 * @return array|bool
	 */
	public function actionUpdate($id)
	{
	    $model = EstateObject::findByIdentity($id);
	    $request = Yii::$app->request->post();
		
    	    if ($model->load($request, '') && $model->update()) {
        	return ["result" => true];
    	    } else {
        	return ["error" => $model->errors];
    	    }
	}

	/**
	 * View object by id
	 *
	 * @return array|bool
	 */
	public function actionView($id)
	{
	    $model = EstateObject::findByIdentity($id);
		
    	    if (!is_null($model)) {
        	return $model;
    	    } else {
        	return ["result"=>false];
    	    }
	}

	/**
	 * Get address from HERE API by search text
	 * 
	 * @param $searchText - text search address
	 * 
	 * @return object
	 */
	private static function getAddress($searchText) {
		// Create query params for get info from API HERE maps
		$param = http_build_query(array(
			'apiKey' => Yii::$app->params['here_api_key'],
			'searchtext' => $searchText,
		));

		// Get info about address
		$searchResult = json_decode(file_get_contents("https://geocoder.ls.hereapi.com/6.2/geocode.json?$param"));
		
		return $searchResult->Response->View[0]->Result[0]->Location;
	}

	/**
	 * Get nearby station from HERE API by langitude & lantitude
	 * 
	 * @param $lt - lantitude
	 * @param $lg - langitude
	 * 
	 * @return object
	 */
	private static function getStation($lt, $lg) {
		// Create query params for get nearby station from API HERE maps
		$param = http_build_query(array(
			'apiKey' => Yii::$app->params['here_api_key'],
			'center' => $lt . ',' . $lg,
			'radius' => 500,
			'max' => 1,
		));

		// Get info about mentro by address
		$searchResult = json_decode(file_get_contents("https://transit.ls.hereapi.com/v3/stations/by_geocoord.json?$param"));
		
		return $searchResult;
	}
}