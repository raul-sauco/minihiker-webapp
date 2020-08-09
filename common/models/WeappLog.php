<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "weapp_log".
 *
 * @property int $id
 * @property string|null $message
 * @property string|null $res
 * @property string|null $req
 * @property string|null $extra
 * @property int|null $level
 * @property string|null $page
 * @property string|null $method
 * @property string|null $line
 * @property string|null $timestamp
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property User $createdBy
 * @property User $updatedBy
 */
class WeappLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'weapp_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['res', 'req', 'extra'], 'string'],
            [['level', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['message', 'page', 'method'], 'string', 'max' => 255],
            [['line'], 'string', 'max' => 20],
            [['timestamp'], 'string', 'max' => 64],
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
            'message' => Yii::t('app', 'Message'),
            'res' => Yii::t('app', 'Res'),
            'req' => Yii::t('app', 'Req'),
            'extra' => Yii::t('app', 'Extra'),
            'level' => Yii::t('app', 'Level'),
            'page' => Yii::t('app', 'Page'),
            'method' => Yii::t('app', 'Method'),
            'line' => Yii::t('app', 'Line'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return ArrayHelper::merge([
            BlameableBehavior::class,
            TimestampBehavior::class,
        ], parent::behaviors());
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return ActiveQuery
     */
    public function getCreatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return ActiveQuery
     */
    public function getUpdatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
