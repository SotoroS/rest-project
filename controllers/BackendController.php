<?php

namespace App\Http\Controllers;

use App;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;


class BackendController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', [
            'only' => [
                'setFilter',
                'getObjects',
                'getAreas'
            ]
        ]);
    }

    public function signUp(Request $request)
    {
        $output = [];
        try {
            $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/../../../google.json');
            $firebase = (new Factory)
                ->withServiceAccount($serviceAccount)
                ->create();

            $accountId = $request['accountId'];
            $firebase->getAuth()->getUser($accountId);

            $user = App\User::where('account_id', '=', $accountId)->first() ?: new App\User();

            if (!$user->account_id) {
                $user->account_id = $accountId;
            }

            $user->deviceType = $request['deviceType'];
            $user->fcmToken = $request['fcmToken'];
            $user->save();

            $output['status'] = true;
            $output['cities'] = App\City::all(['id', 'name'])->toJson();
            $output['city_areas'] = App\CityArea::all(['id', 'name', 'city_id'])->toJson();
            $output['rent_types'] = App\RentType::all(['id', 'name'])->toJson();
            $output['property_types'] = App\PropertyType::all(['id', 'name'])->toJson();

        } catch (\Throwable $e) {
            $output['status'] = false;
            $output['error'] = $e->getMessage();
            return response()->json($output)->setStatusCode(500);
        }
        return response()->json($output)->setStatusCode(200);
    }

    public function setFilter(Request $request)
    {
        $output = [];
        try {
            $fcmToken = $request["fcmToken"];
            $cityId = (int)$request['city'] ?: null;
            $cityAreaId = (int)$request['city_area_id'] ?: null;
            $notification = (int)$request['push_notification'] ?: null;

            $rentType = json_decode($request['rent_type']) ?: null;
            $propertyType = json_decode($request['property_type']) ?: null;

            $propertyType = array_filter(array_map(function ($a) {
                return (int)$a;
            }, $propertyType));

            $rentType = array_filter(array_map(function ($a) {
                return (int)$a;
            }, $rentType));


            $propertyType = implode(',', $propertyType);
            $rentType = implode(',', $rentType);

            $requestData = [
                'city' => $cityId,
                'city_area_id' => $cityAreaId,
                'rent_type' => $rentType,
                'property_type' => $propertyType,
                'price_from' => (int)$request['price_from'] ?: 0,
                'price_to' => (int)$request['price_to'] ?: 500000000,
                'substring' => $request['substring'] ?: "",
            ];

            $user = Auth::user();
            $filterObject = App\Filter::where('user_id', '=', $user->id)->first();
            $user->notifications = $request['push_enabled'] ? 1 : 0;
            if ($fcmToken) {
                $user->fcmToken = $fcmToken;
            }
            $user->save();

            if (is_null($filterObject)) {
                $filterObject = new App\Filter;
                $filterObject->user_id = $user->id;
            }

            $user->notifications = $notification;
            $user->save();

            $filterObject->rent_type = $rentType ?: null;
            $filterObject->property_type = $propertyType ?: null;
            $filterObject->city_area_id = $requestData['city_area_id'] ?: NULL;
            $filterObject->city_id = $requestData['city'] ?: NULL;
            $filterObject->price_from = $requestData['price_from'];
            $filterObject->price_to = $requestData['price_to'];
            $filterObject->substring = $request['substring'];
            $filterObject->save();
            $status = true;

            $output['cities'] = App\City::all(['id', 'name'])->toJson();

        } catch (QueryException $e) {
            $output['error'] = "Invalid data provided;";
            $this->_writeLog($e);
            $status = false;
        } catch (\Throwable $e) {
            $this->_writeLog($e);
            $output['error'] = $e->getMessage();
            $status = false;
        } finally {
            //$output['status'] = $status;
            $output['status'] = true;
            return response()->json($output);
        }
    }

    public function getObjects()
    {
        DB::update('SET time_zone = "SYSTEM"');
        $output = [];
        try {
            $user = Auth::user();
            $filterObject = App\Filter::where('user_id', '=', $user->id)->first();
            if (is_null($filterObject)) {
                throw new \Exception("filter not set");
            }
            $columnsToGet = [
                'objects.id', 'objects.name',
                'objects.description',
                'objects.price',
                'objects.data',
                'objects.url',
                'objects.created_at',
                'cities.name as city_name',
                'rent_type.name as rent_type'
            ];

            $query = DB::table('objects')
                ->leftJoin("cities", 'objects.city_id', '=', 'cities.id')
                ->leftJoin("rent_type", 'rent_type.id', '=', 'objects.rent_type');

            if ($filterObject->city_id) {
                $query->where('city_id', $filterObject->city_id);
            }

//            if ($filterObject->city_area_id) {
//                $query->where('city_area_id', $filterObject->city_id);
//            }

            if ($filterObject->rent_type) {
                $query->whereIn('rent_type', array_filter(explode(',', $filterObject->rent_type)));
            }
            if ($filterObject->property_type) {
                $query->whereIn('property_type', array_filter(explode(',', $filterObject->property_type)));
            }

            $query->where('price', '>=', $filterObject->price_from);
            $query->where('price', '<=', $filterObject->price_to);

            if ($filterObject->substring) {
                $query->where("description", "like", "%{$filterObject->substring}%")
                    ->orWhere("name", "like", "%{$filterObject->substring}%");
            }

            $lastFetchDate = $user->last_fetch;
            if ($lastFetchDate) {
                $query->where("objects.created_at", ">", $lastFetchDate);
            }

            $results = $query->take(100)->get($columnsToGet)->sortByDesc('created_at');

//        	$sql = str_replace(array('?'), array('\'%s\''), $query->toSql());
//          $sql = vsprintf($sql, $query->getBindings());
//          var_dump($sql);exit;

            $objects = [];
            foreach ($results as $singleObject) {
                $singleObjectArray = (Array)$singleObject;
                $images = App\Image::where(function ($q)
                use ($singleObjectArray) {
                    $q->where('object_id', '=', $singleObjectArray['id']);
                })->orderBy('position')->pluck('path')->toArray();
                if (is_array($images)) {
                    $images = array_map(function ($i) {
                        return URL::to('/images') . '/' . $i;
                    }, $images);
                }
                $phones = App\Phone::where(function ($q) use ($singleObjectArray) {
                    $q->where('object_id', '=', $singleObjectArray['id']);
                })->pluck('phone')->toArray();
                $singleObjectArray['images'] = $images;
                $singleObjectArray['phones'] = $phones;
                $singleObjectArray['created_at'] = strtotime($singleObjectArray['created_at']) * 1000;
                $objects[] = $singleObjectArray;
            }

            if (sizeof($objects) > 0) {
                $user->last_fetch = Carbon::now(new \DateTimeZone("Europe/Kiev"));
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

    function getSQL($builder)
    {
        $sql = $builder->toSql();
        foreach ($builder->getBindings() as $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }
        return $sql;
    }


    public function getAreas()
    {
        $output = [];
        try {
            $citiesArray = [];
            $cities = App\City::get();
            foreach ($cities as $city) {
                $areas = App\CityArea::where('city_id', '=', $city->id)->get(['id', 'name']);
                $citiesArray[] = [
                    'name' => $city->name,
                    'id' => $city->id,
//                    'areas' => $areas
                ];
            }
            $output['data'] = $citiesArray;
        } catch (\Exception $e) {
            $output['error'] = $e->getMessage();

        } finally {
            return response()->json($output);
        }
    }

    protected function _sendVerificationCode($phone, $code)
    {
        return true;
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
