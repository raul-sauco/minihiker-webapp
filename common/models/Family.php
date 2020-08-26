<?php

namespace common\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "family".
 *
 * @property int $id
 * @property string $name
 * @property string|null $avatar
 * @property string $serial_number
 * @property string $category
 * @property string $membership_date
 * @property string $phone
 * @property string $wechat
 * @property string $address
 * @property string $place_of_residence
 * @property string $remarks
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Client[] $clients
 * @property Expense[] $expenses
 * @property User $createdBy
 * @property User $updatedBy
 * @property ImportError[] $importErrors
 * @property Payment[] $payments
 * @property ProgramFamily[] $programFamilies
 * @property Program[] $programs
 * @property WxUnifiedPaymentOrder[] $wxUnifiedPaymentOrders
 */
class Family extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'family';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['membership_date'], 'date', 'format' => 'php:Y-m-d'],
            [['address', 'remarks'], 'string'],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 12],
            [['serial_number'], 'string', 'max' => 7],
            [['avatar', 'category', 'wechat', 'place_of_residence'], 'string', 'max' => 64],
            [['phone'], 'string', 'max' => 18],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'avatar' => Yii::t('app', 'Avatar'),
            'serial_number' => Yii::t('app', 'Serial Number'),
            'category' => Yii::t('app', 'Category'),
            'membership_date' => Yii::t('app', 'Membership Date'),
            'phone' => Yii::t('app', 'Phone'),
            'wechat' => Yii::t('app', 'Wechat'),
            'address' => Yii::t('app', 'Address'),
            'place_of_residence' => Yii::t('app', 'Place Of Residence'),
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
    public function getClients(): ActiveQuery
    {
        return $this->hasMany(Client::class, ['family_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getImportErrors(): ActiveQuery
    {
        return $this->hasMany(ImportError::class, ['family_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getExpenses(): ActiveQuery
    {
        return $this->hasMany(Expense::class, ['family_id' => 'id']);
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

    /**
     * @return ActiveQuery
     */
    public function getPayments(): ActiveQuery
    {
        return $this->hasMany(Payment::class, ['family_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProgramFamilies(): ActiveQuery
    {
        return $this->hasMany(ProgramFamily::class, ['family_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getPrograms(): ActiveQuery
    {
        return $this->hasMany(
            Program::class,
            ['id' => 'program_id'])->viaTable(
                'program_family',
                ['family_id' => 'id']);
    }

    /**
     * Get the sum of the payments that the family has made.
     * Payments can be filtered by wallet type, for example:
     *
     *         Wallet::WALLET_TYPE_CARD
     *
     * @param integer|null $walletType filtering parameter.
     * @return bool|int the sum of the payments of the current family.
     */
    public function getPaid($walletType = null)
    {
        $query = $this->getPayments();

        if ($walletType !== null) {

            $query->where(['wallet_type' => $walletType]);

        }

        return $query->sum('amount') ?? 0;
    }

    /**
     * Get the sum of the expenses that the family has incurred.
     * Expenses can be filtered by wallet type, for example:
     *
     *         Wallet::WALLET_TYPE_CARD
     *
     * @param integer|null $walletType filtering parameter.
     * @return bool|int the sum of the expenses of the current family.
     */
    public function getDue($walletType = null)
    {
        $query = $this->getExpenses();

        if ($walletType !== null) {

            $query->where(['wallet_type' => $walletType]);

        }

        return $query->sum('amount') ?? 0;
    }

    /**
     * @return ActiveQuery
     */
    public function getWxUnifiedPaymentOrders(): ActiveQuery
    {
        return $this->hasMany(WxUnifiedPaymentOrder::class, ['family_id' => 'id']);
    }
}
