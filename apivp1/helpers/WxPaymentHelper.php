<?php

namespace apivp1\helpers;

use apivp1\models\Client;
use apivp1\models\ProgramPrice;
use common\models\WxUnifiedPaymentOrder;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\StringHelper;
use yii\web\ServerErrorHttpException;

/**
 * This class encapsulates methods related to the Wechat payments that
 * the public API uses. If a method is used by other applications,
 * it should be placed in \common\helpers\WxPaymentHelper
 *
 * Class WxPaymentHelper
 * @package app\helpers
 */
class WxPaymentHelper extends \common\helpers\WxPaymentHelper
{

    /**
     * Create a new payment instance.
     *
     * The Wechat server expects a XML request with some
     * mandatory data, as in the example:
     *
     * <xml>
     * <appid>wx2421b1c4370ec43b</appid>
     * <attach>支付测试</attach>
     * <body>JSAPI支付测试</body>
     * <mch_id>10000100</mch_id>
     * <detail><![CDATA[{ "goods_detail":[ {
     *   "goods_id":"iphone6s_16G",
     *   "wxpay_goods_id":"1001",
     *   "goods_name":"iPhone6s 16G",
     *   "quantity":1, "price":528800,
     *   "goods_category":"123456",
     *   "body":"苹果手机"
     * },{
     *   "goods_id":"iphone6s_32G",
     *   "wxpay_goods_id":"1002",
     *   "goods_name":"iPhone6s 32G",
     *   "quantity":1,
     *   "price":608800,
     *   "goods_category":"123789",
     *   "body":"苹果手机"
     * } ] }]]></detail>
     * <nonce_str>1add1a30ac87aa2db72f57a2375d8fec</nonce_str>
     * <notify_url>http://wxpay.wxutil.com/pub_v2/pay/notify.v2.php</notify_url>
     * <openid>oUpF8uMuAJO_M2pxb1Q9zNjWeS6o</openid>
     * <out_trade_no>1415659990</out_trade_no>
     * <spbill_create_ip>14.23.150.211</spbill_create_ip>
     * <total_fee>1</total_fee>
     * <trade_type>JSAPI</trade_type>
     * <sign>0CB01533B8C1EF103065174F50BCA001</sign>
     * </xml>
     *
     * @param int $amount
     * @param Client $client
     * @param $price_id
     * @return WxUnifiedPaymentOrder
     * @throws Exception
     * @throws ServerErrorHttpException
     * @throws InvalidConfigException
     */
    public static function generateWxUnifiedPaymentOrder (
        $amount,$client,$price_id): WxUnifiedPaymentOrder
    {
        $order = new WxUnifiedPaymentOrder();

        // Set the appid from the parameters
        $order->appid = Yii::$app->params['weapp_app_id'];

        // Get merchant id from the app parameters
        $order->mch_id = Yii::$app->params['wechat_mch_id'];

        $order->attach = self::generateOrderAttach();

        $order->nonce_str = self::generateNonceStr(20);

        $order->sign_type = 'MD5';

        $order->body = self::generateOrderBody($price_id);

        $order->detail = self::generateOrderDetail($price_id);

        $order->out_trade_no = (string)time();

        // The miniprogram sends amounts on CNY but the server expects cents
        $order->total_fee = $amount * 100;

        $order->openid = $client->openid;

        $order->spbill_create_ip = self::generateSpBillCreateIp();

        $order->notify_url = Yii::$app->params['WxPaymentNotifyUrl'];

        $order->trade_type = 'JSAPI';

        $order->sign = 'TEMP_TEST';

        $order->client_id = $client->id;

        $order->family_id = $client->family_id;

        $order->price_id = (int) $price_id;

        $order->status = WxUnifiedPaymentOrder::STATUS_CREATED;

        $order->sign_type = 'MD5';  //Optional

        if (!$order->save()) {
            Yii::error('Error creating new WxUnifiedPaymentOrder', __METHOD__);
            Yii::error($order->toArray(), __METHOD__);
            Yii::error($order->errors, __METHOD__);
            throw new ServerErrorHttpException('There was a problem saving the new order');
        }

        // Change the out_trade_number to point to the order id
        $order->out_trade_no = (string)$order->id;
        if (!$order->save()) {
            Yii::error('Error updating order out_trade_no to match order ID', __METHOD__);
            Yii::error($order->toArray(), __METHOD__);
            Yii::error($order->errors, __METHOD__);
            throw new ServerErrorHttpException('There was a problem saving the new order');
        }

        return $order;
    }

    /**
     * Generate the attach field for a Wx unified payment order.
     *
     * @return string
     */
    private static function generateOrderAttach (): string
    {
        return '童行者项目报名订单';
    }

    /**
     * Generate Xml to be sent
     * @param $order WxUnifiedPaymentOrder
     * @return string The xml to be sent
     */
    public static function generateOrderXml ($order): string
    {
        // Get all the attributes for the order
        $all_attrs = $order->getAttributes(null, [
            'id',
            'status',
            'sign', // Don't need signature on the string
            'price_id',
            'family_id',
            'client_id',
            'prepay_id',
            'prepay_sign',
            'prepay_timestamp',
            'notify_xml',
            'notify_result_code',
            'notify_return_code',
            'notify_err_code',
            'notify_err_code_desc',
            'created_by',
            'created_at',
            'updated_by',
            'updated_at'
        ]);

        // iterate over the attributes and get rid of nulls
        $attrs = [];
        foreach ($all_attrs as $name => $value) {
            if (!empty($order->getAttribute($name))) {
                $attrs[$name] = $value;
            }
        }

        return self::attrToXml($attrs);
    }

    /**
     * Generate the WxUnifiedPayment order body.
     * The body has a maximum length of 128 character.
     * @link https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_1&index=1
     *
     * @param $priceId
     * @return string
     */
    private static function generateOrderBody ($priceId): string
    {
        $body = '童行者 项目报名订单 ';
        if (($price = ProgramPrice::findOne($priceId)) !== null) {
            if (($program = $price->program) !== null && ($userId = Yii::$app->user->id !== null) !== null) {
                $body = "童行者 项目$program->id 报名订单. p$priceId u$userId";
            } else {
                $body = "童行者 项目报名订单. p$priceId";
            }
        }
        return StringHelper::truncate($body, 60);
    }

    /**
     * Generate the WxUnifiedPayment order detail.
     *
     * @param $priceId
     * @return string
     * @throws InvalidConfigException
     */
    private static function generateOrderDetail ($priceId): string
    {
        $detail = '童行者 项目报名订单 ';

        if (($price = ProgramPrice::findOne($priceId)) !== null) {

            if (($program = $price->program) !== null) {

                $detail .= $program->programGroup->weapp_display_name ?? '';
                $detail .= Yii::$app->formatter->asDate($program->start_date);
                $detail .= '到';
                $detail .= Yii::$app->formatter->asDate($program->end_date);

            }

            $detail .= ' ' . $price->getNamei18n();
        }

        $detail .= '订单时间 ' . Yii::$app->formatter->asDatetime(time());

        return $detail;
    }

    /**
     * Return the IP of the current server.
     *
     * @return mixed
     */
    private static function generateSpBillCreateIp ()
    {
        return Yii::$app->params['serverIp'];
    }
}
