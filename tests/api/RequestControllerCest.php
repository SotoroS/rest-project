<?php 
use micro\models\User;
use micro\models\Filter;

class RequestControllerCest
{
    public $alternativeEmail = 'ghettogopnik1703@gmail.com';
    public $email = 'nape.maxim@gmail.com';
    public $password = '1234';
    public $testUser;
    public $testFilter;

    private function verifyViaApi(\ApiTester $I)
    {
        $this->testUser = User::find()->where(['email' => $this->email])->one();
        
        $I->sendGET('/user/verify',[
            'token' => $testUser->signup_token,
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            array('result' => true)
        );
    }

    //НЕ ЗАКОНЧЕНО
    public function newViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        //Работает
        //Получение токена с помощью регулярного выражения из текста письма.
        $I->sendPOST('/user/signup-web',[
            'email' => $this->email,
            'password' => $this->password
        ]);
        $response = json_decode($I->grabResponse(), true);
        //\Codeception\Util\Debug::debug(is_array($response));die();
        if (array_key_exists("mailSend",$response) && ($response["mailSend"]) == true)
        {
            $this->verifyViaApi($I);
        }
        else
        {
            $I->seeResponseContainsJson(
                array('error' => 'User exist.')
            );
            $testUser = User::find()->where(['email' => $this->email])->one();
            $this->testUser = $testUser;
        }

        //Вход, получение access_token и авторизация
        $I->sendPOST('/user/login',[
            'email' => $this->email,
            'password' => $this->password,
        ]);
        $response=$I->grabResponse();
        $response=json_decode($response);
        $token = $response->access_token;
        $I->amBearerAuthenticated($token);
        //

        //ОТПРАВЛЯТЬ НЕОБХОДИМЫЕ ДАННЫЕ
        $I->sendPOST('/request/filter-new',[
            'user_id' => $this->testUser->ID,
            'num_of_people' => 1,
            'family' => 2,
            'pets' => 3,
            'price_from' => 20000,
            'price_to' => 6000000,
            'description' => 'Description',
            'rent_type' => 'Rent Type',
            'property_type' => 'Property Type',
            'substring' => 'Substring',
            'addresses' => ['Саратов улица Вишневая 24'],
            'requestName' => 'Проверка'
        ]);
        //ОТПРАВЛЯТЬ НЕОБХОДИМЫЕ ДАННЫЕ

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            array('result' => true)
        );

        $this->testFilter = Filter::find()->where(['user_id' => $this->testUser->ID])->one();
    }

    public function setViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        //Вход, получение access_token и авторизация
        $I->sendPOST('/user/login',[
            'email' => $this->email,
            'password' => $this->password,
        ]);
        $response=$I->grabResponse();
        $response=json_decode($response);
        $token = $response->access_token;
        $I->amBearerAuthenticated($token);
        //

        $I->sendPOST('/request/set-filter',[
            'fcmToken' => 'token',
            //'city_area_id' => 1,
            //'request_type_id' => 1,
            'push_notification' => 1,
            'price_from' => 40000,
            'price_to' => 500000,
            'substring' => 'substring',
            'requestName' => 'Abcd',
            'push_enabled' => 1
        ]);

        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'cities' => 'array',
        ]);
    }

    public function updateViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        //Вход, получение access_token и авторизация
        $I->sendPOST('/user/login',[
            'email' => $this->email,
            'password' => $this->password,
        ]);
        $response=$I->grabResponse();
        $response=json_decode($response);
        $token = $response->access_token;
        $I->amBearerAuthenticated($token);
        //

        //ОТПРАВЛЯТЬ НЕОБХОДИМЫЕ ДАННЫЕ
        $I->sendPOST('/request/update/'.$this->testFilter->id,[ //ЗДЕСЬ ДОЛЖЕН БЫТЬ ID ФИЛЬТРА СОЗДАННОГО В ТЕСТЕ ВЫШЕ
            'num_of_people' => 1,
            'family' => 2,
            'pets' => 3,
            //'request_type_id' => 1,
            'square_from' => 200,
            'square_to' => 500,
            //'city_id' => 1,
            'price_from' => 20000,
            'price_to' => 5300000,
            'description' => 'Description',
            //'city_area_id' => 1,
            'rent_type' => 'Rent Type',
            'property_type' => 'Property Type',
            'substring' => 'Substring',
            'created_at' => 'Created At', //Здесь должна быть дата
            'updated_at' => 'Updated At', //Здесь должна быть дата
        ]);
        //ОТПРАВЛЯТЬ НЕОБХОДИМЫЕ ДАННЫЕ

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            array('result' => true)
        );
    }

    public function viewViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        //Вход, получение access_token и авторизация
        $I->sendPOST('/user/login',[
            'email' => $this->email,
            'password' => $this->password,
        ]);
        $response=$I->grabResponse();
        $response=json_decode($response);
        $token = $response->access_token;
        $I->amBearerAuthenticated($token);
        //
        
        $I->sendGET('/request/view/'.$this->testFilter->id); //НУЖНО ИСПОЛЬЗОВАТЬ ID ФИЛЬТРА СОЗДАННОГО В ТЕСТЕ ВЫШЕ

        //Проверка формата данных для даты в формате "гггг-мм-дд чч:мм:cc"
        Codeception\Util\JsonType::addCustomFilter('datetime', function($value) {
            return preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/',$value,$matches);
        });
        //
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'user_id' => 'integer',
            'num_of_people' => 'integer|null',
            'family' => 'integer|null',
            'pets' => 'integer|null',
            'request_type_id' => 'integer|null',
            'square_from' => 'integer|null',
            'square_to' => 'integer|null',
            'city_id' => 'integer',
            'price_from' => 'integer',
            'price_to' => 'integer',
            'description' => 'string|null',
            'pivot_lt' => 'number|null',
            'pivot_lg' => 'number|null',
            'radius' => 'number|null',
            'city_area_id' => 'integer',
            'rent_type' => 'string|null',
            'property_type' => 'string|null',
            'substring' => 'string',
            'created_at' => 'string:datetime',
            'updated_at' => 'string:datetime',
        ]);
    }
}
