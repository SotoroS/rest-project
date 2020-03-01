<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "filters_address".
 *
 * @property int $id
 * @property int|null $address_id
 * @property int|null $filters_id
 *
 * @property Address $address
 * @property Filters $filters
 */
class FiltersAddress extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'filters_address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address_id', 'filters_id'], 'integer'],
            [['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => Address::className(), 'targetAttribute' => ['address_id' => 'id']],
            [['filters_id'], 'exist', 'skipOnError' => true, 'targetClass' => Filters::className(), 'targetAttribute' => ['filters_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address_id' => 'Address ID',
            'filters_id' => 'Filters ID',
        ];
    }

    /**
     * Gets query for [[Address]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(Address::className(), ['id' => 'address_id']);
    }

    /**
     * Gets query for [[Filters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFilters()
    {
        return $this->hasOne(Filters::className(), ['id' => 'filters_id']);
    }
}
