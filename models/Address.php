<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property int $id
 * @property float $lt
 * @property float $lg
 * @property int $city_id
 * @property int $street_id
 * @property int|null $region_id
 * @property int|null $city_area_id
 *
 * @property CityArea $cityArea
 * @property City $city
 * @property Region $region
 * @property Street $street
 * @property EstateObject[] $estateObjects
 * @property RequestAddress[] $requestAddresses
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lt', 'lg', 'city_id', 'street_id'], 'required'],
            [['lt', 'lg'], 'number'],
            [['city_id', 'street_id', 'region_id', 'city_area_id'], 'integer'],
            [['city_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => CityArea::className(), 'targetAttribute' => ['city_area_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['street_id'], 'exist', 'skipOnError' => true, 'targetClass' => Street::className(), 'targetAttribute' => ['street_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lt' => 'Lt',
            'lg' => 'Lg',
            'city_id' => 'City ID',
            'street_id' => 'Street ID',
            'region_id' => 'Region ID',
            'city_area_id' => 'City Area ID',
        ];
    }

    /**
     * Gets query for [[CityArea]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCityArea()
    {
        return $this->hasOne(CityArea::className(), ['id' => 'city_area_id']);
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
     * Gets query for [[Region]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }

    /**
     * Gets query for [[Street]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStreet()
    {
        return $this->hasOne(Street::className(), ['id' => 'street_id']);
    }

    /**
     * Gets query for [[EstateObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEstateObjects()
    {
        return $this->hasMany(EstateObject::className(), ['address_id' => 'id']);
    }

    /**
     * Gets query for [[RequestAddresses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestAddresses()
    {
        return $this->hasMany(RequestAddress::className(), ['address_id' => 'id']);
    }
}
