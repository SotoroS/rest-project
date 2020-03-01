<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "cities".
 *
 * @property int $id
 * @property string $name
 *
 * @property Address[] $addresses
 * @property CityAreas[] $cityAreas
 * @property Filters[] $filters
 * @property Objects[] $objects
 * @property Regions[] $regions
 */
class Cities extends \yii\db\ActiveRecord
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
            [['name'], 'string', 'max' => 256],
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
        ];
    }

    /**
     * Find city by name
     * 
     * @param - name
     * 
     * @return Cities|null 
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
        return $this->hasMany(Address::className(), ['city_id' => 'id']);
    }

    /**
     * Gets query for [[CityAreas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCityAreas()
    {
        return $this->hasMany(CityAreas::className(), ['city_id' => 'id']);
    }

    /**
     * Gets query for [[Filters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFilters()
    {
        return $this->hasMany(Filters::className(), ['city_id' => 'id']);
    }

    /**
     * Gets query for [[Objects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjects()
    {
        return $this->hasMany(Objects::className(), ['city_id' => 'id']);
    }

    /**
     * Gets query for [[Regions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegions()
    {
        return $this->hasMany(Regions::className(), ['city_id' => 'id']);
    }
}
