<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "wx_unified_payment_order".
 *
 * @property int $id
 * @property int $status Current status of the order
 * @property string $appid appid of the app that originates the request
 * @property string $mch_id Wx merchant id for Minihiker
 * @property string $device_info Device ID for the device that generated the request, for example a payment terminal. Not used with Minihiker
 * @property string $nonce_str Random string, length < 32
 * @property string $sign HASH signature of the data
 * @property string $sign_type Signature type, defaults to MD5
 * @property string $body Simple description of the product sold
 * @property string $detail Detailed description of the product
 * @property string $attach Additional data, can be used as custom parameter
 * @property string $out_trade_no Merchant order number
 * @property string $fee_type ISO 4217 currency code for the transaction i.e. CNY
 * @property string $total_fee Transaction amount
 * @property string $spbill_create_ip IP of the machine calling Wx payment API
 * @property string $time_start Order generation time, formatted as yyyyMMddHHmmss
 * @property string $time_expire Order expiration time, format yyyyMMddHHmmss, more than 1 minute and less than 2 hours after generation time
 * @property string $goods_tag Order offer marks, parameters required for the use of vouchers or discount features, indicating details of vouchers or concessions
 * @property string $notify_url WeChat payment result notification. Cannot contain parameters
 * @property string $trade_type For Wx Miniapp use JSAPI as trade type
 * @property string $product_id Internal merchant's product id.
 * @property string $limit_pay A value of "no_credit" will stop users from using credit cards to pay
 * @property string $openid End user openid on the merchant's system
 * @property string $receipt A value of "Y" will display this transaction on the user transactions records. Electronic invoice needs to be enabled by the merchant
 * @property string $scene_info JSON object with physical store data.
 * @property int $price_id
 * @property int $family_id Family ID, on the miniapp used as "Account ID"
 * @property int $client_id ID of the client that performed the payment
 * @property string $prepay_id Prepay ID returned with the preorder request
 * @property string $prepay_sign Signature of the package sent to the miniapp for user confirmation.
 * @property string $prepay_timestamp Timestamp. Time prepay package was sent to miniapp for user confirmation.
 * @property string $notify_xml Raw notify XML package sent by WX backend.
 * @property string $notify_result_code Notify result code SUCCESS/FAIL.
 * @property string $notify_return_code Notify return code SUCCESS/FAIL.
 * @property string $notify_err_code Short description of the error.
 * @property string $notify_err_code_des Long description of the error.
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Client $client
 * @property User $createdBy
 * @property Family $family
 * @property ProgramPrice $price
 * @property User $updatedBy
 */
class WxUnifiedPaymentOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wx_unified_payment_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'appid', 'mch_id', 'nonce_str', 'body', 'out_trade_no', 'total_fee', 'spbill_create_ip', 'notify_url', 'trade_type', 'openid'], 'required'],
            [['status', 'price_id', 'family_id', 'client_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['detail', 'notify_xml'], 'string'],
            [['total_fee'], 'number'],
            [['appid', 'mch_id', 'device_info', 'nonce_str', 'sign', 'sign_type', 'out_trade_no', 'goods_tag', 'product_id', 'limit_pay', 'prepay_sign', 'prepay_timestamp', 'notify_result_code', 'notify_return_code', 'notify_err_code'], 'string', 'max' => 32],
            [['body', 'openid', 'notify_err_code_des'], 'string', 'max' => 128],
            [['attach'], 'string', 'max' => 127],
            [['fee_type', 'trade_type'], 'string', 'max' => 16],
            [['spbill_create_ip', 'prepay_id'], 'string', 'max' => 64],
            [['time_start', 'time_expire'], 'string', 'max' => 14],
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
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'status' => Yii::t('app', 'Status'),
            'appid' => Yii::t('app', 'Appid'),
            'mch_id' => Yii::t('app', 'Mch ID'),
            'device_info' => Yii::t('app', 'Device Info'),
            'nonce_str' => Yii::t('app', 'Nonce Str'),
            'sign' => Yii::t('app', 'Sign'),
            'sign_type' => Yii::t('app', 'Sign Type'),
            'body' => Yii::t('app', 'Body'),
            'detail' => Yii::t('app', 'Detail'),
            'attach' => Yii::t('app', 'Attach'),
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
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
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
    public function getFamily()
    {
        return $this->hasOne(Family::class, ['id' => 'family_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrice()
    {
        return $this->hasOne(ProgramPrice::class, ['id' => 'price_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
