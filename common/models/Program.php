<?php

namespace common\models;

use common\helpers\ProgramHelper;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\helpers\Html;

/**
 * This is the model class for table "program".
 *
 * @property int $id
 * @property int $status
 * @property int $program_group_id
 * @property int $program_period_id
 * @property string $start_date
 * @property string $end_date
 * @property int $registration_open
 * @property int $client_limit
 * @property string $remarks
 * @property int $price
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Expense[] $expenses
 * @property Payment[] $payments
 * @property ProgramGroup $programGroup
 * @property ProgramPeriod $programPeriod
 * @property User $createdBy
 * @property User $updatedBy
 * @property ProgramClient[] $programClients
 * @property Client[] $clients
 * @property ProgramFamily[] $programFamilies
 * @property Family[] $families
 * @property ProgramGuide[] $programGuides
 * @property User[] $guides
 * @property mixed $detailedParticipantCount
 * @property string $participantCount
 * @property string $namei18n
 * @property string $xLParticipantCount
 * @property string $dates
 * @property string $longParticipantCount
 * @property string|int $registrations
 * @property ProgramPrice[] $programPrices
 */
class Program extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_PROPOSED = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_CANCELLED = 3;
    const STATUS_ACTIVE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['status', 'program_group_id', 'program_period_id', 'registration_open', 'client_limit', 'price', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['program_group_id', 'program_period_id'], 'required'],
            [['start_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
            [['remarks'], 'string'],
            [['program_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramGroup::class, 'targetAttribute' => ['program_group_id' => 'id']],
            [['program_period_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramPeriod::class, 'targetAttribute' => ['program_period_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'status' => Yii::t('app', 'Status'),
            'program_group_id' => Yii::t('app', 'Program Group'),
            'program_period_id' => Yii::t('app', 'Program Period'),
            'start_date' => Yii::t('app', 'Start Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'registration_open' => Yii::t('app', 'Registration Open'),
            'client_limit' => Yii::t('app', 'Client Limit'),
            'remarks' => Yii::t('app', 'Remarks'),
            'price' => Yii::t('app', 'Price'),
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
     * {@inheritDoc}
     * @return bool
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function beforeDelete(): bool
    {
        ProgramHelper::deleteAllRelatedRecords($this);
        return parent::beforeDelete();
    }

    /**
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function afterDelete(): void
    {
        // Delete the program group if there are no more programs
        if ((int)$this->programGroup->getPrograms()->count() === 0) {
            $this->programGroup->delete();
        }
    }

    /**
     * Return a formatted and localized representation of the program dates.
     *
     * @return string The program dates
     * @throws InvalidConfigException
     */
    public function getDates(): string
    {
        return ProgramHelper::getDateString($this);
    }

    /**
     * Return the program's name in a human readable format.
     * The returned string has been internationalized and encoded to
     * escape HTML entities.
     *
     * @return string The program's name
     * @throws InvalidConfigException
     */
    public function getNamei18n(): string
    {
        // Add year if not null
        $name = Yii::$app->formatter->asDate($this->start_date, 'php:y') ?? '';

        $name .= empty($this->programGroup->type->name) ? '' :
            ' ' . $this->programGroup->type->name;

        $name .= ' ' . $this->programGroup->location_id;

        $name .= empty($this->programGroup->name) ? '' :
            ' ' . $this->programGroup->name;

        // Add the period if there are multiple programs in the group
        if ($this->programGroup->getPrograms()->count() > 1) {

            $name .= ' ' . $this->programPeriod->name;

        }

        return Html::encode($name);
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function getXLParticipantCount(): string
    {
        $f = Yii::t('app',
            '{c,plural,=0{} =1{1 family. } other{# families. }}',
            ['c' => $this->getFamilies()->count()]);

        return $f . $this->getLongParticipantCount();
    }

    /**
     * Returns the long participant count by concatenating the
     * participant count with the detailed participant count.
     * Example "17 Clients. 8 Adults. 9 Kids."
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function getLongParticipantCount(): string
    {
        $count = $this->getParticipantCount();

        if ($this->getClients()->count() > 0) {

            $count .= $this->getDetailedParticipantCount();

        }

        return $count;
    }

    /**
     * Get the participant count.
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function getParticipantCount(): string
    {
        return Yii::t('app', '{c,plural,=0{no clients} =1{1 client} other{# clients}}. ',
            [
                'c' => $this->getClients()->count(),
            ]);
    }

    /**
     * Returns a detailed count of participants differenciating
     * between adults and kids.
     * @throws InvalidConfigException
     */
    public function getDetailedParticipantCount(): string
    {
        $kidCount = $this->getClients()->where(['family_role_id' => 1])->count();
        $adultCount = $this->getClients()
            ->where(['!=', 'family_role_id', 1])
            ->orWhere(['family_role_id' => null])->count();

        return Yii::t('app', '{n,plural,=0{no kids} =1{1 kid} other{# kids}}. ' .
            '{i,plural,=0{no adults} =1{1 adult} other{# adults}}.', [
                'n' => $kidCount,
                'i' => $adultCount,
            ]);
    }

    /**
     * Return the current count of clients registered for this program
     * @return int|string
     * @throws InvalidConfigException
     */
    public function getRegistrations()
    {
        return $this->getClients()->count();
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
    public function getExpenses(): ActiveQuery
    {
        return $this->hasMany(Expense::class, ['program_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPayments(): ActiveQuery
    {
        return $this->hasMany(Payment::class, ['program_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProgramGroup(): ActiveQuery
    {
        return $this->hasOne(ProgramGroup::class, ['id' => 'program_group_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProgramPeriod(): ActiveQuery
    {
        return $this->hasOne(ProgramPeriod::class, ['id' => 'program_period_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProgramClients(): ActiveQuery
    {
        return $this->hasMany(ProgramClient::class, ['program_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getClients(): ActiveQuery
    {
        return $this->hasMany(Client::class, ['id' => 'client_id'])->viaTable('program_client', ['program_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProgramFamilies(): ActiveQuery
    {
        return $this->hasMany(ProgramFamily::class, ['program_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getFamilies(): ActiveQuery
    {
        return $this->hasMany(Family::class, ['id' => 'family_id'])->viaTable('program_family', ['program_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProgramGuides(): ActiveQuery
    {
        return $this->hasMany(ProgramGuide::class, ['program_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getGuides(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('program_guide', ['program_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProgramPrices(): ActiveQuery
    {
        return $this->hasMany(ProgramPrice::class, ['program_id' => 'id']);
    }
}
