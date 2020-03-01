<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property int $id
 * @property float|null $lt
 * @property float|null $lg
 * @property int|null $region_id
 * @property int|null $city_id
 * @property int|null $city_area_id
 * @property int|null $street_id
 *
 * @property CityAreas $cityArea
 * @property Cities $city
 * @property Regions $region
 * @property Streets $street
 * @property FiltersAddress[] $filtersAddresses
 * @property Objects[] $objects
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
            [['lt', 'lg'], 'number'],
            [['region_id', 'city_id', 'city_area_id', 'street_id'], 'integer'],
            [['city_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => CityAreas::className(), 'targetAttribute' => ['city_area_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['street_id'], 'exist', 'skipOnError' => true, 'targetClass' => Streets::className(), 'targetAttribute' => ['street_id' => 'id']],
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
            'region_id' => 'Region ID',
            'city_id' => 'City ID',
            'city_area_id' => 'City Area ID',
            'street_id' => 'Street ID',
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
        $region = Regions::findByName($this->regionName);

        if (is_null($region)) {
            $region = new Regions();

            $region->name = $this->regionName;

            if (!$region->save()) {
                return false;
            }
        }

        // Find exist City
        $city = Cities::findByName($this->cityName);

        if (is_null($city)) {
            $city = new Cities();

            $city->name = $this->cityName;
            $city->region_id = $region->id;

            if (!$city->save()) {
                return false;
            }
        }

        // Find exist City Area
        $cityArea = CityAreas::findByName($this->cityAreaName);

        if (is_null($cityArea)) {
            $cityArea = new CityAreas();

            $cityArea->name = $this->cityAreaName;
            $cityArea->city_id = $city->id;

            if (!$cityArea->save()) {
                return false;
            }
        }

        // Find exist Street
        $street = Streets::findByName($this->streetName);

        if (is_null($street)) {
            $street = new Streets();

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
     * Gets query for [[Region]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }

    /**
     * Gets query for [[Street]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStreet()
    {
        return $this->hasOne(Streets::className(), ['id' => 'street_id']);
    }

    /**
     * Gets query for [[FiltersAddresses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiltersAddresses()
    {
        return $this->hasMany(FiltersAddress::className(), ['address_id' => 'id']);
    }

    /**
     * Gets query for [[Objects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjects()
    {
        return $this->hasMany(Objects::className(), ['address_id' => 'id']);
    }
}
