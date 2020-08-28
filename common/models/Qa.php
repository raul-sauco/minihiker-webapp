<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "qa".
 *
 * @property int $id
 * @property int $program_group_id
 * @property string $question
 * @property string $answer
 * @property int $answered_at
 * @property int $answered_by
 * @property string $user_avatar_url @deprecated infer avatar based on "created_by" property
 * @property string $user_nickname @deprecated infer nickname based on "created_by" property
 * @property string $user_ip
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $answeredBy
 * @property User $createdBy
 * @property ProgramGroup $programGroup
 * @property User $updatedBy
 */
class Qa extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'qa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['program_group_id', 'answered_at', 'answered_by', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['question', 'answer', 'user_avatar_url'], 'string'],
            [['user_nickname'], 'string', 'max' => 256],
            [['user_ip'], 'string', 'max' => 128],
            [['answered_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['answered_by' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['program_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramGroup::class, 'targetAttribute' => ['program_group_id' => 'id']],
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
            'program_group_id' => Yii::t('app', 'Program Group ID'),
            'question' => Yii::t('app', 'Question'),
            'answer' => Yii::t('app', 'Answer'),
            'answered_at' => Yii::t('app', 'Answered At'),
            'answered_by' => Yii::t('app', 'Answered By'),
            'user_avatar_url' => Yii::t('app', 'User Avatar Url'),
            'user_nickname' => Yii::t('app', 'User Nickname'),
            'user_ip' => Yii::t('app', 'User Ip'),
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
    public function getAnsweredBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'answered_by']);
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
    public function getProgramGroup(): ActiveQuery
    {
        return $this->hasOne(ProgramGroup::class, ['id' => 'program_group_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

}
