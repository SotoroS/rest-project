<?php

namespace micro\models;

use Yii;
use app\models\Region;
use app\models\City;
use app\models\CityArea;
use app\models\Street;

/**
 * This is the model class for table "Address".
 *
 * @property int $id
 * @property float $lt
 * @property float $lg
 * @property int $city_id
 * @property int $street_id
 * @property int|null $region_id
 * @property int|null $city_area_id
 * 
 * @property string $regionName
 * @property string $cityName
 * @property string $cityAreaName
 * @property string $street
 */
class Address extends \yii\db\ActiveRecord
{
    public $regionName;
    public $cityName;
    public $cityAreaName;
    public $streetName;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Address';
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
     * Before save model function
     * 
     * Save model regin, city, city_area, street
     */
    public function beforeValidate() 
    {
        // Save region
        $region = new Region();

        $region->name = $this->regionName;

        if ($region->save()) {
            // Save city
            // TODO: check exist
            $city = new City();

            $city->name = $this->cityName;
            $city->region_id = $region->id;

            if ($city->save()) {
                // Save city area
                // TODO: check exist
                $cityArea = new CityArea();

                $cityArea->name = $this->cityAreaName;
                $cityArea->city_id = $city->id;
                
                if ($cityArea->save()) {
                    // Save street
                    // TODO: check exist
                    $street = new Street();

                    $street->name = $this->streetName;
                    $street->city_area_id = $cityArea->id;
                    
                    if ($street->save()) {
                        // Linking
                        // TODO: check exist
                        $this->region_id = $region->id;
                        $this->city_id = $city->id;
                        $this->city_area_id = $cityArea->id;
                        $this->street_id = $street->id;

                        return parent::beforeValidate();
                    } else {
                        // TODO: Delete model
                        return false;
                    }
                } else {
                    // TODO: Delete model
                    return false;
                }
            } else {
                // TODO: Delete model
                return false;
            }
        } else {
            // TODO: Delete model
            return false;
        }            
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
}