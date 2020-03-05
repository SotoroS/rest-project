<?php
//Строгая типизация
declare(strict_types=1);

namespace micro\controllers;

use Yii;
use \Datetime;
use Yii\db\Query;
use Yii\web\UrlManager;

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
use micro\models\Image;
//use micro\models\Object;
use micro\models\Phone;
use PharIo\Manifest\Url;

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
				// 'get-objects'  => ['get'],
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
	 * 
	 *
	 * @return array
	 * 
	 */

	public function actionGetObjects()//: array
    {
		$output = [];
		$objects = [];

        try {
			$user = User::findOne(Yii::$app->user->identity->id);
			$lastFetchDate = $user->last_fetch;

			// get filter current user
			$filterObject = Filter::find()->where(['user_id' => $user->id])->one();
			
            if (is_null($filterObject)) {
                throw new Exception("filter not set");
            }

			$objectsQuery = EstateObject::find()
				->select(['objects.id', 'objects.name',
							'objects.description',
							'objects.price',
							'objects.data',
							'objects.url',
							'objects.created_at',
							'city_name' => 'cities.name',
							'rent_type' => 'rent_type.name'])
				->joinWith('city')
				->joinWith('rentType');

			$filterInfo[] = $objectsQuery;

            // complement request
            if ($filterObject->city_id) {
				$objectsQuery->andWhere("city_id = $filterObject->city_id");
            }

            if ($filterObject->rent_type) {
				// проверяем каждый rent_type из таблицы filters на наличие в таблице objects
				$rent_type_array = array_filter(explode(',', $filterObject->rent_type));

				for ($i = 0; $i < count($rent_type_array); $i++)
				{
					$current = $rent_type_array[$i];
					$objectsQuery->andWhere("rent_type = $current")->asArray()->all();
				}
			}
			
            if ($filterObject->property_type) {
				// проверяем каждый property_type из таблицы filters на наличие в таблице objects
				$property_type_array = array_filter(explode(',', $filterObject->property_type));

				for ($i = 0; $i < count($property_type_array); $i++)
				{
					$current = $property_type_array[$i];
					$objectsQuery->andWhere("property_type = $current")->asArray()->all();
				}
            }

			$objectsQuery
				->andWhere("price >= $filterObject->price_from")
				->andWhere("price <= $filterObject->price_to");
		

            if ($filterObject->substring) {
                $objectsQuery->andWhere(['like', 'description', $filterObject->substring])
					->orWhere(['like', 'objects.name', $filterObject->substring]);
					
            }

            if ($lastFetchDate) {
				$objectsQuery->andWhere("objects.created_at > $lastFetchDate")->orderBy(['created_at' => SORT_DESC])
				->limit(100)->asArray()->all();
			}
			
			$objects = $objectsQuery->all();

			$items = [];
			
            foreach ($objects as $singleObject) {
				// each element as an array
				$singleObjectArray = (Array)$singleObject;
				$singleObjectId = $singleObjectArray['id'];
				
				// search image
				$images = Image::find()
					->select('path')
					->where("object_id = $singleObjectId")
					->orderBy('position')
					->asArray()
					->all(); 
				
				// if there is an imagery array, then replace each element with url
                if (is_array($images)) {
                    $images = array_map(function ($i) {
                        return ('image/' . $i);
                    }, $images);
				}
				// search phone
				$phones = Phone::find()
					->select('path') 
					->where("object_id = $singleObjectId") 
					->toArray()
					->all();
				
				// fill the array
                $singleObjectArray['images'] = $images;
                $singleObjectArray['phones'] = $phones;
				$singleObjectArray['created_at'] = strtotime($singleObjectArray['created_at']) * 1000;

				// add to the array objects
                $items[] = $singleObjectArray;
            }

            if (sizeof($items) > 0) {
				// set the time of Kiev
				$dateTime = new DateTime(null, new \DateTimeZone("Europe/Kiev"));
                $user->last_fetch = $dateTime->format('Y-m-d H:i:s');
				$user->save();
            }


        } catch (Exception $e) { 
			$output['error'] = $e->getMessage();
        } finally {
            $output['data'] = $objects;

			// Log
			Yii::info("GetObjects Output" ,__METHOD__);

			return $output;
        }
    }

	/**
	 * Create new object
	 * 
	 * @param string $name
	 * @param string $description
	 * @param float $price
	 * 
	 * @param string $address
	 * 
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
			if ($model->load($request->post(), '') && !is_null($request->post('address')) && !is_null($request->post('name')) && !is_null($request->post('description')) && !is_null($request->post('price'))) {
				$model->user_id = Yii::$app->user->identity->getId();

				// Get address info by search address
				$infoObject = static::getAddress($request->post('address'));

				// Check address
				if ($infoObject == false) {
					// Log
					Yii::error("Address Not Found" ,__METHOD__);

					return [
						'error'=>'Address Not Found'
					];
				}
				
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
						
						//$model->lt = $address->lt;
						//$model->lg = $address->lg;

						if (!$model->save()) {
						 	throw new Exception('Object Save False');
						}
					} else { // Return error if not save address
						throw new Exception("Address Save False");
					}
				} else { // if exist address model
					// Link address to model
					$model->address_id = $address->id;
					
					//$model->lt = $address->lt;
					//$model->lg = $address->lg;

					if (!$model->save()) {
						throw new Exception("Object Save False");
					}
				}
				// Create images
				$images = UploadedFile::getInstancesByName('images');
				
				//Add images
				if (!empty($images)) {
					//Директория для изображений
					$dir = Yii::getAlias('@webroot') . '/' .'uploads/' . $model->id;
					
					//Domain
					$dom = 'https://' . $_SERVER['SERVER_NAME'];

					//Если добавляеться первое изображение, то создаётся директория для изображений
					if (!file_exists($dir)) {
						FileHelper::createDirectory($dir);
					}
		
					//Обработка каждого изображения
					foreach ($images as $file) {
						//Создание нового изображения
						$image = new Image();
		
						//Запись данных изображения в объект image
						$image->file = $file;
		
						//Путь к изображению
						$path = '/' .'uploads/' . $model->id . '/' . uniqid() . '.' . $image->file->extension;
		
						//Присвоение $path (путь к изображению) к атрибуту $image->path(string)
						$image->path = $dom . $path;
						$image->object_id = $model->id;

						$image->position = 0;
		
						//Сохранение нового изображения в БД
						if (!$image->save()) {
							throw new Exception('Image Save False');
						}
						//Сохранение изображения в директроии $dir
						$image->file->saveAs(Yii::getAlias('@webroot') . $path);
					}
					//Sorting Images
					$images = Image::findAll(['object_id'=>$model->id]);

					if (!empty($images)) {
						$count = 1;
						foreach ($images as $i) {
							$i->position = $count;
							$i->update();
							$count = $count + 1;
						}
					}
				}
				// Log
				Yii::info("Object Save Success" ,__METHOD__);

				return [
					"id" => $model->id
				];
			} else {
				// Log
				Yii::error("Not all data entered" ,__METHOD__);

				return [
					'error'=>'Not all data entered'
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
	    // $model = EstateObject::findByIdentity($id);
		$model = EstateObject::findByIdentity($id);

		try {
			if (!$model) {
				// Log
				Yii::error("Object Not Found", __METHOD__);

				return [
					'error'=>"Object:$id Not Found"
				];
			}

			$request = Yii::$app->request->post();

			// Images update
			// Delete images
			if (isset($request['image_paths_to_delete'])) {
				foreach ($request['image_paths_to_delete'] as $url) {
					$image = Image::findOne(['path'=>$url, 'object_id'=>$model->id]);

					if (!is_null($image)) {
						FileHelper::removeDirectory($image->path);

						if(!$image->delete()) {
							throw new Exception("Image Delete Failed");
						}
					}
				}
			}

			// Load new images
			$newImages = UploadedFile::getInstancesByName('images');
			
			// Add new images
			if (!empty($newImages)) {
				// Directory for images
				$dir = Yii::getAlias('@webroot') . '/' .'uploads/' . $id;

				//Domain
				$dom = 'https://' . $_SERVER['SERVER_NAME'];
		
				//Create Directory for images
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
					$path = '/' .'uploads/' . $id . '/' . uniqid() . '.' . $image->file->extension;

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
			//Sorting Images
			$images = Image::findAll(['object_id'=>$model->id]);
			
			if (!empty($images)) {
				$count = 1;
				foreach ($images as $i) {
					$i->position = $count;
					$i->update();
					$count = $count + 1;
				}
			}
			
			// Update at - time //Neet to setting TimeZone
			$dateTime = new DateTime("", new \DateTimeZone("Europe/Kiev"));
			$model->updated_at = $dateTime->format('Y-m-d H:i:s');

			// Object update
			if (!$model->load($request, '')) {
				if (empty($newImages) && !isset($request['image_paths_to_delete'])) {
					// Log
					Yii::error("Object Load from request failed", __METHOD__);

					return [
						'error'=>"Nothing Update"
					];
				}
			}

			if ($model->update()) {
				// Log
				Yii::info("Object Update Success",__METHOD__);

				return [
					"result" => true
				];
			} else {
				throw new Exception('Object Update Failed');
			}
		} catch(Exception $e) {
			// Log
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
	    	$model = EstateObject::findByIdentity($id);
		
    	    if (!is_null($model)) {
				$images = Image::find()
					->select('path')
					->where(['object_id'=>$id])
					->all();

				$output = [
					'object'=>$model,
					'images'=>$images,
				];
				// Log
				Yii::info("Object Found Success" ,__METHOD__);

        		return $output;
    	    } else {
				// Log
				Yii::error("Object Not Found" ,__METHOD__);

        		return [
					"error"=>"Object:$id Not Found"
				];
			}
		} catch(Exception $e) {
			// Log
			Yii::error($e->getMessage() ,__METHOD__);

			return [
				'error'=>$e->getMessage()
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
	private static function getAddress($searchText): object
	{
		// Create query params for get info from API HERE maps
		$param = http_build_query(array(
			'apiKey' => Yii::$app->params['here_api_key'],
			'searchtext' => $searchText,
		));

		// Get info about address
		$searchResult = json_decode(file_get_contents("https://geocoder.ls.hereapi.com/6.2/geocode.json?$param"));
		
		// Log
		Yii::info("Get Address" ,__METHOD__);

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
	private static function getStation($lt, $lg): object
	{
		// Create query params for get nearby station from API HERE maps
		$param = http_build_query(array(
			'apiKey' => Yii::$app->params['here_api_key'],
			'center' => $lt . ',' . $lg,
			'radius' => 500,
			'max' => 1,
		));

		// Get info about mentro by address
		$searchResult = json_decode(file_get_contents("https://transit.ls.hereapi.com/v3/stations/by_geocoord.json?$param"));
		
		// Log
		Yii::info("Get Station" ,__METHOD__);

		return $searchResult;
	}
}
