<?php

namespace micro\controllers;

use Yii;
use yii\web\Controller;
use app\models\RequestObject;
use app\models\RequestAddress;
use app\models\Address;

/**
 * Class SiteController
 * @package micro\controllers
 */
class RequestController extends Controller
{
	public function behaviors()
	{
		// удаляем rateLimiter, требуется для аутентификации пользователя
		$behaviors = parent::behaviors();

		return $behaviors;
	}

	/**
	 * Function executing before all action
	 *
	 * - set json format for response
	 *
	 * @param $action
	 * @return bool
	 * @throws \yii\web\BadRequestHttpException
	 */
	public function beforeAction($action)
	{
		if (parent::beforeAction($action)) {
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Create new object
	 *
	 * @return array|bool
	 */
	public function actionNew()
	{
		$model = new RequestObject();
		$request = Yii::$app->request->get();
		$addressIds = [];

        if ($model->load($request, '')) {
			$model->user_id = Yii::$app->user->identity->id;

			foreach ($model->addresses as $address) {
				// Get address info by search address
				$infoObject = static::getAddress($address);

				// Find address by coordinates
				$address = Address::findByCoordinates(
					$infoObject->DisplayPosition->Latitude,
					$infoObject->DisplayPosition->Longitude
				);

				// If address no exsist create new address
				if (!is_null($address)) {
					$address = new Address();

					$address->lt = $infoObject->DisplayPosition->Latitude;
					$address->lg = $infoObject->DisplayPosition->Longitude;

					$address->streetName = $infoObject->Address->Street;
					$address->cityAreaName = $infoObject->Address->District;
					$address->cityName = $infoObject->Address->City;
					$address->regionName = $infoObject->Address->County;

					// Save address & object
					if ($address->save()) {
						// Add id address after save in array od ids
						array_push($addressIds, $address->id);
					} else { // Return error if not save address
						return $address->errors;
					}
				} else { // if exist address model
					// Add id address after save in array od ids
					array_push($addressIds, $address->id);
				}
			}
 
			if ($model->save()) {
				// Create rows in request_address table
				foreach ($addressIds as $addressId) {
					$requestAdrress = new RequestAddress();

					$requestAdrress->address_id = $addressId;
					$requestAdrress->request_object_id = $model->id;

					if (!$requestAdrress->save()) {
						return $requestAdrress->errors;
					}
				}

				return true;
			} else {
				return $model->errors;
			}
        } else {
            return [
				'erorr' => 'empty request'
			];
        }
	}

	/**
	 * Update object by id
	 *
	 * @return array|bool
	 */
	public function actionUpdate($id)
	{
		$model = RequestObject::findByIdentity($id);
		$request = Yii::$app->request->get();
		
        if ($model->load($request, '') && $model->update()) {
            return true;
        } else {
            return $model->errors;
        }
	}

	/**
	 * View object by id
	 *
	 * @return array|bool
	 */
	public function actionView($id)
	{
		$model = RequestObject::findByIdentity($id);
		
        if (!is_null($model)) {
            return $model;
        } else {
            return false;
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
}