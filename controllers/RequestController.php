<?php
//Строгая типизация
declare(strict_types=1);

namespace micro\controllers;

use Yii;

use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use micro\models\FilterAddress;
use micro\models\Address;
use micro\models\User;
use micro\models\Filter;
use micro\models\RequestType;
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

		$behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['set-filter', 'filter-new', 'update', 'view'],
            'rules' => [
                [
                    'actions' => ['set-filter'],
                	'allow' => true,
                	'roles' => ['?'],
                ],
                [
                    'actions' => ['set-filter', 'filter-new', 'update', 'view'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
			],
		];
		$behaviors['verbs'] = [
			'class' => VerbFilter::className(),
			'actions' => [
				'set-filter'  => ['post'],
				'filter-new'   => ['post'],
				'update' => ['post'],
				'view' => ['get'],
			],
		];
		
		// Возвращает результаты экшенов в формате JSON  
		$behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON; 
		// OAuth 2.0
		$behaviors['authenticator'] = [
			'except' => ['set-filter'],
			'class' => HttpBearerAuth::className()
		];

		return $behaviors;
	}

	public function actionSetFilter(): array
	{
		$request = Yii::$app->request;
		$output = [];

		try {
			$fcmToken = $request->post('fcmToken');

			$cityId = (int)$request->post('city') ?: 1;
            $cityAreaId = (int)$request->post('city_area_id') ?: 1;
            $request_type_id = (int)$request->post('request_type_id') ?: 1;
			$notification = (int)$request->post('push_notification') ?: null;
			
			$rentType = $request->post('rent_type') ?: null;
			$propertyType = $request->post('property_type') ?: null;
			
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
			$user = User::findOne(Yii::$app->user->identity->id);

			// current user filter
			$filterObject = Filter::findOne(['user_id' => $user->id]);
			$user->notifications = $request->post('push_enabled') ? 1 : 0;

			
			// if there is a fcmToken, fill in the user
            if ($fcmToken) {
                $user->fcmToken = $fcmToken;
            }

            if (is_null($filterObject)) {
                $filterObject = new Filter();
                $filterObject->user_id = $user->id;
            }

            $user->notifications = $notification;
			$user->save();

			// fill in the data
			$filterObject->rent_type = $rentType;
            $filterObject->property_type = $propertyType;
            $filterObject->request_type_id = $request_type_id;
            $filterObject->city_area_id = $cityAreaId;
            $filterObject->city_id = $cityId;
            $filterObject->price_from = $requestData['price_from'];
            $filterObject->price_to = $requestData['price_to'];
            $filterObject->substring = $requestData['substring'];
            $filterObject->save();
			
			$output['cities'] = City::find()->asArray()->all();
			
			// Log
			Yii::info("Get Address Success" ,__METHOD__);

			return $output;

		} catch (\Exception $e) {
            $output['error'] = "Invalid data provided;";
			// Log
			Yii::error($e->getMessage() ,__METHOD__);
			
        } catch (\Throwable $e) {
            // Log
			Yii::error($e->getMessage() ,__METHOD__);

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
	public function actionFilterNew(): array
	{
		$model = new Filter();
		$request = Yii::$app->request->post();
		$addressIds = [];

		try {
			if ($model->load($request, '')) {
				$model->user_id = Yii::$app->user->identity->id;

				// return $model->addresses;
				foreach ($model->addresses as $address) {

					// Get address info by search address
					$infoObject = static::getAddress($address);

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
							// Add id address after save in array od ids
							array_push($addressIds, $address->id);
						} else { // Return error if not save address
							throw new Exception('Address Save Failed');
						}
					} else { // if exist address model
						// Add id address after save in array od ids
						array_push($addressIds, $address->id);
					}

				}
				
				// проверка на наличие requestName, если есть ищем среди возможных, если не находим - создаем новый. 
				// Если requestName отсутствует записываем request_type_id = default 1
				if (!is_null($model->requestName)) {
					$request_type = RequestType::findByName($model->requestName);

					if (is_null($request_type)) {

						$request_type = new RequestType();
						$request_type->name = $model->requestName;
						
						if (!$request_type->save()) {
							throw new Exception('Request_type Save Failed');
						}
					}
					$model->request_type_id = $request_type->id;

				} else {
					return ["error" => "RequestName field is empty"];
				}
				
				if ($model->save()) {
					// Create rows in request_address table
					foreach ($addressIds as $addressId) {
						// $requestAdrress = new RequestAddress();
						$filterAddress = new FilterAddress();

						$filterAddress->address_id = $addressId;
						$filterAddress->filters_id = $model->id;

						if (!$filterAddress->save()) {
							throw new Exception('FilterAddress Save Failed');
						}
					}
					// Log
					Yii::info("Save Filter" ,__METHOD__);

					return [
						"result" => true
					];
				} else {
					throw new Exception('Filter Save Failed');
				}
			} else {
				// Log
				Yii::error('empty request' ,__METHOD__);

				return [
					'error' => 'empty request'
				];
			}
		} catch(Exception $e) {
			// Log
			Yii::error($e->getMessage() ,__METHOD__);

			return [
				'error' => $e->getMessage();
			]
		}
	}

	/**
	 * Update object by id
	 *
	 * @return array|bool
	 */
	public function actionUpdate($id): array
	{
		// $model = RequestObject::findByIdentity($id);
		$model = Filter::findByIdentity($id);
		$request = Yii::$app->request->post();

		try {
			if ($model->load($request, '') && $model->update()) {
				// Log
				Yii::info('Save Filter' ,__METHOD__);

				return [
					"result" => true
				];
			} elseif (empty($request)) {
				// Log
				Yii::error('Not all data entered' ,__METHOD__);

				return [
					'error'=>'Not all data entered'
				];
			} else {
				throw new Exception('Filter Update Failed');
			}
		} catch(Exception $e) {
			// Log
			Yii::error($e->getMessage() ,__METHOD__);

			return [
				'error' => $e->getMessage()
			];
		}
	}

	/**
	 * View object by id
	 *
	 * @return array|bool
	 */
	public function actionView($id): array
	{
		$model = Filter::findByIdentity($id);
        try {
			if (!is_null($model)) {
				// Log
				Yii::error("Get Filter" ,__METHOD__);

				return $model->toArray();
			} else {
				// Log
				Yii::error("Filter Not Found" ,__METHOD__);

				return [
					'error'=>"Filter Not Found"
				];
			}
		} catch(Exception $e) {
			// Log
			Yii::error($e->getMessage() ,__METHOD__);

			return [
				'error' => $e->getMessage()
			];
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
		
		if (is_null($searchResult)) {
			// Log
			Yii::error("Get Address Failed" ,__METHOD__);

			return false;
		} else {
			// Log
			Yii::info("Get Address Success" ,__METHOD__);

			return $searchResult->Response->View[0]->Result[0]->Location;
		}
	}
} 