<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "estate_object".
 *
 * @property int $id
 * @property int $address_id
 * @property int $rent_type_id
 * @property int $property_type_id
 * @property int $building_type_id
 * @property int|null $metro_id
 * @property string $name
 * @property string $description
 * @property float $price
 * @property string|null $url
 * @property float $square
 * @property float $kitchen_square
 * @property int|null $level
 * @property int|null $rooms
 * @property float $ln
 * @property float $lt
 * @property int|null $internal
 * @property int $agent
 * @property int|null $published
 * @property int|null $user_id
 *
 * @property Address $address
 * @property BuildingType $buildingType
 * @property Metro $metro
 * @property PropertyType $propertyType
 * @property RentType $rentType
 * @property User $user
 */
class EstateObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'estate_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address_id', 'rent_type_id', 'property_type_id', 'building_type_id', 'name', 'description', 'price', 'square', 'kitchen_square', 'ln', 'lt', 'agent'], 'required'],
            [['address_id', 'rent_type_id', 'property_type_id', 'building_type_id', 'metro_id', 'level', 'rooms', 'internal', 'agent', 'published', 'user_id'], 'integer'],
            [['description'], 'string'],
            [['price', 'square', 'kitchen_square', 'ln', 'lt'], 'number'],
            [['name', 'url'], 'string', 'max' => 256],
            [['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => Address::className(), 'targetAttribute' => ['address_id' => 'id']],
            [['building_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => BuildingType::className(), 'targetAttribute' => ['building_type_id' => 'id']],
            [['metro_id'], 'exist', 'skipOnError' => true, 'targetClass' => Metro::className(), 'targetAttribute' => ['metro_id' => 'id']],
            [['property_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => PropertyType::className(), 'targetAttribute' => ['property_type_id' => 'id']],
            [['rent_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => RentType::className(), 'targetAttribute' => ['rent_type_id' => 'id']],
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
            'address_id' => 'Address ID',
            'rent_type_id' => 'Rent Type ID',
            'property_type_id' => 'Property Type ID',
            'building_type_id' => 'Building Type ID',
            'metro_id' => 'Metro ID',
            'name' => 'Name',
            'description' => 'Description',
            'price' => 'Price',
            'url' => 'Url',
            'square' => 'Square',
            'kitchen_square' => 'Kitchen Square',
            'level' => 'Level',
            'rooms' => 'Rooms',
            'ln' => 'Ln',
            'lt' => 'Lt',
            'internal' => 'Internal',
            'agent' => 'Agent',
            'published' => 'Published',
            'user_id' => 'User ID',
        ];
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
        return $this->hasOne(PropertyType::className(), ['id' => 'property_type_id']);
    }

    /**
     * Gets query for [[RentType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRentType()
    {
        return $this->hasOne(RentType::className(), ['id' => 'rent_type_id']);
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
