<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "contact".
 *
 * @property int $id
 * @property int $supplier_id
 * @property string $name
 * @property string $role
 * @property string $phone
 * @property string $wechat_id
 * @property string $email
 * @property string $remarks
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $createdBy
 * @property Supplier $supplier
 * @property User $updatedBy
 */
class Contact extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contact';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['supplier_id', 'name'], 'required'],
            [['supplier_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['remarks'], 'string'],
            [['name', 'role'], 'string', 'max' => 128],
            [['phone'], 'string', 'max' => 18],
            [['wechat_id', 'email'], 'string', 'max' => 64],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'supplier_id' => Yii::t('app', 'Supplier ID'),
            'name' => Yii::t('app', 'Name'),
            'role' => Yii::t('app', 'Role'),
            'phone' => Yii::t('app', 'Phone'),
            'wechat_id' => Yii::t('app', 'Wechat ID'),
            'email' => Yii::t('app', 'Email'),
            'remarks' => Yii::t('app', 'Remarks'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
    * {@inheritDoc}
    * @see \yii\base\Component::behaviors()
    */
    public function behaviors()
    {
        return [
            BlameableBehavior::className(),
            TimestampBehavior::className(),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}
