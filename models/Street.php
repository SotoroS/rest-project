<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "streets".
 *
 * @property int $id
 * @property string $name
 * @property int|null $city_area_id
 *
 * @property Address[] $addresses
 * @property CityAreas $cityArea
 */
class Street extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'streets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['city_area_id'], 'integer'],
            [['name'], 'string', 'max' => 256],
            [['city_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => CityAreas::className(), 'targetAttribute' => ['city_area_id' => 'id']],
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
            'city_area_id' => 'City Area ID',
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
        return $this->hasMany(Address::className(), ['street_id' => 'id']);
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
}
