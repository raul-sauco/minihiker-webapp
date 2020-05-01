<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "expense".
 *
 * @property int $id
 * @property int $family_id
 * @property int $program_id
 * @property int $wallet_type
 * @property double $amount
 * @property string $date
 * @property string $remarks
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Family $family
 * @property Program $program
 */
class Expense extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'expense';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['family_id', 'wallet_type', 'amount', 'date'], 'required'],
            [['family_id', 'program_id', 'wallet_type', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['amount',], 'number'],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['remarks'], 'string'],
            [['family_id'], 'exist', 'skipOnError' => true, 'targetClass' => Family::className(), 'targetAttribute' => ['family_id' => 'id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::className(), 'targetAttribute' => ['program_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'family_id' => Yii::t('app', 'Family ID'),
            'program_id' => Yii::t('app', 'Program ID'),
            'wallet_type' => Yii::t('app', 'Wallet Type'),
            'amount' => Yii::t('app', 'Amount'),
            'date' => Yii::t('app', 'Date'),
            'remarks' => Yii::t('app', 'Remarks'),
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
            TimestampBehavior::className(),
            BlameableBehavior::className(),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFamily()
    {
        return $this->hasOne(Family::className(), ['id' => 'family_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'program_id']);
    }
}
