<?php
    namespace micro\models;

    use Yii;
    use yii\base\Model;

    class UserWeb extends Model
    {
        /**
         * @var string
         */
        public $email;

        /**
         * @var string
         */
        public $password;

        /**
         * @var string
         */
        public $signup_token;
      
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
              [['email', 'password','signup_token'], 'required'],
              ['email','email']
            ];
        }

        public function __construct(User $user = null)
        {
            $this->_user = $user;
        }

        /**
         * Creating and save user model
         * 
         * @return bool whether the saving succeeded (i.e. no validation errors occurred)
         */
        public function save($runValidation = true, $attributeNames = null)
        {
            $user = new User();

            $user->email = $this->email;
            $user->password = $this->password;
            $user->signup_token = $this->signup_token;
            
            if ($this->validate() && $user->save())
            {
                $this->_user = $user;
                return true;
            }
            else 
            {
                return false;
            }
        }

        /**
         * Get user by email
         * 
         * @param int $findParams - associative array "attribute => 'value'" of user model
         * 
         * @return static|null ActiveRecord instance matching the condition, or `null` if nothing matches.
         */
        public static function findOne($findParams)
        {
            return ($user = User::findOne($findParams)) ? new self($user) : null;
        }

        public function getUser()
        {
          return $this->_user;
        }

        public function setUser($user)
        {
          $this->_user = $user;
          return true;
        }
    }