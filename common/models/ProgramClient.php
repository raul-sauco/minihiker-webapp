<?php

namespace common\models;

use common\helpers\ProgramFamilyHelper;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\web\ServerErrorHttpException;

/**
 * This is the model class for table "program_client".
 *
 * @property int $program_id
 * @property int $client_id
 * @property string $remarks
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Program $program
 * @property Client $client
 */
class ProgramClient extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'program_client';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['program_id', 'client_id'], 'required'],
            [['program_id', 'client_id', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['remarks'], 'string'],
            [['program_id', 'client_id'], 'unique', 'targetAttribute' => ['program_id', 'client_id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['program_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'program_id' => Yii::t('app', 'Program ID'),
            'client_id' => Yii::t('app', 'Client ID'),
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
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes): void
    {
        // Update the family link
        if (!ProgramFamilyHelper::safeLink($this->program_id, $this->client->family_id)) {
            Yii::error(
                "ProgramClient::afterSave error ($this->program_id,$this->client_id)",
                __METHOD__
            );
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @throws \Throwable
     * @throws StaleObjectException
     * @throws ServerErrorHttpException
     */
    public function afterDelete(): void
    {
        if (!ProgramFamilyHelper::safeUnLink($this->program_id, $this->client->family_id)) {
            Yii::error(
                "ProgramClient::afterDelete error ($this->program_id, $this->client_id)",
                __METHOD__
            );
        }
        parent::afterDelete();
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
    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }
}
