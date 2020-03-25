<?php

declare(strict_types=1);

namespace micro\controllers;

use Yii;

use \Datetime;

use yii\base\Exception;
use yii\rest\Controller;

use yii\web\Response;
use yii\web\ForbiddenHttpException;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;

use micro\models\FilterAddress;
use micro\models\Address;
use micro\models\User;
use micro\models\Filter;
use micro\models\City;

/**
 * Class RequestController
 */
class RequestController extends Controller
{
	/**
	 * Returns a list of behaviors that this component should behave as.
	 *
	 * Child classes may override this method to specify the behaviors they want to behave as.
	 *
	 * The return value of this method should be an array of behavior objects or configurations
	 * indexed by behavior names. A behavior configuration can be either a string specifying
	 * the behavior class or an array of the following structure:
	 *
	 * ```php
	 * 'behaviorName' => [
	 *     'class' => 'BehaviorClass',
	 *     'property1' => 'value1',
	 *     'property2' => 'value2',
	 * ]
	 * ```
	 *
	 * Note that a behavior class must extend from [[Behavior]]. Behaviors can be attached using a name or anonymously.
	 * When a name is used as the array key, using this name, the behavior can later be retrieved using [[getBehavior()]]
	 * or be detached using [[detachBehavior()]]. Anonymous behaviors can not be retrieved or detached.
	 *
	 * Behaviors declared in this method will be attached to the component automatically (on demand).
	 *
	 * @return array the behavior configurations.
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
	 * Set filter (<b>POST</b>)<br />URL: https://rest.fokin-team.ru/request/set-filter
	 *
	 * Example of successfull response:
	 * 
	 * ```json
	 * {
	 *	 'cities': [
	 *	   {
	 *	     'id' = '...',
	 *	     'name' = '...',
	 *	     'region_id' = '...',
	 *	   },
	 *	 ],
	 *	 'setFilter': {
	 *	   'id' = '...',
	 *	   'user_id' = '...',
	 *	   'num_of_people' = '...',
	 *	   'family' = '...',
	 *	   'pets' = '...',
	 *	   'square_from' = '...',
	 *	   'square_to' = '...',
	 *	   'city_id' = '...',
	 *	   'price_from' = '...',
	 *	   'price_to' = '...',
	 *	   'description' = '...',
	 *	   'pivot_lt' = '...',
	 *	   'pivot_lg' = '...',
	 *	   'radius' = '...',
	 *	   'city_area_id' = '...',
	 *	   'rent_type' = '...',
	 *	   'property_type' = '...',
	 *	   'substring' = '...',
	 *	   'created_at' = '...',
	 *	   'updated_at' = '...',
	 *	 }
	 * }
	 * ```
	 * 
	 * Example of error response:
	 * 
	 * ```json
	 * {
	 *	 'error' = '...'
	 * }
	 * ```
	 * 
	 * @param string $signature The hash signature for this request
	 * 
	 * @param string $push_notification Push notification (0 or 1)
	 * @param string $fcmToken FCM token
	 * @param string $rent_type Rent type (JSON format, example: '[1,2,3]')
	 * @param string $property_type Properpty type (JSON format, example: '[1,2]')
	 * @param string $city_area_id City area ID
	 * @param string $city_id City ID
	 * @param string $price_from Price from
	 * @param string $price_to Price to
	 * @param string $substring Object name or object description for search
	 * 
	 * @return array Response
	 * @throws Exception Exception
	 */
	public function actionSetFilter(
		$signature = '',
		$push_notification = '',
		$fcmToken = '',
		$rent_type = '',
		$property_type = '',
		$city_area_id = '',
		$city_id = '',
		$price_from = '',
		$price_to = '',
		$substring = ''
	): array {
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
	 * New filter (<b>POST</b>)<br />URL: https://rest.fokin-team.ru/request/new-filter
	 *
	 * Example of successfull response:
	 * 
	 * ```json
	 * {
	 *	 'id' = '...',
	 * }
	 * ```
	 * 
	 * Example of error response:
	 * 
	 * ```json
	 * {
	 *	 'error' = '...'
	 * }
	 * ```
	 * 
	 * @param string $signature The hash signature for this request
	 * 
	 * @param integer $num_of_people Num of people
	 * @param integer $family Num of family members
	 * @param integer $pets Num of pets
	 * @param integer $square_from Square from
	 * @param integer $square_to	Square to
	 * @param string $city_id City ID
	 * @param integer $price_from Price from
	 * @param integer $price_to Price to
	 * @param string $description Description
	 * @param float $pivot_lt Pivot lantitude 
	 * @param float $pivot_lg Pivor longitude
	 * @param float $radius Radius
	 * @param string $city_area_id City area ID
	 * @param string $rent_type Rent type (JSON format, example: '[1,2,3]')
	 * @param string $property_type Properpty type (JSON format, example: '[1,2]')
	 * @param string $substring Object name or object description for search
	 * 
	 * @return array Response
	 * 
	 * @throws Exception Exception
	 */
	public function actionNewFilter(
		$signature = '',
		$num_of_people = '',
		$family = '',
		$pets = '',
		$square_from = '',
		$square_to = '',
		$city_id = '',
		$price_from = '',
		$price_to = '',
		$description = '',
		$pivot_lt = '',
		$pivot_lg = '',
		$radius = '',
		$city_area_id = '',
		$rent_type = '',
		$property_type = '',
		$substring = ''
	): array {
		$model = new Filter();

		$request = Yii::$app->request;

		$addresses = [];

		try {
			if ($model->load($request->post(), '')) {
				$model->user_id = Yii::$app->user->identity->id;

				$dateTime = new DateTime("", new \DateTimeZone("Europe/Kiev"));
				$model->created_at = $dateTime->format('Y-m-d H:i:s');

				$model->rentTypeIds = $request->post('rent_type') ? json_decode($request->post('rent_type')) : [];
				$model->propertyTypeIds = $request->post('property_type') ? json_decode($request->post('property_type')) : [];

				foreach ($model->addresses as $searchText) {
					$addresses[] = $this->_getAddress($searchText);
				}

				if ($model->save()) {
					foreach ($addresses as $address) {
						$filterAddress = new FilterAddress();

						$filterAddress->address_id = $address->id;
						$filterAddress->filters_id = $model->id;

						if (!$filterAddress->save()) {
							return ['error' => $filterAddress->errors];
						}
					}

					return ['id' => $model->id];
				} else {
					return ['error' => $model->errors];
				}
			} else {
				throw new Exception('Empty request or wrong attributes.');
			}
		} catch (Exception $e) {
			Yii::error($e->getMessage(), __METHOD__);

			return ['error' => $e->getMessage()];
		}
	}

	/**
	 * Update filter (<b>POST</b>)<br />URL: https://rest.fokin-team.ru/request/update/$id
	 *
	 * Example of successfull response:
	 * 
	 * ```json
	 * {
	 *	 'result' = true,
	 * }
	 * ```
	 * 
	 * Example of error response:
	 * 
	 * ```json
	 * {
	 *	 'error' = '...'
	 * }
	 * ```
	 * 
	 * @param integer $id The hash signature for this request
	 * 
	 * @param integer $num_of_people Num of people
	 * @param integer $family Num of family members
	 * @param integer $pets Num of pets
	 * @param integer $square_from Square from
	 * @param integer $square_to	Square to
	 * @param string $city_id City ID
	 * @param integer $price_from Price from
	 * @param integer $price_to Price to
	 * @param string $description Description
	 * @param float $pivot_lt Pivot lantitude 
	 * @param float $pivot_lg Pivor longitude
	 * @param float $radius Radius
	 * @param string $city_area_id City area ID
	 * @param string $rent_type Rent type (JSON format, example: '[1,2,3]')
	 * @param string $property_type Properpty type (JSON format, example: '[1,2]')
	 * @param string $substring Object name or object description for search
	 * 
	 * @return array Response
	 * 
	 * @throws Exception Exception
	 */
	public function actionUpdate(
		$id,
		$num_of_people = '',
		$family = '',
		$pets = '',
		$square_from = '',
		$square_to = '',
		$city_id = '',
		$price_from = '',
		$price_to = '',
		$description = '',
		$pivot_lt = '',
		$pivot_lg = '',
		$radius = '',
		$city_area_id = '',
		$rent_type = '',
		$property_type = '',
		$substring = ''
	): array {
		$request = Yii::$app->request;
		$model = Filter::findOne($id);

		if ($model->user_id != Yii::$app->user->identity->id) {
			throw new ForbiddenHttpException();
		}

		try {
			if ($model->load($request->post(), '')) {
				$model->rentTypeIds = $request->post('rent_type') ? json_decode($request->post('rent_type')) : [];
				$model->propertyTypeIds = $request->post('property_type') ? json_decode($request->post('property_type')) : [];

				if ($model->update()) {
					return ['status' => true];
				} else {
					return ['error' => $model->errors];
				}
			} else {
				return ['error' => $model->errors];
			}
		} catch (Exception $e) {
			Yii::error($e->getMessage(), __METHOD__);

			return ['error' => $e->getMessage()];
		}
	}

	/**
	 * View filter (<b>GET</b>)<br />URL: https://rest.fokin-team.ru/request/view/$id
	 *
	 * Example of successfull response:
	 * 
	 * ```json
	 * {
	 *	 'id' = '...',
	 *	 'user_id' = '...',
	 *	 'num_of_people' = '...',
	 *	 'family' = '...',
	 *	 'pets' = '...',
	 *	 'square_from' = '...',
	 *	 'square_to' = '...',
	 *	 'city_id' = '...',
	 *	 'price_from' = '...',
	 *	 'price_to' = '...',
	 *	 'description' = '...',
	 *	 'pivot_lt' = '...',
	 *	 'pivot_lg' = '...',
	 *	 'radius' = '...',
	 *	 'city_area_id' = '...',
	 *	 'rent_type' = '...',
	 *	 'property_type' = '...',
	 *	 'substring' = '...',
	 *	 'created_at' = '...',
	 *	 'updated_at' = '...',
	 * }
	 * ```
	 * 
	 * Example of error response:
	 * 
	 * ```json
	 * {
	 *	 'error' = '...'
	 * }
	 * ```
	 * 
	 * @param integer $id The hash signature for this request
	 * 
	 * @return array Response
	 * 
	 * @throws Exception Exception
	 */
	public function actionView($id): array
	{
		$model = Filter::findByIdentity($id);

		if ($model->user_id != Yii::$app->user->identity->id) {
			throw new ForbiddenHttpException();
		}

		try {
			if (!is_null($model)) {
				return $model->toArray();
			} else {
				throw new Exception('Filter not found');
			}
		} catch (Exception $e) {
			Yii::error($e->getMessage(), __METHOD__);

			return ['error' => $e->getMessage()];
		}
	}

	/**
	 * Get address
	 * 
	 * If address not exist in database - create new and return
	 * 
	 * @param string $searchText search text for find gei object by address
	 * 
	 * @return Address address object
	 */
	private function _getAddress($searchText)
	{
		$infoObject = Yii::$app->hereMaps->findAddressByText($searchText)->View[0]->Result[0]->Location;

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

			if (
				!isset($infoObject->Address->Street)
				|| !isset($infoObject->Address->District)
				|| !isset($infoObject->Address->City)
				|| !isset($infoObject->Address->County)
			) {
				throw new Exception('Bad address');
			}

			$address->streetName = $infoObject->Address->Street ?: null;
			$address->cityAreaName = $infoObject->Address->District ?: null;
			$address->cityName = $infoObject->Address->City ?: null;
			$address->regionName = $infoObject->Address->County ?: null;

			// Save address & object
			if (!$address->save()) {
				throw new Exception('Cann\'t create new address model');
			}
		}

		return $address;
	}
}
