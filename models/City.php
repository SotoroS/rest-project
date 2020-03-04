<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "cities".
 *
 * @property int $id
 * @property string $name
 * @property int|null $region_id
 *
 * @property Address[] $addresses
 * @property Regions $region
 * @property CityAreas[] $cityAreas
 * @property Filters[] $filters
 * @property Metro[] $metros
 * @property Objects[] $objects
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cities';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['region_id'], 'integer'],
            [['name'], 'string', 'max' => 256],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
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
            'region_id' => 'Region ID',
        ];
    }

    /**
     * Find city by name
     * 
     * @param - name
     * 
     * @return City|null 
     */
    public static function findByName($name) {
        return static::findOne(['name' => $name]);
    }

    /**
     * Gets query for [[Addresses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAddresses()
    {
        return $this->hasMany(Address::className(), ['city_id' => 'id']);
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
     * Gets query for [[CityAreas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCityAreas()
    {
        return $this->hasMany(CityArea::className(), ['city_id' => 'id']);
    }

    /**
     * Gets query for [[Filters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFilters()
    {
        return $this->hasMany(Filter::className(), ['city_id' => 'id']);
    }

    /**
     * Gets query for [[Metros]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMetros()
    {
        return $this->hasMany(Metro::className(), ['city_id' => 'id']);
    }

    /**
     * Gets query for [[Objects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjects()
    {
        return $this->hasMany(Object::className(), ['city_id' => 'id']);
    }
}
