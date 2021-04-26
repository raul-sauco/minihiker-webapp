<?php

namespace apivp1\helpers;

use apivp1\models\Client;
use apivp1\models\Family;
use common\helpers\StringHelper;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\web\ServerErrorHttpException;

/**
 * Class ClientHelper
 * @package apivp1\helpers
 */
class ClientHelper extends \common\helpers\ClientHelper
{
    /**
     * Find a client by it's wechat openid value.
     * If the client doesn't exists create a new one
     *
     * @param $openid
     * @return Client|null
     * @throws ServerErrorHttpException|Exception
     */
    public static function fetchOrCreateByOpenId($openid) : Client
    {
        if (empty($openid)) {
            throw new ServerErrorHttpException('Missing openid argument');
        }
        Yii::debug("Fetching client with openid: $openid.", __METHOD__);
        if (($client = Client::findOne(['openid' => $openid])) === null) {
            return self::createNewClientFromOpenId($openid);
        }
        return $client;
    }

    /**
     * Create a new Family/Client based on the data obtained from Wechat.
     * @param string $openid
     * @return Client
     * @throws ServerErrorHttpException
     * @throws Exception
     */
    private static function createNewClientFromOpenId(string $openid) : Client
    {
        $user = new User();
        $user->username = self::generateTemporaryUsername($openid);
        $user->password = StringHelper::randomStr(16);
        $user->user_type = User::TYPE_CLIENT;

        if (!$user->save()) {
            Yii::error($user->getErrors(), __METHOD__);
            throw new ServerErrorHttpException(
                'There was an error creating a new Client->user.');
        }

        // Set the new user as the application user to have blame.
        if (!Yii::$app->user->login($user)) {
            Yii::warning(
                "Could not automatically login user $user->id",
                __METHOD__);
        }

        if (!Yii::$app->user->can('client')) {
            Yii::error(
                "Automatically generated user $user->id does not have client permission.",
                __METHOD__
            );
        }

        // Create a Family to link with the client
        $family = new Family();
        $family->name = '未注册';
        $family->avatar = Yii::$app->params['defaultAvatar'];
        $family->membership_date = date('Y-m-d');
        $family->remarks = Yii::t('app',
            'Created automatically for openid {openid} on {date}',
            ['openid' => $openid, 'date' => date('Y-m-d')]);
        $family->category = '非会员';
        if (!$family->save()) {
            $msg = "There was an error creating a new Family for OpenID $openid";
            Yii::error($msg, __METHOD__);
            Yii::error($family->getErrors(), __METHOD__);
            throw new ServerErrorHttpException(
                'There was an error creating a new Client.');
        }

        // Create a Client to link to the user
        $client = new Client();
        $client->nickname = $user->username;
        $client->openid = $openid;
        $client->user_id = $user->id;
        $client->family_id = $family->id;

        // Save the client.
        if (!$client->save()) {
            $msg = "There was an error creating a new Client for OpenID $openid";
            Yii::error($msg, __METHOD__);
            Yii::error($client->getErrors(), __METHOD__);
            throw new ServerErrorHttpException(
                'There was an error creating a new Client.');
        }

        // If we got here, all the data was created successfully.
        Yii::debug(
            "Created new client $client->id with user $client->user_id " .
            "and family $client->family_id", __METHOD__);
        return $client;
    }

    /**
     * Return the last 8 characters of the result of hashing the
     * entry parameter. Add extra random characters if needed
     * to avoid collisions.
     *
     * @param $openid
     * @return bool|string
     * @throws Exception
     */
    private static function generateTemporaryUsername ($openid)
    {
        $name = substr(md5($openid), -8);
        while (($user = User::findOne(['username' => $name])) !== null) {
            Yii::warning(
                "Collision found between new user and user $user->id",
                __METHOD__
            );
            $name .= StringHelper::randomStr(4);
        }
        return $name;
    }
}
