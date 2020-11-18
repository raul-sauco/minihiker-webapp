<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "program_family".
 *
 * @property int $program_id
 * @property int $family_id
 * @property int $cost
 * @property int $discount
 * @property int $final_cost
 * @property string $remarks
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Program $program
 * @property ActiveQuery $payments
 * @property Family $family
 * @property User $createdBy
 * @property User $updatedBy
 */
class ProgramFamily extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'program_family';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['program_id', 'family_id'], 'required'],
            [['program_id', 'family_id', 'cost', 'discount', 'final_cost', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['remarks'], 'string'],
            [['program_id', 'family_id'], 'unique', 'targetAttribute' => ['program_id', 'family_id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['program_id' => 'id']],
            [['family_id'], 'exist', 'skipOnError' => true, 'targetClass' => Family::class, 'targetAttribute' => ['family_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'program_id' => Yii::t('app', 'Program ID'),
            'family_id' => Yii::t('app', 'Family ID'),
            'cost' => Yii::t('app', 'Cost'),
            'discount' => Yii::t('app', 'Discount'),
            'final_cost' => Yii::t('app', 'Final Cost'),
            'remarks' => Yii::t('app', 'Remarks'),
            'status' => Yii::t('app', 'Status'),
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
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProgram(): ActiveQuery
    {
        return $this->hasOne(Program::class, ['id' => 'program_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFamily(): ActiveQuery
    {
        return $this->hasOne(Family::class, ['id' => 'family_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPayments(): ActiveQuery
    {
        return $this->program->getPayments()
            ->where(['family_id' => $this->family_id]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCreatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
