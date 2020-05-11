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
 * @property string|null $order
 * @property string|null $user
 * @property string|null $message
 * @property string|null $get
 * @property string|null $post
 * @property string|null $headers
 * @property string|null $raw
 * @property string|null $method
 * @property string|null $notes
 * @property string|null $notes2
 * @property string|null $notes3
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $created_at
 * @property int|null $updated_at
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
            [['get', 'post', 'headers', 'raw', 'notes', 'notes2', 'notes3'], 'string'],
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
            'notes2' => Yii::t('app', 'Notes2'),
            'notes3' => Yii::t('app', 'Notes3'),
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
