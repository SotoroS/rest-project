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
 * @property string $password
 * @property int $age
 * @property int|null $verified
 * @property string|null $signup_token
 * @property string|null $access_token
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
            [['gender', 'phone', 'email', 'password', 'age'], 'required'],
            [['age', 'verified'], 'integer'],
            [['gender'], 'string', 'max' => 1],
            [['phone'], 'string', 'max' => 30],
            [['email', 'password'], 'string', 'max' => 256],
            [['signup_token', 'access_token'], 'string', 'max' => 13],
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
            'password' => 'Password',
            'age' => 'Age',
            'verified' => 'Verified',
            'signup_token' => 'Signup Token',
            'access_token' => 'Access Token',
        ];
    }
}
