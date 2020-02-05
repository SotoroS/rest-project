<?php

namespace micro\models;

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
    public $regionName = null;
    public $cityName = null;
    public $cityAreaName = null;
    public $streetName = null;
 
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
            [['regionName', 'cityName', 'cityAreaName', 'streetName'], 'string'],
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
     * {@inheritdoc}
     */
    public function beforeValidate() 
    {
        // Check exist needed variable value
        if (is_null($this->regionName) 
            || is_null($this->cityName)
            || is_null($this->cityAreaName)
            || is_null($this->streetName)) {
                return false;
            }

        // Find exist Region
        $region = Region::findByName($this->regionName);

        if (is_null($region)) {
            $region = new Region();

            $region->name = $this->regionName;

            if (!$region->save()) {
                return false;
            }
        }

        // Find exist City
        $city = City::findByName($this->cityName);

        if (is_null($city)) {
            $city = new City();

            $city->name = $this->cityName;
            $city->region_id = $region->id;

            if (!$city->save()) {
                return false;
            }
        }

        // Find exist City Area
        $cityArea = CityArea::findByName($this->cityAreaName);

        if (is_null($cityArea)) {
            $cityArea = new CityArea();

            $cityArea->name = $this->cityAreaName;
            $cityArea->city_id = $city->id;

            if (!$cityArea->save()) {
                return false;
            }
        }

        // Find exist Street
        $street = Street::findByName($this->streetName);

        if (is_null($street)) {
            $street = new Street();

            $street->name = $this->streetName;
            $street->city_area_id = $cityArea->id;
    
            if (!$street->save()) {
                return false;
            }
        }

        // Links
        $this->region_id = $region->id;
        $this->city_id = $city->id;
        $this->city_area_id = $cityArea->id;
        $this->street_id = $street->id;

        return parent::beforeValidate();
    }

    /**
     * Find address by lt, lg
     *
     * @return \yii\db\BaseActiveRecord
     */
    public static function findByCoordinates($lt, $lg) {
        return static::findOne(['lt' => $lt, 'lg' => $lg]);
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
