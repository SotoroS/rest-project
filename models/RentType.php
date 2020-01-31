<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "rent_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property EstateObject[] $estateObjects
 */
class RentType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rent_type';
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
     * Gets query for [[EstateObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEstateObjects()
    {
        return $this->hasMany(EstateObject::className(), ['rent_type_id' => 'id']);
    }
}
