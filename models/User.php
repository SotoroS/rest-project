<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "User".
 *
 * @property int $id
 * @property string $gender
 * @property string $phone
 * @property string $email
 * @property int $age
 * @property int|null $verified
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'User';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gender', 'phone', 'email', 'age'], 'required'],
            [['age', 'verified'], 'integer'],
            [['gender'], 'string', 'max' => 1],
            [['phone'], 'string', 'max' => 30],
            [['email'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gender' => 'Gender',
            'phone' => 'Phone',
            'email' => 'Email',
            'age' => 'Age',
            'verified' => 'Verified',
        ];
    }
}
