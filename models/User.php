<?php

namespace micro\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string|null $gender
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $password
 * @property string|null $access_token
 * @property int|null $age
 * @property int|null $verified
 * @property int|null $notifications
 * @property string|null $last_fetch
 * @property int|null $premium
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $fcmToken
 * @property string|null $deviceType
 *
 * @property Filters[] $filters
 * @property Objects[] $objects
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['age', 'verified', 'notifications', 'premium', 'status'], 'integer'],
            // ['age', function($attribute,$params) {
            //     if (($this->attribute < 0) && ($this->attribute != NULL))
            //     {
            //         $this->addError($attribute, 'Age must be greater than 0.');
            //     }
            // }],
            [['last_fetch', 'created_at', 'updated_at'], 'safe'],
            [['gender'], 'string', 'max' => 1],
            // ['gender', function($attribute,$params) {
            //     echo print_r($this->attribute,true);
            //     if ((($this->attribute != 'F') || ($this->attribute != 'M')) && ($this->attribute != NULL))
            //     {
            //         $this->addError($attribute, 'Gender must be F or M.');
            //     }
            // }],
            [['phone'], 'string', 'max' => 30],
            [['email', 'password', 'access_token', 'signup_token', 'fcmToken', 'deviceType'], 'string', 'max' => 256],
            ['email','email']
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
            'access_token' => 'Access Token',
            'signup_token' => 'Signup Token',
            'age' => 'Age',
            'verified' => 'Verified',
            'notifications' => 'Notifications',
            'last_fetch' => 'Last Fetch',
            'premium' => 'Premium',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'fcmToken' => 'Fcm Token',
            'deviceType' => 'Device Type',
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
     * Gets query for [[Filters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFilters()
    {
        return $this->hasMany(Filters::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Objects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjects()
    {
        return $this->hasMany(Objects::className(), ['user_id' => 'id']);
    }
}
