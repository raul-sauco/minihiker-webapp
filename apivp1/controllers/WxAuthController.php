<?php

namespace apivp1\controllers;

use apivp1\helpers\ClientHelper;
use apivp1\models\Client;
use common\controllers\BaseController;
use common\helpers\HttpHelper;
use Yii;
use yii\web\ServerErrorHttpException;

/**
 * Class WxAuthController
 * @package app\controllers
 */
class WxAuthController extends BaseController
{
    protected $_verbs = ['POST','OPTIONS'];

    /**
     * Add unauthenticated access to the 'create' action
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['options','create'];
        return $behaviors;
    }

    /**
     * Authenticate a wechat user by sending the temporary code to the wechat backend.
     *
     * @return array
     * @throws ServerErrorHttpException
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function actionCreate (): array
    {
        /**
         * The wechat miniapp sends a login request to the backend and obtains
         * a temporary code 'jsCode' that sends to the backend.
         *
         * The backend sends that code to the Wechat API backend and obtains
         * the user's openid and a session key.
         *
         * Using the openid we can find the client, or create a new Client,
         * and personalize the content to their needs.
         */
        $jsCode = Yii::$app->request->post('jsCode');

        $appId = Yii::$app->params['weapp_app_id'];
        $appSecret = Yii::$app->params['weapp_app_secret'];
        $weixin_url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appId&secret=$appSecret&js_code=$jsCode&grant_type=authorization_code";

       /* $ch = curl_init($weixin_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        $response_raw = curl_exec($ch);

        // The response will be JSON, parse it before using
        $res = json_decode($response_raw,true);*/

        $res= HttpHelper::get($weixin_url);
        $res=json_decode($res,true);
        /*
         * If there is any error, the response will take the following form:
         * {
         *  "errcode": 40029,
         *  "errmsg": "invalid code, hints: [ req_id: MJcBzQaLRa-ARyqKa ]"
         * }
         *
         * If there are no errors the server will send a response in the following form:
         * {
         *   "session_key": "oZtIMQzqPe9oifVpNASaKQ==",
         *   "openid": "oSRr-45YjAgCPLECkTlUELCNILjQ"
         * }
         */

        // Check if there were any errors with the request.
        if (!empty($res['errcode'])) {

            Yii::warning(<<<WARNING
Error authenticating against weixin backend.
The server sent an error code {$res['errcode']} with a message {$res['errmsg']}.
WARNING
                , __METHOD__);

            return [
                'success' => false,
                'response' => $res
            ];
        }

        Yii::debug(['Obtained response from WxAPI', $res], __METHOD__);

        /**
         * If we obtained a valid openid value from the weixin server, use it to find or
         * create a new Client.
         * @var Client $client
         */
        $client = ClientHelper::fetchOrCreateByOpenId($res['openid']);

        if ($client === null) {
            throw new ServerErrorHttpException(
                'Obtained null Client for openid ' . $res['openid']
            );
        }

        // If the wx session key is different update it
        if ($client->wx_session_key !== $res['session_key']) {

            $client->wx_session_key = $res['session_key'];
            $client->wx_session_key_obtained_at = time();

            if (!$client->save()) {
                Yii::error(
                    "Error updating Client $client->id's session key.",__METHOD__
                );
            }
        }

        return [
            'success' => true,
            'access_token' => $client->user->access_token,
            'user_id' => $client->user_id,  // YHT weapp code expects the user_id parameter
        ];
    }
}
