<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "objects".
 *
 * @property int $id
 * @property int|null $address_id
 * @property int|null $building_type_id
 * @property int|null $rent_type
 * @property int|null $property_type
 * @property int|null $metro_id
 * @property string $name
 * @property string $description
 * @property float $price
 * @property string|null $url
 * @property int|null $user_id
 * @property int|null $city_id
 * @property int|null $city_area_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $data
 *
 * @property Images[] $images
 * @property Address $address
 * @property BuildingType $buildingType
 * @property CityAreas $cityArea
 * @property Cities $city
 * @property Metro $metro
 * @property PropertyType $propertyType
 * @property RentType $rentType
 * @property Users $user
 * @property Phones[] $phones
 */
class Object extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'objects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address_id', 'building_type_id', 'rent_type', 'property_type', 'metro_id', 'user_id', 'city_id', 'city_area_id'], 'integer'],
            [['name', 'description', 'price'], 'required'],
            [['description', 'url', 'data'], 'string'],
            [['price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 256],
            [['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => Address::className(), 'targetAttribute' => ['address_id' => 'id']],
            [['building_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => BuildingType::className(), 'targetAttribute' => ['building_type_id' => 'id']],
            [['city_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => CityAreas::className(), 'targetAttribute' => ['city_area_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['metro_id'], 'exist', 'skipOnError' => true, 'targetClass' => Metro::className(), 'targetAttribute' => ['metro_id' => 'id']],
            [['property_type'], 'exist', 'skipOnError' => true, 'targetClass' => PropertyType::className(), 'targetAttribute' => ['property_type' => 'id']],
            [['rent_type'], 'exist', 'skipOnError' => true, 'targetClass' => RentType::className(), 'targetAttribute' => ['rent_type' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address_id' => 'Address ID',
            'building_type_id' => 'Building Type ID',
            'rent_type' => 'Rent Type',
            'property_type' => 'Property Type',
            'metro_id' => 'Metro ID',
            'name' => 'Name',
            'description' => 'Description',
            'price' => 'Price',
            'url' => 'Url',
            'user_id' => 'User ID',
            'city_id' => 'City ID',
            'city_area_id' => 'City Area ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'data' => 'Data',
        ];
    }

    /**
     * Find estate object by id
     *
     * @return \yii\db\BaseActiveRecord
     */
    public static function findByIdentity($id)
    {
        return static::findOne($id);
    }


    /**
     * Gets query for [[Images]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(Images::className(), ['object_id' => 'id']);
    }

    /**
     * Gets query for [[Address]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(Address::className(), ['id' => 'address_id']);
    }

    /**
     * Gets query for [[BuildingType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBuildingType()
    {
        return $this->hasOne(BuildingType::className(), ['id' => 'building_type_id']);
    }

    /**
     * Gets query for [[CityArea]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCityArea()
    {
        return $this->hasOne(CityAreas::className(), ['id' => 'city_area_id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(Cities::className(), ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Metro]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMetro()
    {
        return $this->hasOne(Metro::className(), ['id' => 'metro_id']);
    }

    /**
     * Gets query for [[PropertyType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyType()
    {
        return $this->hasOne(PropertyType::className(), ['id' => 'property_type']);
    }

    /**
     * Gets query for [[RentType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRentType()
    {
        return $this->hasOne(RentType::className(), ['id' => 'rent_type']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Phones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhones()
    {
        return $this->hasMany(Phones::className(), ['object_id' => 'id']);
    }
}
