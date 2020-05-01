<?php

namespace common\models;

use common\helpers\ProgramHelper;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
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
    public static function tableName()
    {
        return 'program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
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
    public function attributeLabels()
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
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritDoc}
     * @return bool
     */
    public function beforeDelete()
    {
        // Delete related ProgramUser records
        ProgramUser::deleteAll(['program_id' => $this->id]);

        return parent::beforeDelete();
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function afterDelete()
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
     * @throws \yii\base\InvalidConfigException
     */
    public function getDates()
    {
        return ProgramHelper::getDateString($this);
    }

    /**
     * Return the program's name in a human readable format.
     * The returned string has been internationalized and encoded to
     * escape HTML entities.
     *
     * @return string The program's name
     * @throws \yii\base\InvalidConfigException
     */
    public function getNamei18n()
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
     * @throws \yii\base\InvalidConfigException
     */
    public function getXLParticipantCount()
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
     * @throws \yii\base\InvalidConfigException
     */
    public function getLongParticipantCount()
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
     * @throws \yii\base\InvalidConfigException
     */
    public function getParticipantCount()
    {
        return Yii::t('app', '{c,plural,=0{no clients} =1{1 client} other{# clients}}. ',
            [
                'c' => $this->getClients()->count(),
            ]);
    }

    /**
     * Returns a detailed count of participants differenciating
     * between adults and kids.
     * @throws \yii\base\InvalidConfigException
     */
    public function getDetailedParticipantCount()
    {
        $kidCount = $this->getClients()->where(['family_role_id' => 1])->count();
        $adultCount = $this->getClients()->where(['!=', 'family_role_id', 1])->count();

        return Yii::t('app', '{n,plural,=0{no kids} =1{1 kid} other{# kids}}. ' .
            '{i,plural,=0{no adults} =1{1 adult} other{# adults}}.', [
                'n' => $kidCount,
                'i' => $adultCount,
            ]);
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
    public function getExpenses()
    {
        return $this->hasMany(Expense::class, ['program_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payment::class, ['program_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramGroup()
    {
        return $this->hasOne(ProgramGroup::class, ['id' => 'program_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramPeriod()
    {
        return $this->hasOne(ProgramPeriod::class, ['id' => 'program_period_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramClients()
    {
        return $this->hasMany(ProgramClient::class, ['program_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getClients()
    {
        return $this->hasMany(Client::class, ['id' => 'client_id'])->viaTable('program_client', ['program_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramFamilies()
    {
        return $this->hasMany(ProgramFamily::class, ['program_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getFamilies()
    {
        return $this->hasMany(Family::class, ['id' => 'family_id'])->viaTable('program_family', ['program_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramGuides()
    {
        return $this->hasMany(ProgramGuide::class, ['program_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getGuides()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('program_guide', ['program_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramPrices()
    {
        return $this->hasMany(ProgramPrice::class, ['program_id' => 'id']);
    }
}

