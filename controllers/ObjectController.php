<?php

namespace micro\controllers;

use Yii;
use \Datetime;
use Yii\db\Query;
use Yii\web\UrlManager;

use yii\rest\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;

use yii\helpers\FileHelper;

use micro\models\EstateObject;
use micro\models\Address;
use micro\models\Metro;
use micro\models\Users;
use micro\models\Filters;
use micro\models\Images;
use micro\models\Objects;
use micro\models\Phones;
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
            'only' => ['get-objects'],
            'rules' => [
                [
                    'actions' => ['get-objects'],
                	'allow' => true,
                	'roles' => ['?'],
                ],
                [
                    'actions' => ['get-objects'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
            
            // Возвращает результаты экшенов в формате JSON  
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON; 
            
        $behaviors['authenticator'] = [
            'except' => ['login'],
            'class' => HttpBearerAuth::className()
        ];

		return $behaviors;
	}

	public function actionGetObjects()
    {
		$output = [];
        try {
			$user = Users::findOne(Yii::$app->user->identity->id);
			$lastFetchDate = $user->last_fetch;
			// get filter current user
            $filterObject = Filters::find()->where(['user_id' => $user->id])->one();
            if (is_null($filterObject)) {
                throw new \Exception("filter not set");
            }

			$objectsQuery = Objects::find()
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
				for ($i=0;$i<count($rent_type_array);$i++)
				{
					$current = $rent_type_array[$i];
					$objectsQuery->andWhere("rent_type = $current")->asArray()->all();
				}

            }
            if ($filterObject->property_type) {
				// проверяем каждый property_type из таблицы filters на наличие в таблице objects
				$property_type_array = array_filter(explode(',', $filterObject->property_type));
				for ($i=0;$i<count($property_type_array);$i++)
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
					->orWhere(['like', 'name', $filterObject->substring]);
					
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
				$images = Images::find()
					->select('path')
					->where("object_id = $singleObjectId")
					->orderBy('position')
					->asArray()
					->all(); 
				
				// if there is an imagery array, then replace each element with url
                if (is_array($images)) {
                    $images = array_map(function ($i) {
                        return ('images/' . $i);
                    }, $images);
				}
				// search phone
				$phones = Phones::find()
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

        } catch (\Throwable $e) {
            $this->_writeLog($e);
            $output['error'] = $e->getMessage();
        } finally {
            $output['data'] = $objects;

			return $output;
			
        }
    }
	

	/**
	 * Create new object
	 * 
	 * @param int|null $address_id
	 * @param int|null $building_type_id
	 * @param int|null $rent_type
	 * @param int|null $property_type
	 * @param int|null $metro_id
	 * @param string $name
	 * @param string $description
	 * @param float $price
	 * @param string|null $url
	 * @param int|null $user_id
	 * @param int|null $city_id
	 * @param int|null $region_id
	 * @param int|null $city_area_id
	 * @param string|null $created_at
	 * @param string|null $updated_at
	 * @param string|null $data
	 * 
	 * @param file|null $images[] - files of images
	 * 
	 * @return array|bool
	 */
	public function actionNew()
	{
        // $model = new EstateObject();
        $model = new Objects();
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

			// Create images
			$images = UploadedFile::getInstancesByName('images');

			//Add images
			if (!empty($images)) {
				//Директория для изображений
				$dir = Yii::getAlias('@webroot') . '/' .'uploads/' . $model->id;
	
				//Если добавляеться первое изображение, то создаётся директория для изображений
				if (!file_exists($dir)) {
					FileHelper::createDirectory($dir);
				}
	
				//Обработка каждого изображения
				foreach ($images as $file) {
					//Создание нового изображения
					$image = new Images();
	
					//Запись данных изображения в объект image
					$image->file = $file;
	
					//Путь к изображению
					$path = $dir . '/' . uniqid() . '.' . $image->file->extension;
	
					//Присвоение $path (путь к изображению) к атрибуту $image->path(string)
					$image->path = $path;
					$image->object_id = $model->id;

					//????position
					//$image->position = $request->post('position');
					$image->position = 0;
	
					//Сохранение нового изображения в БД
					$image->save();
	
					//Сохранение изображения в директроии $dir
					$image->file->saveAs($image->path);
				}
			}

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
						return [
							"id" => $model->id
						];
					} else {
						return [
							"errors" => $model->errors
						];
					}
				} else { // Return error if not save address
					return [
						"error" => $address->errors
					];
				}
			} else { // if exist address model
				// Link address to model
				$model->address_id = $address->id;
				
				$model->ln = $address->lt;
				$model->lt = $address->lg;

				if ($model->save()) {
					return [
						"id" => $model->id
					];
				} else {
					return [
						"error" => $model->errors
					];
				}
			}
        } else {
            return [
				'error' => 'empty request'
			];
        }
	}

	/**
	 * Update object by id
	 *
	 * @param file $images[] 
	 * @param string $urlsToDeleteImage[]
	 * 
	 * @return array|bool
	 */
	public function actionUpdate($id)
	{
	    // $model = EstateObject::findByIdentity($id);
	    $model = Objects::findByIdentity($id);
	    $request = Yii::$app->request->post();

		//Images update
		//Delete images
		if (isset($request['urlsToDeleteImage'])) {
			foreach ($request['urlsToDeleteImage'] as $url) {
				$images = Images::findOne('path'=>$url);

				FileHelper::removeDirectory($image->path);

				$image->delete();
			}
		}

		$newImages = UploadedFile::getInstancesByName('images');
		//Add new images
		if (!empty($newImage)) {
			//Директория для изображений
			$dir = Yii::getAlias('@webroot') . '/' .'uploads/' . $id;
	
			//Если добавляеться первое изображение, то создаётся директория для изображений
			if (!file_exists($dir)) {
				FileHelper::createDirectory($dir);
			}

			//Обработка каждого изображения
			foreach ($newImages as $file) {
				//Создание нового изображения
				$image = new Images();

				//Запись данных изображения в объект image
				$image->file = $file;

				//Путь к изображению
				$path = $dir . '/' . uniqid() . '.' . $image->file->extension;

				$image->path = $path;
				$image->object_id = $model->id;

				//????position
				//$image->position = $request->post('position');
				$image->position = 0;

				//Сохранение нового изображения в БД
				$image->save();

				//Сохранение изображения в директроии $dir
				$image->file->saveAs($image->path);
			}
		}
		
		// Object update
		if ($model->load($request, '') && $model->update()) {
			return [
				"result" => true
			];
		} else {
			return [
				"error" => $model->errors
			];
		}
	}

	/**
	 * View object by id
	 *
	 * @return array|bool
	 */
	public function actionView($id)
	{
	    $model = Objects::findByIdentity($id);
		
    	    if (!is_null($model)) {
        		return $model;
    	    } else {
        		return [
					"result"=>false
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