<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "filters".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $num_of_people
 * @property int|null $family
 * @property int|null $pets
 * @property int|null $request_type_id
 * @property int|null $square_from
 * @property int|null $square_to
 * @property int|null $city_id
 * @property int|null $price_from
 * @property int|null $price_to
 * @property string|null $description
 * @property float|null $pivot_lt
 * @property float|null $pivot_lg
 * @property float|null $radius
 * @property int|null $city_area_id
 * @property string|null $rent_type
 * @property string|null $property_type
 * @property string|null $substring
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property CityAreas $cityArea
 * @property Cities $city
 * @property Users $user
 * @property FiltersRegions[] $filtersRegions
 */
class Filters extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'filters';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'num_of_people', 'family', 'pets', 'request_type_id', 'square_from', 'square_to', 'city_id', 'price_from', 'price_to', 'city_area_id'], 'integer'],
            [['description'], 'string'],
            [['pivot_lt', 'pivot_lg', 'radius'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['rent_type', 'property_type', 'substring'], 'string', 'max' => 256],
            [['city_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => CityAreas::className(), 'targetAttribute' => ['city_area_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'num_of_people' => 'Num Of People',
            'family' => 'Family',
            'pets' => 'Pets',
            'request_type_id' => 'Request Type ID',
            'square_from' => 'Square From',
            'square_to' => 'Square To',
            'city_id' => 'City ID',
            'price_from' => 'Price From',
            'price_to' => 'Price To',
            'description' => 'Description',
            'pivot_lt' => 'Pivot Lt',
            'pivot_lg' => 'Pivot Lg',
            'radius' => 'Radius',
            'city_area_id' => 'City Area ID',
            'rent_type' => 'Rent Type',
            'property_type' => 'Property Type',
            'substring' => 'Substring',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Find request object by id
     *
     * @return \yii\db\BaseActiveRecord
     */
    public static function findByIdentity($id)
    {
        return static::findOne($id);
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[FiltersRegions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiltersRegions()
    {
        return $this->hasMany(FiltersRegions::className(), ['filters_id' => 'id']);
    }
}
