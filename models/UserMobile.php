<?php

namespace micro\models;

use Yii;

use yii\base\Model;

use micro\models\User;

/**
 * Class UserMobile
 */
class UserMobile extends Model
{
    /**
     * @var string
     */
    public $deviceType;

    /**
     * @var string
     */
    public $fcmToken;

    /**
     * @var User
     */
    private $_user = null;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['deviceType', 'fcmToken'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(User $user = null) {
        $this->_user = $user;
    }

    /**
     * Creating and save user model
     * 
     * @return bool whether the saving succeeded (i.e. no validation errors occurred)
     */
    public function save()
    {
        if (is_null($this->_user)) {
            $this->_user = new User();
        }

        $this->_user->deviceType = $this->deviceType;
        $this->_user->fcmToken = $this->fcmToken;

        if ($this->validate() && $this->_user->save()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get user by id
     * 
     * @param int $id - id of user model
     * 
     * @return static|null ActiveRecord instance matching the condition, or `null` if nothing matches.
     */
    public static function findOne($id)
    {
        return ($user = User::findOne($id)) ? new self($user) : null;
    }
}
