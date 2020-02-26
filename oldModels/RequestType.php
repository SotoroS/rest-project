<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "request_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property RequestObject[] $requestObjects
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
     * Gets query for [[RequestObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestObjects()
    {
        return $this->hasMany(RequestObject::className(), ['request_type_id' => 'id']);
    }
}
