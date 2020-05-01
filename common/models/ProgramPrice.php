<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "program_price".
 *
 * @property int $id
 * @property int $program_id
 * @property int $adults
 * @property int $kids
 * @property int $membership_type
 * @property int $price
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $createdBy
 * @property Program $program
 * @property User $updatedBy
 */
class ProgramPrice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_price';
    }

    /**
     * Override this to eliminate the model name on the generated forms.
     *
     * @return string
     */
    public function formName()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_id', 'adults', 'kids', 'membership_type', 'price', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::className(), 'targetAttribute' => ['program_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'program_id' => Yii::t('app', 'Program ID'),
            'adults' => Yii::t('app', 'Adults'),
            'kids' => Yii::t('app', 'Kids'),
            'membership_type' => Yii::t('app', 'Membership Type'),
            'price' => Yii::t('app', 'Price'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function getNamei18n()
    {
        return Yii::t('app',
            '{n,plural,=0{no kids} =1{1 kid} other{# kids}}, {i,plural,=0{no adults} =1{1 adult} other{# adults}}',
            ['n' => $this->kids, 'i' => $this->adults]);
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
    public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'program_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

}
