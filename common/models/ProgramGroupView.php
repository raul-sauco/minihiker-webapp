<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "program_group_view".
 *
 * @property int $program_group_id
 * @property int $user_id
 * @property int $timestamp
 *
 * @property ProgramGroup $programGroup
 * @property User $user
 */
class ProgramGroupView extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_group_view';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_group_id', 'user_id'], 'required'],
            [['program_group_id', 'user_id', 'timestamp'], 'integer'],
            [['program_group_id', 'user_id'], 'unique', 'targetAttribute' => ['program_group_id', 'user_id']],
            [['program_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramGroup::class, 'targetAttribute' => ['program_group_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'program_group_id' => Yii::t('app', 'Program Group ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'timestamp' => Yii::t('app', 'Timestamp'),
        ];
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
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
