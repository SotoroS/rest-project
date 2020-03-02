<?php

namespace micro\controllers;

use Yii;

use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;

use micro\models\RequestAddress;
use micro\models\FilterAddress;
use micro\models\RequestObject;
use micro\models\Address;
use micro\models\User;
use micro\models\Filter;
use micro\models\City;

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

		// Возвращает результаты экшенов в формате JSON  
		$behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON; 
		// OAuth 2.0
		$behaviors['authenticator'] = ['class' => HttpBearerAuth::className()];

		return $behaviors;
	}

	public function actionSetFilter()
	{
		$request = Yii::$app->request;
		$output = [];

		try {
			$fcmToken = $request->post('fcmToken');
			$cityId = (int)$request->post('city') ?: null;
            $cityAreaId = (int)$request->post('city_area_id') ?: null;
			$notification = (int)$request->post('push_notification') ?: null;
			
			$rentType = json_decode($request->post('rent_type')) ?: null;
			$propertyType = json_decode($request->post('property_type')) ?: null;
			
			// $propertyType = implode(',', $propertyType);
			// $rentType = implode(',', $rentType);

			$requestData = [
                'city' => $cityId,
                'city_area_id' => $cityAreaId,
                'rent_type' => $rentType,
                'property_type' => $propertyType,
                'price_from' => (int)$request->post('price_from') ?: 0,
                'price_to' => (int)$request->post('price_to') ?: 500000000,
                'substring' => $request->post('substring') ?: "",
            ];

			// get current user
			// $user = Yii::$app->user;
			// $user = Users::findOne(Yii::$app->users->identity->id);
			$user = User::findOne(1);

			// current user filter
			$filterObject = Filter::findOne(['user_id' => $user->id]);
			$user->notifications = $request->post('push_enabled') ? 1 : 0;
			
			// if there is a fcmToken, fill in the user
            if ($fcmToken) {
                $user->fcmToken = $fcmToken;
            }
            $user->save();

            if (is_null($filterObject)) {
                $filterObject = new Filter();
                $filterObject->user_id = $user->id;
            }

            $user->notifications = $notification;
			$user->save();
			
			// fill in the data
			$filterObject->rent_type = $rentType ?: null;
            $filterObject->property_type = $propertyType ?: null;
            $filterObject->city_area_id = $cityAreaId ?: NULL;
            $filterObject->city_id = $cityId ?: NULL;
            $filterObject->price_from = $requestData['price_from'];
            $filterObject->price_to = $requestData['price_to'];
            $filterObject->substring = $requestData['substring'];
            $filterObject->save();
			
            $output['cities'] = City::find()->asArray()->all();
			return $output;

		} catch (\Exception $e) {
            $output['error'] = "Invalid data provided;";
			$this->_writeLog($e);
			
        } catch (\Throwable $e) {
            $this->_writeLog($e);
			$output['error'] = $e->getMessage();
			
        } finally {
            $output['status'] = true;
            return $output;
        }
	}

	/**
	 * Create new object
	 *
	 * @return array|bool
	 */
	public function actionNew()
	{
		$model = new Filter();
		$request = Yii::$app->request->post();
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
						return ["error" => $address->errors];
					}
				} else { // if exist address model
					// Add id address after save in array od ids
					array_push($addressIds, $address->id);
				}
			}
	
			if ($model->save()) {
				// Create rows in request_address table
				foreach ($addressIds as $addressId) {
					// $requestAdrress = new RequestAddress();
					$requestAdrress = new FilterAddress();

					$requestAdrress->address_id = $addressId;
					$requestAdrress->request_object_id = $model->id;

					if (!$requestAdrress->save()) {
						return ["errors" => $requestAdrress->errors];
					}
				}

				return ["result" => true];
			} else {
				return ["errors" => $model->errors];
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
		// $model = RequestObject::findByIdentity($id);
		$model = Filter::findByIdentity($id);
		$request = Yii::$app->request->post();
		
        if ($model->load($request, '') && $model->update()) {
            return ["result" => true];
        } else {
            return ["errors" => $model->errors];
        }
	}

	/**
	 * View object by id
	 *
	 * @return array|bool
	 */
	public function actionView($id)
	{
		// $model = RequestObject::findByIdentity($id);
		$model = Filter::findByIdentity($id);
		
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
     * @param $e
     */
    protected function _writeLog($e)
    {
        $f = fopen("log.txt", 'a');
        fwrite($f, "[ERR] {$e->getMessage()}");
        fclose($f);
    }
}