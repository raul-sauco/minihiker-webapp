<?php

namespace common\helpers;

use common\models\WxPaymentLog;
use common\models\WxUnifiedPaymentOrder;
use SimpleXMLElement;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use yii\web\ServerErrorHttpException;

/**
 * Class WxPaymentHelper
 * Helper functionality related to mini program payments and Wx unified payment orders.
 * @package common\helpers
 */
class WxPaymentHelper
{
    /**
     * Return an i18n label for an order status
     *
     * @param int $i The status
     * @return string The label for the order status
     */
    public static function getStatusLabel(int $i): string
    {
        $labels = [
            WxUnifiedPaymentOrder::STATUS_CREATED
            => Yii::t('app', 'Order Created'),
            WxUnifiedPaymentOrder::STATUS_PREPAY_ERROR
            => Yii::t('app', 'Error generating prepay ID'),
            WxUnifiedPaymentOrder::STATUS_WAITING_CONFIRMATION
            => Yii::t('app', 'Waiting for user confirmation'),
            WxUnifiedPaymentOrder::STATUS_CONFIRMATION_ERROR
            => Yii::t('app', 'Confirmation error'),
            WxUnifiedPaymentOrder::STATUS_CONFIRMATION_SUCCESS
            => Yii::t('app', 'Payment confirmed'),
            WxUnifiedPaymentOrder::STATUS_ORDER_EXPIRED
            => Yii::t('app', 'Order expired'),
            WxUnifiedPaymentOrder::STATUS_CANCELLED_BY_CLIENT
            => Yii::t('app', 'Payment cancelled by client'),
            WxUnifiedPaymentOrder::STATUS_REFUNDED
            => Yii::t('app', 'Transfer refunded'),
            WxUnifiedPaymentOrder::STATUS_NOT_PAY
            => Yii::t('app', 'Transfer is unpaid'),
            WxUnifiedPaymentOrder::STATUS_CLOSED
            => Yii::t('app', 'Transfer is closed'),
            WxUnifiedPaymentOrder::STATUS_REVOKED
            => Yii::t('app', 'Transfer revoked'),
            WxUnifiedPaymentOrder::STATUS_USER_PAYING
            => Yii::t('app', 'The user is paying'),
            WxUnifiedPaymentOrder::STATUS_PAY_ERROR
            => Yii::t('app', 'Payment failed'),
            WxUnifiedPaymentOrder::STATUS_ACCEPT
            => Yii::t('app', 'Transfer received, awaiting deduction'),
            WxUnifiedPaymentOrder::STATUS_SUCCESS
            => Yii::t('app', 'Payment successful'),
        ];

        return $labels[$i];
    }

    /**
     * Get a query for Wx Unified Payment Orders with abnormal status.
     * @return ActiveQuery
     */
    public static function getOrdersWithAbnormalStatus(): ActiveQuery
    {
        // Orders that are more than 12 hours old and have a "pending" status
        return WxUnifiedPaymentOrder::find()
            ->where(['<=', 'created_at', strtotime('-12 hours')])
            ->andWhere(['in', 'status', [
                WxUnifiedPaymentOrder::STATUS_CREATED,
                WxUnifiedPaymentOrder::STATUS_PREPAY_ERROR,
                WxUnifiedPaymentOrder::STATUS_WAITING_CONFIRMATION,
                WxUnifiedPaymentOrder::STATUS_CANCELLED_BY_CLIENT
            ]]);
    }

    /**
     * Update pending
     * @return bool
     * @throws ServerErrorHttpException
     */
    public static function updateExpiredOrdersStatus(): bool
    {
        $orderQuery = self::getOrdersWithAbnormalStatus();
        /** @var WxUnifiedPaymentOrder $order */
        foreach ($orderQuery->each() as $order) {
            // The order is more than 12 hours old, has expired.
            $order->status = WxUnifiedPaymentOrder::STATUS_ORDER_EXPIRED;
            if (!$order->save()) {
                $msg = "Error updating wx payment order $order->id status to expired";
                Yii::error($msg, __METHOD__);
                Yii::error($order->errors, __METHOD__);
                throw new ServerErrorHttpException($msg);
            }
        }
        return true;
    }

    /**
     * Check the current status of Wx payment orders. Documentation is at:
     * @link https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_2
     *
     * The server expects an XML package like the following:
     *
     * <xml>
     * <appid>wx2421b1c4370ec43b</appid>
     * <mch_id>10000100</mch_id>
     * <nonce_str>ec2316275641faa3aacf3cc599e8730f</nonce_str>
     * <transaction_id>1008450740201411110005820873</transaction_id>
     * <sign>FDD167FAA73459FD921B144BAF4F4CA2</sign>
     * </xml>
     *
     * @param WxUnifiedPaymentOrder $order
     * @return WxUnifiedPaymentOrder|null
     * @throws Exception
     * @throws ServerErrorHttpException
     * @throws \Exception
     */
    public static function updateOrderStatus(WxUnifiedPaymentOrder $order): ?WxUnifiedPaymentOrder
    {
        // Log intention of sending the request.
        $xml = WxPaymentHelper::generateCheckOrderStatusXml($order);
        $url = Yii::$app->params['wechat_pay_order_query_url'];
        $log = new WxPaymentLog();
        $log->order = Json::encode($order->toArray());
        $log->user = Yii::$app->user->id;
        $log->message = 'Requesting current order status from Wx API';
        $log->raw = $xml;
        $log->headers = Json::encode(Yii::$app->request->headers);
        $log->notes .= 'Request URL: ' . $url;
        if (!$log->save()) {
            Yii::warning($log->errors, __METHOD__);
        }

        // Send the request
        $xmlResponse = self::sendXMLRequest($url, $xml);
        if ($xmlResponse === null
            || !self::isSuccessXMLResponse($xmlResponse)
            || !self::isValidXMLSignature($xmlResponse)) {
            return null;
        }
        $xmlElem = new SimpleXMLElement($xmlResponse);
        if (!empty($xmlElem->is_subscribe)) {
            $order->is_subscribe = (string)$xmlElem->is_subscribe;
        }
        if (!empty($xmlElem->bank_type)) {
            $order->bank_type = (string)$xmlElem->bank_type;
        }
        if (!empty($xmlElem->time_end)) {
            $order->time_end = (string)$xmlElem->time_end;
        }
        if (!empty($xmlElem->trade_state)) {
            $order->trade_state = (string)$xmlElem->trade_state;
            $order->status = self::getOrderStatus((string)$xmlElem->trade_state);
        }
        if (!empty($xmlElem->trade_state_desc)) {
            $order->trade_state_desc = (string)$xmlElem->trade_state_desc;
        }
        if (!empty($xmlElem->cash_fee)) {
            $order->cash_fee = (string)$xmlElem->cash_fee;
        }
        if (!empty($xmlElem->cash_fee_type)) {
            $order->cash_fee_type = (string)$xmlElem->cash_fee_type;
        }
        if (!empty($xmlElem->transaction_id)
            && (empty($order->transaction_id)
                || $xmlElem->transaction_id !== $order->transaction_id)) {
            $order->transaction_id = (string)$xmlElem->transaction_id;
        }
        if (!$order->save()) {
            Yii::debug(["Error updating order $order->id", $order->errors], __METHOD__);
        }
        return $order;
    }

    /**
     * Get the corresponding value of a WxUnifiedPaymentOrder status given the
     * trade_state value of the WeChat Pay API response.
     *
     * The response can take the following values:
     *
     * SUCCESS--支付成功
     * REFUND--转入退款
     * NOTPAY--未支付
     * CLOSED--已关闭
     * REVOKED--已撤销(刷卡支付)
     * USERPAYING--用户支付中
     * PAYERROR--支付失败(其他原因，如银行返回失败)
     * ACCEPT--已接收，等待扣款
     *
     * Documentation is here:
     * @link https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_2
     *
     * @param string $tradeState
     * @return int
     */
    protected static function getOrderStatus(string $tradeState): int
    {
        switch ($tradeState) {
            case 'SUCCESS':
                return WxUnifiedPaymentOrder::STATUS_CONFIRMATION_SUCCESS;
            case 'REFUND':
                return WxUnifiedPaymentOrder::STATUS_REFUNDED;
            case 'NOTPAY':
                return WxUnifiedPaymentOrder::STATUS_NOT_PAY;
            case 'CLOSED':
                return WxUnifiedPaymentOrder::STATUS_CLOSED;
            case 'REVOKED':
                return WxUnifiedPaymentOrder::STATUS_REVOKED;
            case 'USERPAYING':
                return WxUnifiedPaymentOrder::STATUS_USER_PAYING;
            case 'PAYERROR':
                return WxUnifiedPaymentOrder::STATUS_PAY_ERROR;
            case 'ACCEPT':
                return WxUnifiedPaymentOrder::STATUS_ACCEPT;
            default:
                return WxUnifiedPaymentOrder::STATUS_UNDEFINED_ERROR;
        }
    }

    /**
     * Return whether a XML string returned by the Wechat API contains a
     * success response.
     *
     * The success response will be formatted as follows:
     *
     * <xml>
     *   <return_code><![CDATA[SUCCESS]]></return_code>
     *   <return_msg><![CDATA[OK]]></return_msg>
     *   <result_code><![CDATA[SUCCESS]]></result_code>
     *   ...
     * </xml>
     *
     * @throws \Exception
     */
    protected static function isSuccessXMLResponse(string $xmlResponse): bool
    {
        $xmlElem = new SimpleXMLElement($xmlResponse);
        return strcmp($xmlElem->return_code, 'SUCCESS') === 0
            && strcmp($xmlElem->result_code, 'SUCCESS') === 0;
    }

    /**
     * Validate the signature of an XML response. Documentation is here:
     * @link https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=4_3
     *
     * @param string $xml
     * @return bool
     */
    protected static function isValidXMLSignature(string $xml): bool
    {
        $xmlElem = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xmlElem);
        $array = json_decode($json, TRUE);
        $payload = $array;
        unset($payload['sign']);
        $sign = self::generateOrderSignature($payload);
        return strcmp($sign, $array['sign']) === 0;
    }

    /**
     * Generate the required XML to request the current order status.
     *
     * Documentation is at:
     * @link https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_2
     *
     * <xml>
     * <appid>wx2421b1c4370ec43b</appid>
     * <mch_id>10000100</mch_id>
     * <nonce_str>ec2316275641faa3aacf3cc599e8730f</nonce_str>
     * <transaction_id>1008450740201411110005820873</transaction_id>
     * <sign>FDD167FAA73459FD921B144BAF4F4CA2</sign>
     * </xml>
     *
     * @param WxUnifiedPaymentOrder $order
     * @return string
     * @throws Exception
     */
    protected static function generateCheckOrderStatusXml(WxUnifiedPaymentOrder $order): string
    {
        $attrs = [
            'appid' => Yii::$app->params['weapp_app_id'],
            'mch_id' => Yii::$app->params['wechat_mch_id'],
            'nonce_str' => self::generateNonceStr(),
            'sign_type' => Yii::$app->params['wechat_sign_type'],
        ];
        if (!empty($order->transaction_id)) {
            $attrs['transaction_id'] = $order->transaction_id;
        } else {
            $attrs['out_trade_no'] = $order->out_trade_no;
        }
        return self::attrToXml($attrs);
    }

    /**
     * @param string $url
     * @param string $xml
     * @return string|null
     * @throws ServerErrorHttpException
     */
    public static function sendXMLRequest(string $url, string $xml): ?string
    {
        // Create a new log instance
        $log = new WxPaymentLog();
        if (!function_exists('curl_version')) {
            $log->message = 'This server does not have curl';
            if (!$log->save()) {
                Yii::warning($log->errors, __METHOD__);
            }
            throw new ServerErrorHttpException(
                'This server does not have an updated version of curl'
            );
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $wxResponse = curl_exec($ch);
        $log->message = 'Received response from WxPay';
        $log->raw = $wxResponse;
        $log->save();
        if (!$wxResponse) {
            $log->message = 'No response from WxPayment API';
            $log->save();
            return null;
        }
        return $wxResponse;
    }

    /**
     * Given an array of order attributes, generate and sign it's corresponding
     * XML in the format expected by the WeChat server.
     *
     * Documentation for the API version used is here:
     * @link https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=4_3
     *
     * @param array $attrs
     * @return string
     */
    protected static function attrToXml(array $attrs): string
    {
        $xml = '<xml>';
        foreach ($attrs as $name => $value) {
            $xml .= "<$name>$value</$name>";
        }
        $sign = self::generateOrderSignature($attrs);
        $xml .= "<sign>$sign</sign>";
        $xml .= '</xml>';
        return $xml;
    }

    /**
     * Generate a 32 character length pseudo-random string.
     *
     * @param int $length
     * @return string
     * @throws Exception
     */
    protected static function generateNonceStr($length = 32): string
    {
        $str = Yii::$app->security->generateRandomString($length);
        return str_replace(['_', '-'], '', $str);
    }

    /**
     * Generate the expected signature as detailed on
     * @link https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=4_3
     *
     * @param array $attrs
     * @return string
     */
    public static function generateOrderSignature(array $attrs): string
    {
        // Order the keys alphabetically, as expected by the api
        ksort($attrs);
        $string = '';
        // Iterate over the array, no values will be empty
        foreach ($attrs as $name => $value) {
            $string .= "&$name=$value";
        }
        // trim the first &
        // $string = preg_replace('/^&/', '', $tring);
        $string = ltrim($string, '&');
        $key = strtolower(Yii::$app->params['wechat_mch_secret_key']);
        // Add the wechat secret key
        $string .= '&key=' . $key;
        // Generate the signature and return it
        return strtoupper(md5($string));
    }
}
