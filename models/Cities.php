<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property string $name
 * @property int $region_id
 *
 * @property Address[] $addresses
 * @property Region $region
 * @property CityArea[] $cityAreas
 * @property RequestObject[] $requestObjects
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
     * Gets query for [[Regions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegions()
    {
        return $this->hasMany(Regions::className(), ['city_id' => 'id']);
    }

    /**
     * Gets query for [[Filters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFilter()
    {
        return $this->hasOne(Filters::className(), ['city_id' => 'id']);
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
     * Gets query for [[Objects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjects()
    {
        return $this->hasMany(Objects::className(), ['city_id' => 'id']);
    }
}
