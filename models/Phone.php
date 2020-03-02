<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "phones".
 *
 * @property int $id
 * @property string|null $phone
 * @property int|null $object_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Objects $object
 */
class Phone extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'phones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['object_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['phone'], 'string', 'max' => 256],
            [['object_id'], 'exist', 'skipOnError' => true, 'targetClass' => Object::className(), 'targetAttribute' => ['object_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Phone',
            'object_id' => 'Object ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Object]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(Object::className(), ['id' => 'object_id']);
    }
}
