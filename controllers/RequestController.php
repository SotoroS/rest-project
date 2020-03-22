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
use micro\models\RentType;
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

		$behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

		$behaviors['authenticator'] = [
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
				return [
					'error' => $user->errors
				];
			}
			$filterObject = Filter::findOne(['user_id' => $user->id]);

			if (is_null($filterObject)) {
				$filterObject = new Filter();

				$filterObject->user_id = $user->id;
			}

			$filterObject->rentTypeIds = $request->post('rent_type') ? json_decode($request->post('rent_type')) : [];
			$filterObject->propertyTypeIds = $request->post('property_type') ? json_decode($request->post('property_type')) : [];
			
			$filterObject->city_area_id = (int) $request->post('city_area_id') ?: 1;
			$filterObject->city_id = (int) $request->post('city_id') ?: 1;
			$filterObject->price_from = (int) $request->post('price_from') ?: 0;
			$filterObject->price_to = (int) $request->post('price_to') ?: 500000000;

			$filterObject->substring = $request->post('substring') ?: "";

			if (!$filterObject->save()) {
				return [
					'error' => $filterObject->errors
				];
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
	}

	/**
	 * Create new filter
	 *
	 * @return array
	 */
	public function actionNewFilter(): array
	{
		$model = new Filter();

		$request = Yii::$app->request;

		$addressIds = [];
		try {
			if ($model->loadOnlyAttributes($request->post())) {
				$model->user_id = Yii::$app->user->identity->id;
				$model->rentTypeIds = $request->post('rent_type') ? json_decode($request->post('rent_type')) : [];
				$model->propertyTypeIds = $request->post('property_type') ? json_decode($request->post('property_type')) : [];
				
				foreach ($model->addresses as $searchText) {
					$addressIds[] = $this->_getAddressBySearchText($searchText);
				}
				
				// проверка на наличие rentTypeIds, если есть ищем среди возможных, если не находим - выдаём ошибку. 
				// Если rentType отсутствует, записываем rent_type_id = default 1
				if (!is_null($model->rentTypeIds)) {
					foreach($model->rentTypeIds as $id) {
						$rent_type = RentType::findById($id);
						if (!is_null($rent_type)) {
							$rent_type = null;
						}
						else
						{
							throw new Exception('Can\'t find rent type with id ' . $id . '.');
						}
					}
				}
				else {
					$model->rent_type = ['1'];
					$model->rentTypeIds = json_decode($model->rent_type);
				}

				// 	if (is_null($request_type)) {
				// 		$request_type = new RequestType();
				// 		$request_type->name = $model->requestName;

				// 		if (!$request_type->save()) {
				// 			throw new Exception('Request type save failed');
				// 		}
				// 	}

				// 	$model->request_type_id = $request_type->id;
				// } else {
				// 	$model->request_type_id = 1;
				// }

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
						'result' => true
					];
				} else {
					return [
						'error' => $model->errors
					];
				}
			} else {
				throw new Exception('Empty request or wrong attributes.');
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
		$model = Filter::findOne($id);

		if ($model->user_id != Yii::$app->user->identity->id) {
			throw new ForbiddenHttpException();
		}

		$request = Yii::$app->request;

		try {
			if ($model->load($request->post(), '')) {
				$model->rentTypeIds = $request->post('rent_type') ? json_decode($request->post('rent_type')) : [];
				$model->propertyTypeIds = $request->post('property_type') ? json_decode($request->post('property_type')) : [];
				if ($model->update()) {
					return [
						"result" => true
					];
				}
				else {
					throw new Exception('Can\'t update filter.');
				}
			} else if (empty($request->post())) {
				throw new Exception('Not all data entered');
			} else {
				return [
					"error" => $model->errors
				];
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
	private function _getAddressBySearchText(string $searchText)
	{
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

			if (!isset($infoObject->Address->Street)) {
				throw new Exception('Bad address');
			}

			if (!isset($infoObject->Address->District)) {
				throw new Exception('Bad address');
			}

			if (!isset($infoObject->Address->City)) {
				throw new Exception('Bad address');
			}

			if (!isset($infoObject->Address->County)) {
				throw new Exception('Bad address');
			}

			$address->streetName = $infoObject->Address->Street;
			$address->cityAreaName = $infoObject->Address->District;
			$address->cityName = $infoObject->Address->City;
			$address->regionName = $infoObject->Address->County;

			// Save address and add to array of address ids
			if ($address->save()) {
				return $address->id;
			} else {
				return [
					'error' => $address->errors
				];
			}
		} else {
			return $address->id;
		}
	}
}
