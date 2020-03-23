<?php

namespace micro\models;

use Yii;

use micro\models\RentType;

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
 * @property FiltersAddress[] $filtersAddresses
 */
class Filter extends \yii\db\ActiveRecord
{
    /**
     * @var array
     */
    public $addresses = [];

    /**
     * @var array
     */
    public $rentTypeIds;

    /**
     * @var array
     */
    public $propertyTypeIds;

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
            [['user_id', 'num_of_people', 'square_from', 'square_to', 'city_id', 'price_from', 'price_to', 'city_area_id'], 'integer'],
            [['description'], 'string'],
            [['pivot_lt', 'pivot_lg', 'radius'], 'number'],
            [['created_at', 'updated_at', 'addresses'], 'safe'],
            [['rent_type', 'property_type', 'substring'], 'string', 'max' => 256],
            [['city_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => CityArea::className(), 'targetAttribute' => ['city_area_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['family', 'pets'], 'integer', 'min' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        $isValid = true;

        $isValid = $this->validateRentTypes() ?? false;
        $isValid = $this->validatePropertyTypes() ?? false;

        return $isValid && parent::validate($attributeNames, $clearErrors);
    }

    /**
     * {{@inheritdoc}}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $dateTime = new \DateTime("", new \DateTimeZone("Europe/Kiev"));
				
            $this->updated_at = $dateTime->format('Y-m-d H:i:s');

            return true;
        } else {
            return false;
        }
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
        return $this->hasOne(CityArea::className(), ['id' => 'city_area_id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[FiltersAddresses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFilterAddresses()
    {
        return $this->hasMany(FilterAddress::className(), ['filters_id' => 'id']);
    }

    /**
     * Gets query for [[RentType]]
     * 
     * @return void \yii\db\ActiveQuery
     */
    public function getRentTypes()
    {
        $this->rentTypeIds = explode(',', $this->rent_type);

        return RentType::findAll($this->rentTypeIds);
    }

    /**
     * Gets query for [[PropertyType]]
     * 
     * @return void \yii\db\ActiveQuery
     */
    public function getPropertyTypes()
    {
        $this->propertyTypeIds = explode(',', $this->property_type);

        return PropertyType::findAll($this->propertyTypeIds);
    }

    /**
     * Validate rent types
     * 
     * @return bool is valid
     */
    private function validateRentTypes() {
        $isValid = true;
        $rentTypeIdsError = "";

        if (!is_array($this->rentTypeIds)) {
            $isValid = false;
            $this->addError('rent_type', 'Is not array.');
        } else {
            foreach ($this->rentTypeIds as $id) {
                if (is_null(RentType::findOne($id))) {
                    $rentTypeIdsError .= $id . ' ';
                }
            }

            if (!empty($rentTypeIdsError)) {
                $isValid = false;
                $this->addError('rent_type', "Not exist rent type with id $rentTypeIdsError.");
            }

            $this->rent_type = implode(",", $this->rentTypeIds);
        }

        return $isValid;
    }

    /**
     * Validate property types
     * 
     * @return bool is valid
     */
    private function validatePropertyTypes() {
        $isValid = true;
        $propertyTypeIdsError = "";

        if (!is_array($this->propertyTypeIds)) {
            $isValid = false;

            $this->addError('property_type', 'Is not array.');
        } else {
            foreach ($this->propertyTypeIds as $id) {
                if (is_null(PropertyType::findOne($id))) {
                    $propertyTypeIdsError .= $id . ' ';
                }
            }

            if (!empty($propertyTypeIdsError)) {
                $isValid = false;
            
                $this->addError('property_type', "Not exist property type with id $propertyTypeIdsError.");
            }

            $this->property_type = implode(",", $this->propertyTypeIds);
        }

        return $isValid;
    }
}
