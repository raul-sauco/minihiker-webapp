<?php

namespace apivp1\controllers;

use apivp1\helpers\PaymentHelper;
use apivp1\helpers\WxPaymentHelper;
use apivp1\models\WxUnifiedPaymentOrder;
use common\models\WxPaymentLog;
use Yii;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\Response;

/**
 * Class WxPaymentNotifyController
 * @package apivp1\controllers
 */
class WxPaymentNotifyController extends Controller
{
    private $_verbs = ['POST', 'OPTIONS'];

    /**
     * Handle WX backend payment notifications. Documentation is at:
     * https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_7
     *
     * The server will send an XML package like the following:
     *
        	'<xml><appid><![CDATA[wxfcf7eeb4bb398f08]]></appid>
            <attach><![CDATA[Payment test]]></attach>
            <bank_type><![CDATA[CFT]]></bank_type>
            <cash_fee><![CDATA[1]]></cash_fee>
            <fee_type><![CDATA[CNY]]></fee_type>
            <is_subscribe><![CDATA[N]]></is_subscribe>
            <mch_id><![CDATA[1533448121]]></mch_id>
            <nonce_str><![CDATA[g8licDxZKPGN7drV8iO]]></nonce_str>
            <openid><![CDATA[oSRr-45YjAgCPLECkTlUELCNILjQ]]></openid>
            <out_trade_no><![CDATA[1561535285]]></out_trade_no>
            <result_code><![CDATA[SUCCESS]]></result_code>
            <return_code><![CDATA[SUCCESS]]></return_code>
            <sign><![CDATA[E7091615B3CB828D129AD3546D43D433]]></sign>
            <time_end><![CDATA[20190626154809]]></time_end>
            <total_fee>1</total_fee>
            <trade_type><![CDATA[JSAPI]]></trade_type>
            <transaction_id><![CDATA[4200000301201906261850092111]]></transaction_id>
            </xml>'
     *
     * The method will first check the signature, then try to find the order.
     * If both those actions are successful it will update the order and return
     * success.
     */
    public function actionCreate(): array
    {
        // Get the parameters from the request
        // It will use app/helpers/XmlParser
        $post = Yii::$app->request->post();
        $order_no = $post['out_trade_no'];

        // WX backend expects an XML response
        Yii::$app->response->format = Response::FORMAT_XML;
        Yii::$app->response->formatters[Response::FORMAT_XML] = [
            'class' => '\yii\web\XmlResponseFormatter',
            'contentType' => 'text/xml',
            'rootTag' => 'xml'
        ];

        // Check that the signature is valid
        $payload = $post;
        unset($payload['sign']);
        $sign = WxPaymentHelper::generateOrderSignature($payload);

        if (strcmp($sign, $post['sign']) !== 0) {

            // The signature is not valid, refuse processing the request

            // Create a log of the error
            $log = new WxPaymentLog();
            $log->message = 'Notification failed. Signature failed.';
            $log->notes = "Package signature $sign does not match expected signature " . $post['sign'];
            $log->raw = Yii::$app->request->rawBody;
            $log->post = Json::encode($post);
            $log->headers = Json::encode(Yii::$app->request->headers);
            $log->method = __METHOD__;
            $log->save();

            return [
                'return_code' => 'FAIL',
                'return_msg' => "签名失败"
            ];

        }

        // Fetch the order related to this notification
        $order = WxUnifiedPaymentOrder::findOne((int)$order_no);

        $log = new WxPaymentLog();
        if ($order === null) {
            // Create a log of the error
            $log->message = 'Notification failed. Order ' . $order_no . ' not found';
            $log->raw = Yii::$app->request->rawBody;
            $log->post = Json::encode($post);
            $log->headers = Json::encode(Yii::$app->request->headers);
            $log->method = __METHOD__;
            $log->save();

            return [
                'return_code' => 'FAIL',
                'return_msg' => "订单 $order_no 未找到"
            ];
        }


        // Create a log of the update

        if (isset($post['result_code'])) {

            $result = (string)$post['result_code'];
            $order->notify_result_code = $result;

            if (strcmp($result, 'SUCCESS') === 0) {

                $order->status = WxUnifiedPaymentOrder::STATUS_CONFIRMATION_SUCCESS;
                $log->message = 'Order ' . $order->id . ' updated to STATUS_PAYMENT_CONFIRMED';

            } else {

                $order->status = WxUnifiedPaymentOrder::STATUS_CONFIRMATION_ERROR;
                $log->message = 'Order ' . $order->id . ' updated to STATUS_CONFIRMATION_ERROR';

            }

        } else {

            $log->message = 'Wx notification did not contain a result_code attribute';

        }

        // We have found an order, update it's values

        // The simpler construct does not work well with CDATA conversion
        // $order->notify_return_code  = $post['return_code'] ?? '';

        if (isset($post['transaction_id'])) {
            $order->transaction_id = (string)$post['transaction_id'];
        }

        if (isset($post['time_end'])) {
            $order->time_end = (string)$post['time_end'];
        }

        if (isset($post['bank_type'])) {
            $order->bank_type = (string)$post['bank_type'];
        }

        if (isset($post['is_subscribe'])) {
            $order->is_subscribe = (string)$post['is_subscribe'];
        }

        if (isset($post['return_code'])) {
            $order->notify_return_code = (string)$post['return_code'];
        }

        if (isset($post['err_code'])) {
            $order->notify_err_code = (string)$post['err_code'];
        }

        if (isset($post['err_code_des'])) {
            $order->notify_err_code_des = (string)$post['err_code_des'];
        }

        $log->order = Json::encode($order);
        $log->raw = Yii::$app->request->rawBody;
        $log->post = Json::encode($post);
        $log->headers = Json::encode(Yii::$app->request->headers);
        $log->method = __METHOD__;

        if (!$order->save()) {
            $msg = 'Problem updating order ' . $order->id . ' to STATUS_PAYMENT_CONFIRMED';
            Yii::error($msg, __METHOD__);
            Yii::error($order->toArray(), __METHOD__);
            Yii::error($order->errors, __METHOD__);
            $log->message = $msg;
            $log->notes = Json::encode($order);
            $log->notes2 = Json::encode($order->errors);

            return [
                'return_code' => 'FAIL',
                'return_msg' => '参数格式校验错误'
            ];
        }

        if (!PaymentHelper::registerWxPayment($order)) {
            Yii::error(
                'Failed to create MhPayment from Wx Unified Order ' . $order->id,
                __METHOD__);
        }
        $log->save();

        return [
            'return_code' => 'SUCCESS',
            'return_msg' => 'OK'
        ];
    }

    /**
     * Send the HTTP options available to this route
     */
    public function actionOptions()
    {
        if (Yii::$app->getRequest()->getMethod() !== 'OPTIONS') {
            Yii::$app->getResponse()->setStatusCode(405);
        }
        $options = $this->_verbs;
        Yii::$app->getResponse()->getHeaders()->set('Allow', implode(', ', $options));
    }
}
