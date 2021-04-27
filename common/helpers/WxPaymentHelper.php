<?php

namespace common\helpers;

use common\models\WxPaymentLog;
use common\models\WxUnifiedPaymentOrder;
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
    public static function getStatusLabel (int $i): string
    {
        $labels = [
            0 => Yii::t('app', 'Order Created'),
            1 => Yii::t('app', 'Error generating prepay ID'),
            2 => Yii::t('app', 'Waiting for user confirmation'),
            3 => Yii::t('app', 'Confirmation error'),
            4 => Yii::t('app', 'Payment confirmed'),
            5 => Yii::t('app', 'Order expired'),
            6 => Yii::t('app', 'Payment cancelled by client'),
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
     * @throws Exception
     */
    public static function checkOrderStatus(WxUnifiedPaymentOrder $order): ?string
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
        $log->notes .= 'Request URL: ' . $url ;
        if (!$log->save()) {
            Yii::warning($log->errors, __METHOD__);
        }

        // Send the request
        $xmlResponse = self::sendXMLRequest($url, $xml);
        // TODO update the order
        return $xmlResponse;
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
        if (!$log->save()) {
            Yii::warning($log->errors, __METHOD__);
        }
        if (!$wxResponse) {
            $error_msg = 'No response from WxPayment API';
            $log->message = $error_msg;
            if (!$log->save()) {
                Yii::warning($log->errors, __METHOD__);
            }
//            throw new ServerErrorHttpException($error_msg);
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
    protected static function generateNonceStr ($length = 32): string
    {
        $str = Yii::$app->security->generateRandomString($length);
        return str_replace(['_','-'], '', $str);
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
