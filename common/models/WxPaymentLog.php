<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "wx_payment_log".
 *
 * @property int $id
 * @property array $order
 * @property array $user
 * @property string $message
 * @property string $get
 * @property string $post
 * @property string $headers
 * @property string $raw
 * @property string $method
 * @property string $notes
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class WxPaymentLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'wx_payment_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['order', 'user'], 'safe'],
            [['get', 'post', 'headers', 'raw', 'notes'], 'string'],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['message', 'method'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'order' => Yii::t('app', 'Order'),
            'user' => Yii::t('app', 'User'),
            'message' => Yii::t('app', 'Message'),
            'get' => Yii::t('app', 'Get'),
            'post' => Yii::t('app', 'Post'),
            'headers' => Yii::t('app', 'Headers'),
            'raw' => Yii::t('app', 'Raw'),
            'method' => Yii::t('app', 'Method'),
            'notes' => Yii::t('app', 'Notes'),
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
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                BlameableBehavior::class,
                TimestampBehavior::class
            ]
        );
    }

}
