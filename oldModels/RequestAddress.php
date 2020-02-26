<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "request_address".
 *
 * @property int $id
 * @property int $request_object_id
 * @property int $address_id
 *
 * @property Address $address
 * @property RequestObject $requestObject
 */
class RequestAddress extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_object_id', 'address_id'], 'required'],
            [['request_object_id', 'address_id'], 'integer'],
            [['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => Address::className(), 'targetAttribute' => ['address_id' => 'id']],
            [['request_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => RequestObject::className(), 'targetAttribute' => ['request_object_id' => 'id']],
        ];
    }
 
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'request_object_id' => 'Request Object ID',
            'address_id' => 'Address ID',
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
     * Gets query for [[RequestObject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestObject()
    {
        return $this->hasOne(RequestObject::className(), ['id' => 'request_object_id']);
    }
}
