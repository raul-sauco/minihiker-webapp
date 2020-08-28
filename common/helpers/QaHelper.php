<?php

namespace common\helpers;

use common\models\Client;
use common\models\Qa;
use Yii;

/**
 * Class QaHelper
 * @package common\helpers
 */
class QaHelper
{
    /**
     * Get an account name for the "asker" of a QA
     * @param Qa $qa
     * @return string
     */
    public static function getWxAccountNickname(Qa $qa) : string {
        if ($qa->createdBy !== null &&
            ($client = Client::findOne(['user_id' => $qa->created_by])) !== null &&
            $client->family !== null && !empty($client->family->name)) {
            return $client->family->name;
        }
        return Yii::t('app', 'Unregistered user');
    }

    /**
     * Get an account avatar for the "asker" of a QA
     * @param Qa $qa
     * @return string
     */
    public static function getWxAccountAvatar(Qa $qa) : string {
        if ($qa->createdBy !== null &&
            ($client = Client::findOne(['user_id' => $qa->created_by])) !== null &&
            $client->family !== null && !empty($client->family->avatar)) {
            return $client->family->avatar;
        }
        return Yii::$app->params['defaultAvatar'];
    }
}
