<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "request_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property Filters[] $filters
 */
class RequestType extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_type';
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
     * Find region by name
     * 
     * @param name
     * 
     * @return RequestType|null
     */
    public static function findByName($name) {
        return static::findOne(['name' => $name]);
    }

    /**
     * Gets query for [[Filters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFilters()
    {
        return $this->hasMany(Filter::className(), ['request_type_id' => 'id']);
    }
}
