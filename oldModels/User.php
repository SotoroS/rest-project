<?php

namespace micro\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string|null $gender
 * @property string|null $phone
 * @property string $email
 * @property string|null $password
 * @property int|null $age
 * @property int|null $verified
 * @property string|null $signup_token
 * @property string|null $access_token
 *
 * @property EstateObject[] $estateObjects
 * @property RequestObject[] $requestObjects
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['age', 'verified'], 'integer'],
            [['gender'], 'string', 'max' => 1],
            [['phone'], 'string', 'max' => 30],
            [['email', 'password', 'access_token'], 'string', 'max' => 256],
            [['signup_token'], 'string', 'max' => 13],
            ['email', 'email'],
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

    /**
     * Find user by access token
     * 
     * @param $token - access token's user
     * @param $type
     * 
     * @return User user with token user
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }
    
	/**
	 * {@inheritdoc}
	 */
	public static function findIdentity($id)
	{
		return static::findOne(['id' => $id]);
    }
    
    /**
	 * {@inheritdoc}
	 */
	public function getId()
	{
		return $this->getPrimaryKey();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthKey()
	{
		return $this->auth_key;
    }
    
    /**
	 * {@inheritdoc}
	 */
	public function validateAuthKey($authKey)
	{
		return $this->getAuthKey() === $authKey;
	}

    /**
     * Gets query for [[EstateObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEstateObjects()
    {
        return $this->hasMany(EstateObject::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[RequestObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestObjects()
    {
        return $this->hasMany(RequestObject::className(), ['user_id' => 'id']);
    }
}
