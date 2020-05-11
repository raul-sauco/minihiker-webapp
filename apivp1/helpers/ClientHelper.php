<?php

namespace apivp1\helpers;

use apivp1\models\Client;
use apivp1\models\Family;
use common\helpers\StringHelper;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\helpers\Json;
use yii\web\ServerErrorHttpException;

/**
 * Class ClientHelper
 * @package apivp1\helpers
 */
class ClientHelper extends \common\helpers\ClientHelper
{
    /**
     * Find a client by it's wechat openid value. If the client doesn't exists create
     * a new one.
     *
     * @param $openid
     * @param $userInfo
     * @return Client|null
     * @throws ServerErrorHttpException|Exception
     */
    public static function fetchOrCreateByOpenId($openid, $userInfo = []) : Client
    {
        if (empty($openid)) {
            throw new ServerErrorHttpException('Missing openid argument');
        }

        Yii::debug("Fetching client with openid: $openid.", __METHOD__);

        $client = Client::findOne(['openid' => $openid]);

        if ($client !== null) {

            // If the client exists return it directly but check if the username has been updated
            if (!empty($userInfo['nickName']) &&
                strcmp($client->user->username, self::generateTemporaryUsername($openid)) === 0) {

                // The username was autogenerated and has never been updated, update it
                $client->user->username = $userInfo['nickName'];
                if ($client->user->save()) {
                    $client->nickname = $client->user->username;
                    if (!$client->save()) {
                        Yii::warning('Could not update client nickname. ' .
                            Json::encode($client->errors));
                    }
                } else {
                    Yii::warning('Failed to update $user->username. ' .
                        Json::encode($client->user->errors));
                }
            }

            return $client;

        }

        // If the client does not exist create a new one
        return self::createNewClientFromOpenId($openid, $userInfo);
    }

    /**
     * Create a new Family/Client based on the data obtained from Wechat.
     *
     * @param string $openid
     * @param array $userInfo
     * @return Client
     * @throws ServerErrorHttpException
     * @throws Exception
     */
    private static function createNewClientFromOpenId(
        string $openid, $userInfo) : Client
    {
        $user = new User();

        // Check if the frontend has sent us the user information
        if (!empty($userInfo['nickName'])) {

            $user->username = $userInfo['nickName'];

        } else {

            // if no nickName, temporarily use the openid as the username
            $user->username = self::generateTemporaryUsername($openid);

        }

        $user->password = StringHelper::random_str(16);
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

        // Add userInfo if available
        if (!empty($userInfo['gender'])) {

            $client->is_male = ((int)$userInfo['gender']) === 1;

        }

        // Let's assume that the person registering is a parent
        $client->is_kid = false;

        if (!$client->save()) {

            Yii::error($client->getErrors(), __METHOD__);
            throw new ServerErrorHttpException(
                'There was an error creating a new Client.');

        }

        // Create a Family to link with the client
        $family = new Family();
        $family->name = $client->nickname;

        // Shorten the Family's name if longer than 12
        if (strlen($family->name) > 12) {
            $family->name = substr($family->name, 0, 12);
        }


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
