<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "regions".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $area_id
 * @property int|null $city_id
 * @property int|null $street_id
 * @property int|null $city_area_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Address[] $addresses
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
            'area_id' => 'Area ID',
            'city_id' => 'City ID',
            'street_id' => 'Street ID',
            'city_area_id' => 'City Area ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Find region by name
     * 
     * @param name
     * 
     * @return Region|null
     */
    public static function findByName($name) {
        return static::find(['name' => $name])->one();
    }

    /**
     * Gets query for [[Addresses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAddresses()
    {
        return $this->hasMany(Address::className(), ['region_id' => 'id']);
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
}
