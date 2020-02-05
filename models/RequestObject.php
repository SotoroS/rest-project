<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "request_object".
 *
 * @property int $id
 * @property int $user_id
 * @property int $num_of_people
 * @property int $family
 * @property int $pets
 * @property int $request_type_id
 * @property int $square_from
 * @property int $square_to
 * @property int $city_id
 * @property int $price_from
 * @property int $price_to
 * @property string $description
 * @property float|null $pivot_lt
 * @property float|null $pivot_lg
 * @property float $radius
 *
 * @property array $addresses
 * 
 * @property RequestAddress[] $requestAddresses
 * @property City $city
 * @property RequestType $requestType
 * @property User $user
 */
class RequestObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'num_of_people', 'family', 'pets', 'request_type_id', 'square_from', 'square_to', 'city_id', 'price_from', 'price_to', 'description', 'radius'], 'required'],
            [['user_id', 'num_of_people', 'family', 'pets', 'request_type_id', 'square_from', 'square_to', 'city_id', 'price_from', 'price_to'], 'integer'],
            [['description'], 'string'],
            [['pivot_lt', 'pivot_lg', 'radius'], 'number'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['request_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => RequestType::className(), 'targetAttribute' => ['request_type_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'num_of_people' => 'Num Of People',
            'family' => 'Family',
            'pets' => 'Pets',
            'request_type_id' => 'Request Type ID',
            'square_from' => 'Square From',
            'square_to' => 'Square To',
            'city_id' => 'City ID',
            'price_from' => 'Price From',
            'price_to' => 'Price To',
            'description' => 'Description',
            'pivot_lt' => 'Pivot Lt',
            'pivot_lg' => 'Pivot Lg',
            'radius' => 'Radius',
        ];
    }

    /**
     * Find request object by id
     *
     * @return \yii\db\BaseActiveRecord
     */
    public static function findByIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Gets query for [[RequestAddresses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestAddresses()
    {
        return $this->hasMany(RequestAddress::className(), ['request_object_id' => 'id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * Gets query for [[RequestType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestType()
    {
        return $this->hasOne(RequestType::className(), ['id' => 'request_type_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
