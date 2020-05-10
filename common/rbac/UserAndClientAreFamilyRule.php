<?php

namespace common\rbac;

use common\models\Client;
use Yii;
use yii\rbac\Rule;

/**
 * Class UserAndClientAreFamilyRule
 * @package common\rbac
 */
class UserAndClientAreFamilyRule extends Rule
{
    public $name = 'userAndClientAreFamilyRule';

    public function execute($user, $item, $params) : bool
    {
        Yii::info(
            "Checking if current application user ($user) and client " .
            $params['client_id'] . ' are from the same family.', __METHOD__);

        if (empty($params['client_id'])) {

            Yii::error('client_id parameter should not be null', __METHOD__);
            return false;

        }

        $client = Client::findOne($params['client_id']);

        if ($client === null) {

            Yii::error('Client referenced by id: ' . $params['client_id'] . ' is null.',
                __METHOD__);
            return false;

        }

        $userClient = Client::findOne(['user_id' => $user]);

        if ($userClient === null) {

            Yii::debug('Client referenced by user is null', __METHOD__);
            return false;

        }

        if ($userClient->family_id === null) {

            Yii::debug('Client referenced by user does not belong to any family.', __METHOD__);
            return false;

        }

        if ($client->family_id === null) {

            Yii::debug('Client referenced by client_id parameter does not belong to any family.', __METHOD__);
            return false;

        }

        if ($userClient->family_id !== $client->family_id) {

            Yii::debug("User client family ($userClient->family_id) and client family ($client->family_id) are not the same family.",
                __METHOD__);
            return false;

        }

        return true;
    }
}
