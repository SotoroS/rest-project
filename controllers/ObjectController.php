<?php
//Строгая типизация
declare(strict_types=1);

namespace micro\controllers;

use Yii;

use \Datetime;

use yii\base\Exception;
use yii\rest\Controller;

use yii\web\Response;
use yii\web\UploadedFile;

use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;

use yii\helpers\FileHelper;

use micro\models\EstateObject;
use micro\models\Address;
use micro\models\Metro;
use micro\models\User;
use micro\models\Filter;
use micro\models\City;
use micro\models\Image;
use micro\models\Phone;
use micro\models\RentType;

/**
 * Class ObjectController
 * 
 * @package micro\controllers
 */
class ObjectController extends Controller
{
	/**
	 * @return array
	 */
	public function behaviors()
	{
		$behaviors = parent::behaviors();

		$behaviors['access'] = [
			'class' => AccessControl::className(),
			'only' => ['get-objects', 'new', 'update', 'view'],
			'rules' => [
				[
					'actions' => ['get-objects'],
					'allow' => true,
					'roles' => ['?'],
				],
				[
					'actions' => ['get-objects', 'new', 'update', 'view'],
					'allow' => true,
					'roles' => ['@'],
				],
			],

		];
		$behaviors['verbs'] = [
			'class' => VerbFilter::className(),
			'actions' => [
				'get-objects' => ['get'],
				'new'   => ['post'],
				'update' => ['post'],
				'view' => ['get'],
			],
		];

		// Возвращает результаты экшенов в формате JSON  
		$behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

		$behaviors['authenticator'] = [
			'except' => [],
			'class' => HttpBearerAuth::className()
		];

		return $behaviors;
	}

	/**
	 * Get all objects
	 *
	 * @return array
	 */
	public function actionGetObjects(): array
	{
		$output = [];
		$objects = [];
		$items = [];

		try {
			$user = User::findOne(Yii::$app->user->identity->id);
			$lastFetchDate = $user->last_fetch;

			$filterObject = Filter::find()
				->where("user_id = $user->id")
				->orderBy(['created_at' => SORT_DESC])
				->one();

			if (is_null($filterObject)) {
				throw new Exception("Filter not set");
			}

			$objectsQuery = EstateObject::find()
				->select([
					'objects.id',
					'objects.name',
					'objects.description',
					'objects.price',
					'objects.data',
					'objects.url',
					'objects.created_at',
					'objects.city_id',
					'objects.rent_type'
				])
				->joinWith('city')
				->joinWith('rentType');

			$filterInfo[] = $objectsQuery;

			if ($filterObject->city_id) {
				$objectsQuery->andWhere("city_id = $filterObject->city_id");
			}

			if ($filterObject->rent_type) {
				$rent_type_array = array_filter(explode(',', $filterObject->rent_type));
				$subQuery = ['or'];

				for ($i = 0; $i < count($rent_type_array); $i++) {
					$current = $rent_type_array[$i];
					array_push($subQuery,["rent_type"=>$current]);
				}
				$objectsQuery->andWhere($subQuery)->asArray()->all();
			}
			
			if ($filterObject->property_type) {
				$property_type_array = array_filter(explode(',', $filterObject->property_type));
				$subQuery = ['or'];
				
				for ($i = 0; $i < count($property_type_array); $i++) {
					$current = $property_type_array[$i];
					array_push($subQuery,["property_type"=>$current]);
				}
				$objectsQuery->andFilterWhere($subQuery)->asArray()->all();
			}

			$objectsQuery
				->andWhere("price >= $filterObject->price_from")
				->andWhere("price <= $filterObject->price_to");


			if ($filterObject->substring) {
				$objectsQuery->andWhere(['like', 'description', $filterObject->substring])
					->orWhere(['like', 'objects.name', $filterObject->substring]);
			}

			if ($lastFetchDate) {
				$objectsQuery->andWhere(['>=', 'objects.created_at', $lastFetchDate]);
			}

			$objects = $objectsQuery->limit(100)->orderBy(['created_at' => SORT_DESC])->all();
			
			$dateTime = new DateTime();

			$user->last_fetch = $dateTime->format('Y-m-d H:i:s');
			$user->update();

			$items = [];
			foreach ($objects as $singleObject) {		
				$singleObject=(object)$singleObject;
				$singleObjectId = $singleObject->id;

				$images = Image::find()
					->where(['object_id' => $singleObjectId])
					->orderBy('position')
					->all();

				$paths = [];

				if (!empty($images)) {
					$paths = [];

					foreach ($images as $image) {
						$paths[] = $image->path;
					}
				}

				$phones = Phone::find()
					->select('phone')
					->where(['object_id' => $singleObjectId])
					->all();

				$phoneArray = [];

				if (!empty($phones)) {
					$phoneArray = [];

					foreach ($phones as $phone) {
						$phoneArray[] = $phone->phone;
					}
				}

				$city = City::findOne(['id' => $singleObject->city_id]);
				$rent_type = RentType::findOne(['id' => $singleObject->rent_type]);

				$fields = [
					'id' => $singleObjectId,
					'name' => $singleObject->name,
					'description' => $singleObject->description,
					'price' => $singleObject->price,
					'data' => $singleObject->data,
					'url' => $singleObject->url,
					'created_at' => $singleObject->created_at,
					'city_name' => $city->name,
					'rent_type' => $rent_type->name,
				];

				$fields['images'] = $paths;
				$fields['phones'] = $phoneArray;

				$items[] = $fields;
			}
		} catch (Exception $e) {
			$output = [];

			Yii::error($e->getMessage(), __METHOD__);

			$output['error'] = $e->getMessage();
		}

		$output['data'] = $items;

		return $output;
	}

	/**
	 * Create new object
	 *  
	 * @param string $name
	 * @param string $description
	 * @param string $address
	 * 
	 * @param float $price
	 * 
	 * @param file|null $images[] - files of images
	 * 
	 * @return array|bool
	 */
	public function actionNew(): array
	{
		$model = new EstateObject();
		$request = Yii::$app->request;

		try {
			if (
				$model->load($request->post(), '')
				&& !empty($request->post('address'))
				&& !empty($request->post('name'))
				&& !empty($request->post('description'))
				&& !empty($request->post('price'))
			) {
				$model->user_id = Yii::$app->user->identity->getId();
				echo print_r($model->save(),true);
				exit();
				$address = $this->_getAddress($request->post('address'));
				
				$metro = $this->_getMetro(
					(float)$address->lt,
					(float)$address->lg
				);
				
				$model->address_id = $address->id;
				$model->metro_id = $metro->id;
				$model->save();
				
				if ($request->post('phone')) {
					$phone = new Phone();

					$phone->phone = $request->post('phone');
					$phone->object_id = $model->id;

					if (!$phone->save()) {
						return [
							'error' => $phone->errors
						];
					}
				}

				// Create images
				$images = UploadedFile::getInstancesByName('images');

				// Add images
				if (!empty($images)) {
					// Директория для изображений
					$dir = Yii::getAlias('@webroot') . '/' . 'uploads/' . $model->id;

					// Domain
					$dom = 'https://' . $_SERVER['SERVER_NAME'];

					// Если добавляеться первое изображение, то создаётся директория для изображений
					if (!file_exists($dir)) {
						FileHelper::createDirectory($dir);
					}

					// Обработка каждого изображения
					foreach ($images as $file) {
						// Создание нового изображения
						$image = new Image();

						//Запись данных изображения в объект image
						$image->file = $file;

						// Путь к изображению
						$path = '/' . 'uploads/' . $model->id . '/' . uniqid() . '.' . $image->file->extension;

						// Присвоение $path (путь к изображению) к атрибуту $image->path(string)
						$image->path = $dom . $path;
						$image->object_id = $model->id;

						$image->position = 0;

						// Сохранение нового изображения в БД
						if (!$image->save()) {
							throw new Exception('Image Save False');
						}
						// Сохранение изображения в директроии $dir
						$image->file->saveAs(Yii::getAlias('@webroot') . $path);
					}
					// Sorting Images
					$images = Image::findAll(['object_id' => $model->id]);

					if (!empty($images)) {
						$count = 1;
						foreach ($images as $i) {
							$i->position = $count;
							$i->update();
							$count = $count + 1;
						}
					}
				}

				$model->save();
				
				return [
					'id' => $model->id
				];
			} else {
				throw new Exception("Not set address, name, description, price.");
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
	 * @param string $name
	 * @param string $description
	 * @param float $price
	 * @param file $images[] 
	 * @param string $image_paths_to_delete[]
	 * 
	 * @return array|bool
	 */
	public function actionUpdate($id): array
	{
		$user = User::findOne(Yii::$app->user->identity->id);
		$model = EstateObject::findByIdentity([
			'id' => $id,
			'user_id' => $user->id
		]);

		try {
			if (!$model) {
				throw new Exception("Object Not Found");

				return [
					'error' => "Object:$id Not Found"
				];
			}

			$request = Yii::$app->request->post();

			// Phones update
			if (isset($request['phone'])) {
				$phone = new Phone();

				$phone->phone = $request['phone'];
				$phone->object_id = $model->id;

				$phone->save();
			}

			// Images update
			// Delete images
			if (isset($request['image_paths_to_delete'])) {
				foreach ($request['image_paths_to_delete'] as $url) {
					$image = Image::findOne(['path' => $url, 'object_id' => $model->id]);

					if (!is_null($image)) {
						FileHelper::removeDirectory($image->path);

						if (!$image->delete()) {
							throw new Exception("Image Delete Failed");
						}
					} else {
						throw new Exception("Cannot find image with that url for current user.");
					}
				}
			}

			// Load new images
			$newImages = UploadedFile::getInstancesByName('images');

			// Add new images
			if (!empty($newImages)) {
				// Directory for images
				$dir = Yii::getAlias('@webroot') . '/' . 'uploads/' . $id;

				// Domain
				$dom = 'https://' . $_SERVER['SERVER_NAME'];

				// Create Directory for images
				if (!file_exists($dir)) {
					FileHelper::createDirectory($dir);
				}

				// New images save
				foreach ($newImages as $file) {
					// Create new image
					$image = new Image();

					// Write data from file into $image->file
					$image->file = $file;

					// Image path 
					$path = '/' . 'uploads/' . $id . '/' . uniqid() . '.' . $image->file->extension;

					$image->path = $dom . $path;
					$image->object_id = $model->id;

					$image->position = 0;

					// Save image
					if (!$image->save()) {
						throw new Exception('Image Save Failed');
					}

					// Save image to path
					$image->file->saveAs(Yii::getAlias('@webroot') . $path);
				}
			}

			// Sorting Images
			$images = Image::findAll(['object_id' => $model->id]);

			if (!empty($images)) {
				$count = 1;

				foreach ($images as $i) {
					$i->position = $count;
					$i->update();
					$count = $count + 1;
				}
			}

			$oldValueCreateAt = $model->created_at;

			// Object update
			if (!$model->load($request, '')) {
				if (empty($newImages) && !isset($request['image_paths_to_delete'])) {
					throw new Exception("Object Load from request failed");
				}
			}

			// Update fields updated_at by current time and protect created_at from change by user.
			$dateTime = new DateTime("", new \DateTimeZone("Europe/Kiev"));

			$model->updated_at = $dateTime->format('Y-m-d H:i:s');
			$model->created_at = $oldValueCreateAt;

			if ($model->update()) {
				return [
					"result" => true
				];
			} else {
				throw new Exception('Object update failed');
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
	 * @return array|bool
	 */
	public function actionView($id): array
	{
		try {
			$model = EstateObject::findOne($id);

			if (!is_null($model)) {
				$images = Image::find()
					->where(['object_id' => $id])
					->orderBy('position')
					->all();

				$paths = [];

				foreach ($images as $image) {
					$paths[] = $image->path;
				}

				$phones = Phone::find()
					->where(['object_id' => $id])
					->all();

				$phonesArray = [];

				foreach ($phones as $phone) {
					$phonesArray[] = $phone->phone;
				}

				$output = [
					'object' => $model,
					'images' => $paths,
					'phones' => $phonesArray,
				];

				return $output;
			} else {
				throw new Exception('Object not found');
			}
		} catch (Exception $e) {
			Yii::error($e->getMessage(), __METHOD__);

			return [
				'error' => $e->getMessage()
			];
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

	/**
	 * Get nearby metro model
	 * 
	 * @param float $lt - lantitude of center point search
	 * @param float $lg - longitude of center point search
	 * 
	 * @return Metro metro model
	 */
	private function _getMetro($lt, $lg)
	{
		$infoObject = Yii::$app->hereMaps->findStatationsNearby($lt, $lg);
		
		$metro = Metro::find()->where(['name' => $infoObject->Stations->Stn[0]->name])->one();
		
		if (is_null($metro)) {
			$metro = new Metro();

			$metro->name = $infoObject->Stations->Stn[0]->name;

			if (!$metro->save()) {
				throw new Exception("Cann't create new metro station model");
			}
		}
		
		return $metro;
	}
}
