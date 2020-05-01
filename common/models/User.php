<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property string $access_token
 * @property string $avatar
 * @property string $name_zh
 * @property string $name_pinyin
 * @property string $name_en
 * @property string $birthdate
 * @property boolean $is_male
 * @property string $id_card_number
 * @property string $passport_number
 * @property string $passport_issue_date
 * @property string $passport_expire_date
 * @property string $passport_place_of_issue
 * @property string $passport_issuing_authority
 * @property string $place_of_birth
 * @property integer $user_type
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * User type admin, application term for a super-user, this user can do
     * and undo pretty much anything. Use with care.
     */
    const TYPE_ADMIN = 1;

    /**
     * User type user, regular application user, can create/update most of
     * the data, not delete or modify users.
     */
    const TYPE_USER = 2;

    /**
     * Suspended User, the user won't have access to any of the functionality,
     * we are just keeping the user's information in the record, otherwise is
     * as good as a deleted user.
     */
    const TYPE_SUSPENDED = 3;

    /**
     * User type client, regular client account.
     */
    const TYPE_CLIENT = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'user_type'], 'required'],
            [['birthdate', 'passport_issue_date', 'passport_expire_date'], 'date', 'format' => 'php:Y-m-d'],
            [['is_male'], 'boolean'],
            // [['created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['user_type',], 'integer'],
            [['username', 'password', 'avatar'], 'string', 'max' => 64],
            [['name_zh'], 'string', 'max' => 12],
            [['name_pinyin', 'name_en'], 'string', 'max' => 128],
            [['id_card_number', 'passport_number'], 'string', 'max' => 20],
            [['passport_place_of_issue', 'place_of_birth'], 'string', 'max' => 60],
            [['passport_issuing_authority'], 'string', 'max' => 120],
            [['username'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'access_token' => Yii::t('app', 'Access Token'),
            'avatar' => Yii::t('app', 'Avatar'),
            'name_zh' => Yii::t('app', 'Name Zh'),
            'name_pinyin' => Yii::t('app', 'Name Pinyin'),
            'name_en' => Yii::t('app', 'English name'),
            'birthdate' => Yii::t('app', 'Birthdate'),
            'is_male' => Yii::t('app', 'Is Male'),
            'id_card_number' => Yii::t('app', 'Id Card Number'),
            'passport_number' => Yii::t('app', 'Passport Number'),
            'passport_issue_date' => Yii::t('app', 'Passport Issue Date'),
            'passport_expire_date' => Yii::t('app', 'Passport Expire Date'),
            'passport_place_of_issue' => Yii::t('app', 'Passport Place Of Issue'),
            'passport_issuing_authority' => Yii::t('app', 'Passport Issuing Authority'),
            'place_of_birth' => Yii::t('app', 'Place Of Birth'),
            'user_type' => Yii::t('app', 'User type'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
    	return [
    		BlameableBehavior::className(),
    		TimestampBehavior::className(),
    	];
    }
    
    /**
     * Finds an identity by the given ID.
     *
     * @param string|integer $id the ID to be looked for
     * @return yii\web\IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
    	return static::findOne($id);
    }
    
    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return yii\web\IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
    	return static::findOne(['access_token' => $token]);
    }
    
    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUserName($username)
    {
    	return static::findOne(['username' => $username]);
    }
    
    /**
     * Validates the value introduced by the user against the user's password hash
     * stored on the database.
     *
     * @param  string  $password password hash to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
    	return \Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }
    
    /**
     * @return int|string current user ID
     */
    public function getId()
    {
    	return $this->id;
    }
    
    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
    	return $this->auth_key;
    }
    
    /**
     * @param string $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
    	return $this->getAuthKey() === $authKey;
    }

    /**
     * {@inheritDoc}
     * @see \yii\db\BaseActiveRecord::beforeSave()
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		if ($this->isNewRecord)
    		{
    			$this->auth_key = \Yii::$app->security->generateRandomString();
    			$this->access_token = \Yii::$app->security->generateRandomString();
    			$this->password = \Yii::$app->security->generatePasswordHash($this->password);
    		} else {
    			if ($this->isAttributeChanged('password' , false))
    			{
    				$this->password = \Yii::$app->security->generatePasswordHash($this->password);
    			}
    		}
    		return true;
    	}
    	return false;
    }

    /**
     * {@inheritDoc}
     * @see \yii\db\BaseActiveRecord::afterSave()
     * @throws \Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        // Revoke all this users 
        if ($insert) {
            // Is a new record, assign roles directly
            $this->assignRoles();
        } else {
            // Is an update, revoke all previous roles
            Yii::$app->authManager->revokeAll($this->id);
            
            // Assign roles
            $this->assignRoles();
        }
        
    }
    
    /**
     * Revoke all this users privileges to prevent accidentally assigning
     * them to another user with the same ID that may be inserted later
     * {@inheritDoc}
     * @see \yii\db\BaseActiveRecord::afterDelete()
     */
    public function afterDelete()
    {
        // Revoke all roles assigned to the user
        Yii::$app->authManager->revokeAll($this->id);
        
        // Call the parent implementation
        parent::afterDelete();
    }

    /**
     * Assign the roles corresponding to this user based on user type.
     *
     * @throws \Exception
     */
    protected function assignRoles ()
    {
        // Get a handle to the auth manager
        $auth = Yii::$app->authManager;
        
        if (intval($this->user_type) === self::TYPE_ADMIN) {

            Yii::trace('Assigning role admin to user ' . $this->id, __METHOD__);
            $auth->assign($auth->getRole('admin'), $this->id);

        } else if (intval($this->user_type) === User::TYPE_USER) {

            Yii::trace('Assigning role user to user ' . $this->id, __METHOD__);
            $auth->assign($auth->getRole('user'), $this->id);

        } else {

            Yii::trace('Not assigning any role to user ' . $this->id, __METHOD__);

        }
    }
}
