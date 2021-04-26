<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "wx_unified_payment_order".
 *
 * @property int $id
 * @property int $status Current status of the order
 * @property int|null $hidden
 * @property string $appid appid of the app that originates the request
 * @property string $mch_id Wx merchant id for Minihiker
 * @property string|null $device_info Device ID for the device that generated the request, for example a payment terminal. Not used with Minihiker
 * @property string $nonce_str Random string, length < 32
 * @property string|null $sign HASH signature of the data
 * @property string|null $sign_type Signature type, defaults to MD5
 * @property string $body Simple description of the product sold
 * @property string|null $detail Detailed description of the product
 * @property string|null $attach Additional data, can be used as custom parameter
 * @property string|null $transaction_id WeChat Pay order number
 * @property string|null $time_end Payment completion time, the format is yyyyMMddHHmmss
 * @property string|null $bank_type Bank type, use string type bank identification, see bank list for bank type
 * @property string|null $is_subscribe Does the user follow the public account, Y-follow, N-not follow
 * @property string $out_trade_no Merchant order number
 * @property string|null $fee_type ISO 4217 currency code for the transaction i.e. CNY
 * @property float $total_fee Transaction amount
 * @property string $spbill_create_ip IP of the machine calling Wx payment API
 * @property string|null $time_start Order generation time, formatted as yyyyMMddHHmmss
 * @property string|null $time_expire Order expiration time, format yyyyMMddHHmmss, more than 1 minute and less than 2 hours after generation time
 * @property string|null $goods_tag Order offer marks, parameters required for the use of vouchers or discount features, indicating details of vouchers or concessions
 * @property string $notify_url WeChat payment result notification. Cannot contain parameters
 * @property string $trade_type For Wx Miniapp use JSAPI as trade type
 * @property string|null $product_id Internal merchant's product id.
 * @property string|null $limit_pay A value of "no_credit" will stop users from using credit cards to pay
 * @property string $openid End user openid on the merchant's system
 * @property string|null $receipt A value of "Y" will display this transaction on the user transactions records. Electronic invoice needs to be enabled by the merchant
 * @property string|null $scene_info JSON object with physical store data.
 * @property int|null $price_id
 * @property int|null $family_id Family ID, on the miniapp used as "Account ID"
 * @property int|null $client_id ID of the client that performed the payment
 * @property string|null $prepay_id Prepay ID returned with the preorder request
 * @property string|null $prepay_sign Signature of the package sent to the miniapp for user confirmation.
 * @property string|null $prepay_timestamp Timestamp. Time prepay package was sent to miniapp for user confirmation.
 * @property string|null $notify_xml Raw notify XML package sent by WX backend.
 * @property string|null $notify_result_code Notify result code SUCCESS/FAIL.
 * @property string|null $notify_return_code Notify return code SUCCESS/FAIL.
 * @property string|null $notify_err_code Short description of the error.
 * @property string|null $notify_err_code_des Long description of the error.
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Client $client
 * @property User $createdBy
 * @property Family $family
 * @property ProgramPrice $price
 * @property User $updatedBy
 */
class WxUnifiedPaymentOrder extends ActiveRecord
{
    // Order has been created and sent to WX backend, waiting prepay id
    public const STATUS_CREATED = 0;

    // WX backend returned an error, order has been cancelled
    public const STATUS_PREPAY_ERROR = 1;

    // WX backend returned success, pre-order has been created
    // Waiting for user to confirm payment on Mini-app
    public const STATUS_WAITING_CONFIRMATION = 2;

    // WX backend notified of an error confirming payment
    // Order has been cancelled while waiting for confirmation
    public const STATUS_CONFIRMATION_ERROR = 3;

    // WX backend has confirmed successful payment
    public const STATUS_CONFIRMATION_SUCCESS = 4;

    // After 12 hours the order was still waiting confirmation
    public const STATUS_ORDER_EXPIRED = 5;

    // The client cancelled the payment on the mini program
    public const STATUS_CANCELLED_BY_CLIENT = 6;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'wx_unified_payment_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['status', 'appid', 'mch_id', 'nonce_str', 'body', 'out_trade_no', 'total_fee', 'spbill_create_ip', 'notify_url', 'trade_type', 'openid'], 'required'],
            [['status', 'hidden', 'price_id', 'family_id', 'client_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['detail', 'notify_xml'], 'string'],
            [['total_fee'], 'number'],
            [['appid', 'mch_id', 'device_info', 'nonce_str', 'sign', 'sign_type', 'transaction_id', 'bank_type', 'out_trade_no', 'goods_tag', 'product_id', 'limit_pay', 'prepay_sign', 'prepay_timestamp', 'notify_result_code', 'notify_return_code', 'notify_err_code'], 'string', 'max' => 32],
            [['body', 'openid', 'notify_err_code_des'], 'string', 'max' => 128],
            [['attach'], 'string', 'max' => 127],
            [['time_end', 'time_start', 'time_expire'], 'string', 'max' => 14],
            [['is_subscribe'], 'string', 'max' => 1],
            [['fee_type', 'trade_type'], 'string', 'max' => 16],
            [['spbill_create_ip', 'prepay_id'], 'string', 'max' => 64],
            [['notify_url', 'scene_info'], 'string', 'max' => 256],
            [['receipt'], 'string', 'max' => 8],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['family_id'], 'exist', 'skipOnError' => true, 'targetClass' => Family::class, 'targetAttribute' => ['family_id' => 'id']],
            [['price_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramPrice::class, 'targetAttribute' => ['price_id' => 'id']],
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
            'status' => Yii::t('app', 'Status'),
            'hidden' => Yii::t('app', 'Hidden'),
            'appid' => Yii::t('app', 'Appid'),
            'mch_id' => Yii::t('app', 'Mch ID'),
            'device_info' => Yii::t('app', 'Device Info'),
            'nonce_str' => Yii::t('app', 'Nonce Str'),
            'sign' => Yii::t('app', 'Sign'),
            'sign_type' => Yii::t('app', 'Sign Type'),
            'body' => Yii::t('app', 'Body'),
            'detail' => Yii::t('app', 'Detail'),
            'attach' => Yii::t('app', 'Attach'),
            'transaction_id' => Yii::t('app', 'Transaction ID'),
            'time_end' => Yii::t('app', 'Time End'),
            'bank_type' => Yii::t('app', 'Bank Type'),
            'is_subscribe' => Yii::t('app', 'Is Subscribe'),
            'out_trade_no' => Yii::t('app', 'Out Trade No'),
            'fee_type' => Yii::t('app', 'Fee Type'),
            'total_fee' => Yii::t('app', 'Total Fee'),
            'spbill_create_ip' => Yii::t('app', 'Spbill Create Ip'),
            'time_start' => Yii::t('app', 'Time Start'),
            'time_expire' => Yii::t('app', 'Time Expire'),
            'goods_tag' => Yii::t('app', 'Goods Tag'),
            'notify_url' => Yii::t('app', 'Notify Url'),
            'trade_type' => Yii::t('app', 'Trade Type'),
            'product_id' => Yii::t('app', 'Product ID'),
            'limit_pay' => Yii::t('app', 'Limit Pay'),
            'openid' => Yii::t('app', 'Openid'),
            'receipt' => Yii::t('app', 'Receipt'),
            'scene_info' => Yii::t('app', 'Scene Info'),
            'price_id' => Yii::t('app', 'Price ID'),
            'family_id' => Yii::t('app', 'Family ID'),
            'client_id' => Yii::t('app', 'Client ID'),
            'prepay_id' => Yii::t('app', 'Prepay ID'),
            'prepay_sign' => Yii::t('app', 'Prepay Sign'),
            'prepay_timestamp' => Yii::t('app', 'Prepay Timestamp'),
            'notify_xml' => Yii::t('app', 'Notify Xml'),
            'notify_result_code' => Yii::t('app', 'Notify Result Code'),
            'notify_return_code' => Yii::t('app', 'Notify Return Code'),
            'notify_err_code' => Yii::t('app', 'Notify Err Code'),
            'notify_err_code_des' => Yii::t('app', 'Notify Err Code Des'),
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

    /**
     * @return ActiveQuery
     */
    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
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
    public function getFamily(): ActiveQuery
    {
        return $this->hasOne(Family::class, ['id' => 'family_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPrice(): ActiveQuery
    {
        return $this->hasOne(ProgramPrice::class, ['id' => 'price_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Return the total paid on this order on RMB.
     * @return float
     */
    public function getOrderAmountRmb(): float
    {
        // total_fee is in cents
        return $this->total_fee / 100;
    }
}
