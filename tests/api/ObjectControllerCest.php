<?php 
use micro\models\User;
use micro\models\EstateObject;

class ObjectControllerCest
{
    public $alternativeEmail = 'ghettogopnik1703@gmail.com';
    public $email = 'nape.maxim@gmail.com';
    public $password = '1234';
    public $testUser;
    public $testObject;

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

    public function newObjectViaApi(\ApiTester $I)
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

        $I->sendPOST('/object/new',[
            'address' => 'г.Волгоград, ул.50-летия ВЛКСМ',
            'name' => 'test',
            'description' => 'test',
            'price' => '5000000'
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
        ]);

        $response=$I->grabResponse();
        $response=json_decode($response);

        

        $this->testObject = EstateObject::find()->where(['user_id' => $testUser->ID])->orderBy('id DESC')->one(); //РАБОТАЕТ
        //$testObject = EstateObject::find()->where(['id' => $testUser->ID])->one(); //ПОЧЕМУ-ТО НЕ РАБОТАЕТ
    }

    public function getObjectsViaApi(\ApiTester $I)
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

        $I->sendGET('/object/get-objects');
        $I->seeResponseMatchesJsonType([
            'data' => 'array',
        ]);
    }

    public function updateObjectViaApi(\ApiTester $I)
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

        //Замена имени у объекта
        $I->sendPOST('/object/update/'.$this->testObject->id,[ //НУЖНО БРАТЬ ID ИЗ ТОЛЬКО ЧТО СОЗДАННОГО ТЕСТОВОГО ОБЪЕКТА
            'name' => 'updateTest'
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            array('result' => true)
        );
    }

    public function viewObjectViaApi(\ApiTester $I)
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

        $I->sendGET('/object/view/'.$this->testObject->id); //ТУТ ДОЛЖЕН БЫТЬ ID КАКОГО-ТО ОБЪЕКТА, ЖЕЛАТЕЛЬНО НОВОСОЗДАННОГО
        $I->seeResponseIsJson();

        //Проверка формата данных для даты в формате "гггг-мм-дд чч:мм:cc"
        Codeception\Util\JsonType::addCustomFilter('datetime', function($value) {
            return preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/',$value,$matches);
        });
        //

        $I->seeResponseMatchesJsonType([
        'object' => array(
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
            "data" => 'boolean|null'
        ),
            'images' => 'array',
            'phones' => 'array'
        ]);
    }
}
