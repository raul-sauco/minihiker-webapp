<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client".
 *
 * @property int $id
 * @property string $name_zh
 * @property string $nickname
 * @property string $name_pinyin
 * @property string $name_en
 * @property string $birthdate
 * @property bool $is_male
 * @property bool $is_kid
 * @property int $family_role_id
 * @property string $family_role_other
 * @property string $remarks
 * @property string $phone_number
 * @property string $phone_number_2
 * @property string $email
 * @property string $wechat_id
 * @property string $openid
 * @property string $wx_session_key
 * @property int $wx_session_key_obtained_at
 * @property string $id_card_number
 * @property string $passport_number
 * @property string $passport_issue_date
 * @property string $passport_expire_date
 * @property string $passport_place_of_issue
 * @property string $passport_issuing_authority
 * @property string $passport_image
 * @property string $place_of_birth
 * @property string $dietary_restrictions
 * @property string $allergies
 * @property int $family_id
 * @property int $user_id
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $createdBy
 * @property FamilyRole $familyRole
 * @property User $updatedBy
 * @property User $user
 * @property Family $family
 * @property Family[] $families
 * @property Family[] $families0
 * @property ImportError[] $importErrors
 * @property ProgramClient[] $programClients
 * @property Program[] $programs
 * @property string $name
 * @property WxUnifiedPaymentOrder[] $wxUnifiedPaymentOrders
 */
class Client extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'client';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['birthdate', 'passport_issue_date', 'passport_expire_date'], 'safe'],
            [['is_male', 'is_kid'], 'boolean'],
            [['family_role_id', 'wx_session_key_obtained_at', 'family_id', 'user_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['remarks'], 'string'],
            [['name_zh'], 'string', 'max' => 12],
            [['nickname', 'name_pinyin', 'name_en'], 'string', 'max' => 128],
            [['family_role_other', 'email', 'wechat_id', 'openid', 'wx_session_key', 'passport_image'], 'string', 'max' => 64],
            [['phone_number', 'phone_number_2'], 'string', 'max' => 18],
            [['id_card_number', 'passport_number'], 'string', 'max' => 20],
            [['passport_place_of_issue', 'place_of_birth'], 'string', 'max' => 60],
            [['passport_issuing_authority'], 'string', 'max' => 120],
            [['birthdate', 'passport_issue_date','passport_expire_date'], 'date', 'format' => 'php:Y-m-d'],
            [['dietary_restrictions', 'allergies'], 'string', 'max' => 255],
            [['openid'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['family_role_id'], 'exist', 'skipOnError' => true, 'targetClass' => FamilyRole::class, 'targetAttribute' => ['family_role_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['family_id'], 'exist', 'skipOnError' => true, 'targetClass' => Family::class, 'targetAttribute' => ['family_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name_zh' => Yii::t('app', 'Name'),
            'nickname' => Yii::t('app', 'Nickname'),
            'name_pinyin' => Yii::t('app', 'Name Pinyin'),
            'name_en' => Yii::t('app', 'Name En'),
            'birthdate' => Yii::t('app', 'Birthdate'),
            'is_male' => Yii::t('app', 'Sex'),
            'family_role_id' => Yii::t('app', 'Family Role'),
            'family_role_other' => Yii::t('app', 'Family Role Other'),
            'remarks' => Yii::t('app', 'Remarks'),
            'phone_number' => Yii::t('app', 'Phone Number'),
            'phone_number_2' => Yii::t('app', 'Phone Number 2'),
            'email' => Yii::t('app', 'Email'),
            'wechat_id' => Yii::t('app', 'Wechat ID'),
            'openid' => Yii::t('app', 'Openid'),
            'wx_session_key' => Yii::t('app', 'Wx Session Key'),
            'wx_session_key_obtained_at' => Yii::t('app', 'Wx Session Key Obtained At'),
            'id_card_number' => Yii::t('app', 'Id Card Number'),
            'passport_number' => Yii::t('app', 'Passport Number'),
            'passport_issue_date' => Yii::t('app', 'Passport Issue Date'),
            'passport_expire_date' => Yii::t('app', 'Passport Expire Date'),
            'passport_place_of_issue' => Yii::t('app', 'Passport Place Of Issue'),
            'passport_issuing_authority' => Yii::t('app', 'Passport Issuing Authority'),
            'passport_image' => Yii::t('app', 'Passport Image'),
            'place_of_birth' => Yii::t('app', 'Place Of Birth'),
            'dietary_restrictions' => Yii::t('app', 'Dietary Restrictions'),
            'allergies' => Yii::t('app', 'Allergies'),
            'family_id' => Yii::t('app', 'Family ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \yii\base\Component::behaviors()
     */
    public function behaviors() 
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->isAttributeChanged('family_role_id')) {

            // Self adjust is_kid value on weapp update.
            if ((int)$this->family_role_id === 1) {

                $this->is_kid = true;

            } else {

                $this->is_kid = false;

            }

        }

        return parent::beforeSave($insert);
    }
    
    /**
    * Tries to find a valid name in all the possible fields and return it.
    * @return string The first valid name found in the order Chinese name -> Nickname -> English name.
    */
    public function getName(): string
    {
        if (!empty($this->name_zh)) {
            
            return $this->name_zh;
            
        }

        if (!empty($this->nickname)) {

            return $this->nickname;

        }

        if (!empty($this->name_en)) {

            return $this->name_en;

        }

        Yii::warning('Detected unnamed client with id=' . $this->id, __METHOD__);
        return Yii::t('app', 'Unnamed client');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFamilyRole()
    {
        return $this->hasOne(FamilyRole::class, ['id' => 'family_role_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFamily()
    {
        return $this->hasOne(Family::class, ['id' => 'family_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFamilies()
    {
        return $this->hasMany(Family::class, ['mother_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFamilies0()
    {
        return $this->hasMany(Family::class, ['father_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImportErrors()
    {
        return $this->hasMany(ImportError::class, ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramClients()
    {
        return $this->hasMany(ProgramClient::class, ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getPrograms()
    {
        return $this->hasMany(Program::class, ['id' => 'program_id'])->viaTable('program_client', ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWxUnifiedPaymentOrders()
    {
        return $this->hasMany(WxUnifiedPaymentOrder::class, ['client_id' => 'id']);
    }
}
