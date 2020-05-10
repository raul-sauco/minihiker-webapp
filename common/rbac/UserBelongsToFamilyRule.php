<?php

namespace common\rbac;

use common\models\Family;
use Yii;
use yii\rbac\Rule;

/**
 * Class UserBelongsToFamilyRule
 * @package common\rbac
 */
class UserBelongsToFamilyRule extends Rule
{
    public $name = 'userBelongsToFamilyRule';

    public function execute($user, $item, $params) : bool
    {
        if (empty($params['family_id'])) {

            Yii::error('family_id parameter should not be null', __METHOD__);
            return false;

        }

        Yii::debug(
            "Checking if current application user ($user) belongs to family " .
            $params['family_id'] . '.', __METHOD__);

        if (($family = Family::findOne($params['family_id'])) === null) {

            Yii::error('Family referenced by id: ' . $params['family_id'] . ' is null.',
                __METHOD__);
            return false;

        }

        foreach ($family->clients as $client) {

            if ( (int)$client->user_id === $user ) {

                Yii::debug("User $user belongs to family $family->id.",
                    __METHOD__);
                return true;

            }
        }

        // No match found, do not authorize
        return false;
    }
}
