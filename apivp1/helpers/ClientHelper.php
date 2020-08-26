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
     * Create a new Family/Client based on the data obtained from Wechat.     *
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

        // Create a Client to link to the user
        $client = new Client();
        $client->nickname = $user->username;
        $client->openid = $openid;
        $client->user_id = $user->id;
        if (!$client->save()) {
            Yii::error($client->getErrors(), __METHOD__);
            throw new ServerErrorHttpException(
                'There was an error creating a new Client.');
        }

        // Create a Family to link with the client
        $family = new Family();
        $family->name = '未注册';
        $family->membership_date = date('Y-m-d');
        $family->remarks = Yii::t('app',
            'Created automatically for openid {openid} on {date}',
            ['openid' => $openid, 'date' => date('Y-m-d')]);
        $family->category = '非会员';
        if (!$family->save()) {
            Yii::error($family->getErrors(), __METHOD__);
            throw new ServerErrorHttpException(
                'There was an error creating a new Family.');
        }
        $client->family_id = $family->id;
        return $client;
    }

    /**
     * Return the last 8 characters of the result of hashing the
     * entry parameter.
     *
     * @param $openid
     * @return bool|string
     */
    private static function generateTemporaryUsername ($openid)
    {
        return substr(md5($openid), -8);
    }
}
