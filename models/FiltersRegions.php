<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "filters_regions".
 *
 * @property int $id
 * @property int|null $regions_id
 * @property int|null $filters_id
 *
 * @property Filters $filters
 * @property Regions $regions
 */
class FiltersRegions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'filters_regions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['regions_id', 'filters_id'], 'integer'],
            [['filters_id'], 'exist', 'skipOnError' => true, 'targetClass' => Filters::className(), 'targetAttribute' => ['filters_id' => 'id']],
            [['regions_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['regions_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'regions_id' => 'Regions ID',
            'filters_id' => 'Filters ID',
        ];
    }

    /**
     * Gets query for [[Filters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFilter()
    {
        return $this->hasOne(Filters::className(), ['id' => 'filters_id']);
    }

    /**
     * Gets query for [[Regions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'regions_id']);
    }
}
