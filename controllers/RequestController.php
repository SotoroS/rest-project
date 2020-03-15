<?php

declare(strict_types=1);

namespace micro\controllers;

use Yii;

use yii\base\Exception;
use yii\rest\Controller;
use yii\web\Response;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;

use micro\models\FilterAddress;
use micro\models\Address;
use micro\models\User;
use micro\models\Filter;
use micro\models\RequestType;
use micro\models\City;
use yii\web\ForbiddenHttpException;

/**
 * Class RequestController
 */
class RequestController extends Controller
{
	/**
	 * @return void
	 */
	public function behaviors()
	{
		$behaviors = parent::behaviors();

		$behaviors['access'] = [
			'class' => AccessControl::className(),
			'only' => ['set-filter', 'filter-new', 'update', 'view'],
			'rules' => [
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
				'set-filter' => ['post'],
				'filter-new' => ['post'],
				'update' => ['post'],
				'view' => ['get'],
			],
		];

		// Set JSON format for response  
		$behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

		// OAuth 2.0
		$behaviors['authenticator'] = [
			'except' => [],
			'class' => HttpBearerAuth::className()
		];

		return $behaviors;
	}

	/**
	 * Set filter
	 *
	 * @return array
	 */
	public function actionSetFilter(): array
	{
		$request = Yii::$app->request;
		$output = [];

		try {
			$user = User::findOne(Yii::$app->user->identity->id);

			$user->notifications = (int) $request->post('push_notification') ?: 0;
			$user->fcmToken = $request->post('fcmToken') ?: $user->fcmToken;

			if (!$user->save()) {
				throw new Exception('Cann\'t save user model');
			}

			$filterObject = Filter::findOne(['user_id' => $user->id]);

			// If not exist filter object - create new
			if (is_null($filterObject)) {
				$filterObject = new Filter();
				
				$filterObject->user_id = $user->id;
			}

			// Fill filter object model request data
			$filterObject->rent_type = $request->post('rent_type') ?: null;
			$filterObject->property_type = $request->post('property_type') ?: null;
			$filterObject->request_type_id = (int) $request->post('request_type_id') ?: 1;
			$filterObject->city_area_id = (int) $request->post('city_area_id') ?: 1;
			$filterObject->city_id = (int) $request->post('city_id') ?: 1;
			$filterObject->price_from = (int) $request->post('price_from') ?: 0;
			$filterObject->price_to = (int) $request->post('price_to') ?: 500000000;
			$filterObject->substring = $request->post('substring') ?: "";

			if (!$filterObject->save()) {
				throw new Exception('Cann\'t save filter object.');
			}

			$output['cities'] = City::find()->asArray()->all();
			$output['setFilter'] = $filterObject;

			return $output;
		} catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return [
                'error' => $e->getMessage()
            ];
		}

		$output['status'] = true;

		return $output;
	}

	/**
	 * Create new filter
	 *
	 * @return array
	 */
	public function actionNewFilter(): array
	{
		$model = new Filter();
		
		$request = Yii::$app->request->post();

		$addressIds = [];

		try {
			if ($model->load($request, '')) {
				$model->user_id = Yii::$app->user->identity->id;

				foreach ($model->addresses as $searchText) {
					$addressIds[] = $this->_getAddressBySearchText($searchText);
				}

				// проверка на наличие requestName, если есть ищем среди возможных, если не находим - создаем новый. 
				// Если requestName отсутствует записываем request_type_id = default 1
				if (!is_null($model->requestName)) {
					$request_type = RequestType::findByName($model->requestName);

					if (is_null($request_type)) {
						$request_type = new RequestType();
						$request_type->name = $model->requestName;

						if (!$request_type->save()) {
							throw new Exception('Request type save failed');
						}
					}

					$model->request_type_id = $request_type->id;
				} else {
					$model->request_type_id = 1;
				}

				if ($model->save()) {
					// Create rows in request_address table
					foreach ($addressIds as $addressId) {
						// $requestAdrress = new RequestAddress();
						$filterAddress = new FilterAddress();

						$filterAddress->address_id = $addressId;
						$filterAddress->filters_id = $model->id;

						if (!$filterAddress->save()) {
							throw new Exception('Cann\'t save FilterAddress model');
						}
					}

					return [
						"result" => true
					];
				} else {
					throw new Exception('Cann\'t save filter model');
				}
			} else {
				throw new Exception('Empty request');
			}
		} catch (Exception $e) {
			Yii::error($e->getMessage(), __METHOD__);

			return [
				'error' => $e->getMessage()
			];
		}
	}

	/**
	 * Update object by id
	 *
	 * @return array
	 */
	public function actionUpdate($id): array
	{
		$model = Filter::findByIdentity($id);
	
		if ($model->user_id != Yii::$app->user->identity->id) {
			throw new ForbiddenHttpException();
		}
	
		$request = Yii::$app->request->post();

		try {
			if ($model->load($request, '') && $model->update()) {
				return [
					"result" => true
				];
			} else if (empty($request)) {
				throw new Exception('Not all data entered');
			} else {
				throw new Exception('Cann\'t update filter model');
			}
		} catch (Exception $e) {
			Yii::error($e->getMessage(), __METHOD__);

			return [
				'error' => $e->getMessage()
			];
		}
	}

	/**
	 * View object by id
	 *
	 * @return array
	 */
	public function actionView($id): array
	{
		$model = Filter::findByIdentity($id);

		try {
			if (!is_null($model)) {
				return $model->toArray();
			} else {
				throw new Exception('Filter not found');
			}
		} catch (Exception $e) {
			Yii::error($e->getMessage(), __METHOD__);

			return [
				'error' => $e->getMessage()
			];
		}
	}

	/**
	 * Get address by search text
	 * 
	 * @param string $searchText
	 * 
	 * @return int id addresse's 
	 */
	private function _getAddressBySearchText(string $searchText) {
		if ($searchText == "") {
			throw new Exception("Search text is empty");
		}

		// Get address info by search text
		$infoObject = Yii::$app->hereMaps->findAddressByText($searchText)->View[0]->Result[0]->Location;

		// Find address by coordinates
		$address = Address::findByCoordinates(
			$infoObject->DisplayPosition->Latitude,
			$infoObject->DisplayPosition->Longitude
		);

		// If address not exsist - create new address
		if (is_null($address)) {
			$address = new Address();

			$address->lt = $infoObject->DisplayPosition->Latitude;
			$address->lg = $infoObject->DisplayPosition->Longitude;

			$address->streetName = $infoObject->Address->Street;
			$address->cityAreaName = $infoObject->Address->District;
			$address->cityName = $infoObject->Address->City;
			$address->regionName = $infoObject->Address->County;

			// Save address and add to array of address ids
			if ($address->save()) {
				return $address->id;
			} else {
				throw new Exception('Cann\'t save address');
			}
		} else {
			return $address->id;
		}
	}
}
