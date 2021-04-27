<?php

namespace apivp1\controllers;

use apivp1\helpers\WxPaymentHelper;
use apivp1\models\Client;
use apivp1\models\ProgramPrice;
use apivp1\models\WxUnifiedPaymentOrder;
use common\controllers\BaseController;
use common\models\WxPaymentLog;
use SimpleXMLElement;
use Yii;
use yii\base\Exception;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * Class WxPaymentController
 * @package apivp1\controllers
 */
class WxPaymentController extends BaseController
{
    protected $_verbs = ['POST', 'OPTIONS'];

    /**
     * Preprocess a wx payment order. This method expects two parameters:
     *      amount - The total amount to be charged, on CNY
     *      price  - The ID attribute of the apivp1\models\Price linked to the payment
     *
     * The method will send a request to the wechat server and, if successful,
     * return the expected values to the Miniapp.
     *
     * @return array
     * @throws Exception
     * @throws ServerErrorHttpException
     * @throws \Exception
     */
    public function actionCreate(): array
    {
        $post = Yii::$app->request->post();

        // Find a Client model related to this request
        $client = Client::findOne(['user_id' => Yii::$app->user->id]);

        if ($client === null) {
            throw new UnauthorizedHttpException(
                'Your account does not have enough related Client information.'
            );
        }

        $price = ProgramPrice::findOne($post['price']);

        if ($price === null) {
            throw new BadRequestHttpException(
                'Price ' . $post['price'] . ' cannot be found in the system.'
            );
        }

        /** @var Yii::$app->request->getUserIP() $client */
        $order = WxPaymentHelper::generateWxUnifiedPaymentOrder(
            $post['amount'],
            $client,
            $price->id ?? ''
        );

        $xml = WxPaymentHelper::generateOrderXml($order);
        $url = Yii::$app->params['wechat_pay_url'];

        // Create a log with the request information
        $log = new WxPaymentLog();
        $log->order = Json::encode($order);
        $log->user = Json::encode($client);
        $log->post = Json::encode($post);
        $log->message = 'Sending data to Wx API';
        $log->raw = $xml;
        $log->headers = Json::encode(Yii::$app->request->headers);
        $log->notes = 'Origin IP: ' . Yii::$app->request->getUserIP() . "\n";
        $log->notes .= 'Request URL: ' . $url;
        if (!$log->save()) {
            Yii::warning($log->errors, __METHOD__);
        }

        $xmlElem = new SimpleXMLElement(WxPaymentHelper::sendXMLRequest($url, $xml));
        if ($xmlElem === null) {
            $error_msg = 'No response from WxPayment API';
            $log->message = $error_msg;
            if (!$log->save()) {
                Yii::warning($log->errors, __METHOD__);
            }
            $order->status = WxUnifiedPaymentOrder::STATUS_PREPAY_ERROR;
            if (!$order->save()) {
                Yii::error(
                    'Error updating order ' . $order->id . ' to STATUS_PREPAY_ERROR.',
                    __METHOD__
                );
                Yii::error($order->errors, __METHOD__);
            }
            throw new ServerErrorHttpException($error_msg);
        }
        // We have a valid SimpleXMLElement
        $return_code = $xmlElem->return_code;

        if (strcmp($return_code, 'SUCCESS') === 0) {
            // If return_code is 'SUCCESS' we can check the result
            $result_code = $xmlElem->result_code;
            if (strcmp($result_code, 'SUCCESS') === 0) {
                // Both result_code and return_code are good, we have prepay_id
                $prepay_id = (string)$xmlElem->prepay_id;
                $log->message = 'WxPayment request SUCCESS, prepay_id: ' . $prepay_id;
                if (!$log->save()) {
                    Yii::warning($log->errors, __METHOD__);
                }
                $attrs = [
                    'appId' => $order->appid,
                    'timeStamp' => time(),
                    'nonceStr' => $order->nonce_str,
                    'package' => 'prepay_id=' . $prepay_id,
                    'signType' => $order->sign_type
                ];
                $sign = WxPaymentHelper::generateOrderSignature($attrs);
                $order->prepay_id = $prepay_id;
                $order->prepay_sign = $sign;
                $order->prepay_timestamp = (string)time();
                $order->status = WxUnifiedPaymentOrder::STATUS_WAITING_CONFIRMATION;
                if (!$order->save()) {
                    Yii::error(
                        'Error updating order ' . $order->id .
                        ' to STATUS_WAITING_CONFIRMATION.',
                        __METHOD__
                    );
                    Yii::error($order->errors, __METHOD__);
                }
                $attrs['paySign'] = $sign;
                return $attrs;
            }
            $error_msg = 'WxPayment API result_code: FAIL';
            $log->message = $error_msg;
            $log->notes = "WxPay API err_code: $xmlElem->err_code, $xmlElem->err_code_des.";
        } else {
            $error_msg = 'WxPayment API return_code: FAIL';
            $log->message = $error_msg;
            $log->notes = $xmlElem->return_msg;
        }
        if (!$log->save()) {
            Yii::warning($log->errors, __METHOD__);
        }
        $order->status = WxUnifiedPaymentOrder::STATUS_PREPAY_ERROR;
        if (!$order->save()) {
            Yii::error(
                'Error updating order ' . $order->id . ' to STATUS_PREPAY_ERROR.',
                __METHOD__
            );
            Yii::error($order->errors, __METHOD__);
        }
        throw new ServerErrorHttpException($error_msg);
    }
}
