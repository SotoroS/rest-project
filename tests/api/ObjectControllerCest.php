<?php 

class ObjectControllerCest
{
    public function getObjectsViaApi(\ApiTester $I)
    {
        //Вход, получение access_token и авторизация
        $I->amHttpAuthenticated('service_user', 'test1234');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPOST('/user/login',[
            'email' => 'ghettogopnik1703@gmail.com',
            'password' => '45678',
        ]);
        $response=$I->grabResponse();
        $response=json_decode($response);
        $token = $response->access_token;
        $I->amBearerAuthenticated($token);
        //
        $I->sendGET('object/get-objects');
        $I->seeResponseMatchesJsonType([
        'data' => 'array',
        ]);
    }

    public function newObjectViaApi(\ApiTester $I)
    {
        $I->amHttpAuthenticated('service_user', 'test1234');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        //Вход, получение access_token и авторизация
        $I->amHttpAuthenticated('service_user', 'test1234');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPOST('/user/login',[
            'email' => 'ghettogopnik1703@gmail.com',
            'password' => '45678',
        ]);
        $response=$I->grabResponse();
        $response=json_decode($response);
        $token = $response->access_token;
        $I->amBearerAuthenticated($token);
        //

        $I->sendPOST('/object/get-objects',[
        'address' => 'г.Волгоград, ул.50-летия ВЛКСМ',
        'name' => 'test',
        'description' => 'test',
        'price' => '5000000'
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
        'data' => 'array',
        ]);
    }

    public function updateObjectViaApi(\ApiTester $I)
    {
        $I->amHttpAuthenticated('service_user', 'test1234');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        //Вход, получение access_token и авторизация
        $I->amHttpAuthenticated('service_user', 'test1234');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPOST('/user/login',[
            'email' => 'ghettogopnik1703@gmail.com',
            'password' => '45678',
        ]);
        $response=$I->grabResponse();
        $response=json_decode($response);
        $token = $response->access_token;
        $I->amBearerAuthenticated($token);
        //

        $I->sendPOST('/object/update/3',[ //НУЖНО БРАТЬ ID ИЗ ТОЛЬКО ЧТО СОЗДАННОГО ТЕСТОВОГО ОБЪЕКТА
        'name' => 'Update-update'
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
        'result' => 'boolean',
        ]);
    }

    public function viewObjectViaApi(\ApiTester $I)
    {
        $I->amHttpAuthenticated('service_user', 'test1234');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        //Вход, получение access_token и авторизация
        $I->amHttpAuthenticated('service_user', 'test1234');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPOST('/user/login',[
            'email' => 'ghettogopnik1703@gmail.com',
            'password' => '45678',
        ]);
        $response=$I->grabResponse();
        $response=json_decode($response);
        $token = $response->access_token;
        $I->amBearerAuthenticated($token);
        //

        $I->sendGET('/object/view/5'); //ТУТ ДОЛЖЕН БЫТЬ ID КАКОГО-ТО ОБЪЕКТА, ЖЕЛАТЕЛЬНО НОВОСОЗДАННОГО
        $I->seeResponseIsJson();

        //Фильтр для даты в формате "гггг-мм-дд чч:мм:cc"
        Codeception\Util\JsonType::addCustomFilter('datetime', function($value) {
            return preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/',$value,$matches);;
        });
        //

        $I->seeResponseMatchesJsonType([
        "id" => 'integer',
        "address_id" => 'integer',
        "building_type_id" => 'integer',
        "rent_type" => 'integer',
        "property_type" => 'integer',
        "metro_id" => 'integer',
        "name" => 'string',
        "description" => 'string',
        "price" => 'string',
        "url" => 'string:url|null',
        "user_id" => 'integer',
        "city_id" => 'integer',
        "city_area_id" => 'integer',
        "created_at" => 'string:datetime',
        "updated_at" => 'string:datetime',
        "data" => 'boolean|null',
        ]);
    }
}
