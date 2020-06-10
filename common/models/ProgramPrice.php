<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "program_price".
 *
 * @property int $id
 * @property int|null $program_id
 * @property int|null $adults
 * @property int|null $kids
 * @property int|null $membership_type
 * @property int|null $price
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property User $createdBy
 * @property Program $program
 * @property mixed $namei18n
 * @property User $updatedBy
 * @property WxUnifiedPaymentOrder[] $wxUnifiedPaymentOrders
 */
class ProgramPrice extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'program_price';
    }

    /**
     * Override this to eliminate the model name on the generated forms.     *
     * @return string
     */
    public function formName(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['program_id', 'adults', 'kids', 'membership_type', 'price',
                'created_by', 'updated_by', 'created_at', 'updated_at'],
                'integer'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' =>
                User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' =>
                Program::class, 'targetAttribute' => ['program_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' =>
                User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
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

    /**
     * Get the i18n version of the model's name
     * @return string
     */
    public function getNamei18n(): string
    {
        return Yii::t('app',
            '{n,plural,=0{no kids} =1{1 kid} other{# kids}}, ' .
            '{i,plural,=0{no adults} =1{1 adult} other{# adults}}',
            ['n' => $this->kids, 'i' => $this->adults]);
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function beforeDelete(): bool
    {
        foreach ($this->wxUnifiedPaymentOrders as $wxUnifiedPaymentOrder) {
            if (!$wxUnifiedPaymentOrder->delete()) {
                Yii::error([
                    "Error deleting wx-unified-payment-order $wxUnifiedPaymentOrder->id",
                    $wxUnifiedPaymentOrder->errors
                ], __METHOD__);
            }
        }
        return parent::beforeDelete();
    }

    /**
    * {@inheritDoc}
    * @see \yii\base\Component::behaviors()
    */
    public function behaviors(): array
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
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
    public function getProgram(): ActiveQuery
    {
        return $this->hasOne(Program::class, ['id' => 'program_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[WxUnifiedPaymentOrders]].
     *
     * @return ActiveQuery
     */
    public function getWxUnifiedPaymentOrders(): ActiveQuery
    {
        return $this->hasMany(WxUnifiedPaymentOrder::class, ['price_id' => 'id']);
    }
}
