<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Street".
 *
 * @property int $id
 * @property string $name
 * @property int $city_area_id
 */
class Street extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Street';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'city_area_id'], 'required'],
            [['city_area_id'], 'integer'],
            [['name'], 'string', 'max' => 256],
            [['city_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => CityArea::className(), 'targetAttribute' => ['city_area_id' => 'id']],
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
}
