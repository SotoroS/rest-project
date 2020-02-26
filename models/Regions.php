<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "regions".
 *
 * @property int $id
 * @property string|null $name
 * @property float|null $lt
 * @property float|null $lg
 * @property int|null $area_id
 * @property int|null $city_id
 * @property int|null $street_id
 * @property int|null $city_area_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property FiltersRegions[] $filtersRegions
 * @property Objects[] $objects
 * @property CountryAreas $area
 * @property CityAreas $cityArea
 * @property Cities $city
 * @property Streets $street
 */
class Regions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'regions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lt', 'lg'], 'number'],
            [['area_id', 'city_id', 'street_id', 'city_area_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 256],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => CountryAreas::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['city_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => CityAreas::className(), 'targetAttribute' => ['city_area_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::className(), 'targetAttribute' => ['city_id' => 'id']],
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
            'name' => 'Name',
            'lt' => 'Lt',
            'lg' => 'Lg',
            'area_id' => 'Area ID',
            'city_id' => 'City ID',
            'street_id' => 'Street ID',
            'city_area_id' => 'City Area ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
     * Gets query for [[FiltersRegions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiltersRegions()
    {
        return $this->hasMany(FiltersRegions::className(), ['regions_id' => 'id']);
    }

    /**
     * Gets query for [[Objects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjects()
    {
        return $this->hasMany(Objects::className(), ['region_id' => 'id']);
    }

    /**
     * Gets query for [[Area]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(CountryAreas::className(), ['id' => 'area_id']);
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
     * Gets query for [[Street]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStreet()
    {
        return $this->hasOne(Streets::className(), ['id' => 'street_id']);
    }

    // ---Был необходим для создания экземпляра класса--- // 

    // /**
    //  * {@inheritdoc}
    //  */
    // public function beforeValidate() 
    // {
    //     // Check exist needed variable value
    //     if (is_null($this->regionName) 
    //         || is_null($this->cityName)
    //         || is_null($this->cityAreaName)
    //         || is_null($this->streetName)) {
    //             return false;
    //         }

    //     // Find exist Region
    //     $region = Region::findByName($this->regionName);

    //     if (is_null($region)) {
    //         $region = new Region();

    //         $region->name = $this->regionName;

    //         if (!$region->save()) {
    //             return false;
    //         }
    //     }

    //     // Find exist City
    //     $city = City::findByName($this->cityName);

    //     if (is_null($city)) {
    //         $city = new City();

    //         $city->name = $this->cityName;
    //         $city->region_id = $region->id;

    //         if (!$city->save()) {
    //             return false;
    //         }
    //     }

    //     // Find exist City Area
    //     $cityArea = CityAreas::findByName($this->cityAreaName);

    //     if (is_null($cityArea)) {
    //         $cityArea = new CityAreas();

    //         $cityArea->name = $this->cityAreaName;
    //         $cityArea->city_id = $city->id;

    //         if (!$cityArea->save()) {
    //             return false;
    //         }
    //     }

    //     // Find exist Street
    //     $street = Streets::findByName($this->streetName);

    //     if (is_null($street)) {
    //         $street = new Streets();

    //         $street->name = $this->streetName;
    //         $street->city_area_id = $cityArea->id;
    
    //         if (!$street->save()) {
    //             return false;
    //         }
    //     }

    //     // Links
    //     $this->region_id = $region->id;
    //     $this->city_id = $city->id;
    //     $this->city_area_id = $cityArea->id;
    //     $this->street_id = $street->id;

    //     return parent::beforeValidate();
    // }
}
