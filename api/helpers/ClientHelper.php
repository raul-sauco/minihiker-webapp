<?php

namespace api\helpers;

use api\models\Client;
use common\models\FamilyRole;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class ClientHelper
 * @package api\helpers
 */
class ClientHelper
{
    /**
     * Try to update client family roles based on search query parameters
     * if roles are null in the database.
     * @param ActiveQuery $query
     * @param int $roleId
     */
    public static function assignRoleIfNull(ActiveQuery $query, int $roleId): void
    {
        if (($role = FamilyRole::findOne($roleId)) !== null) {
            /** @var Client $client */
            foreach ($query->each() as $client) {
                if ($client->familyRole === null) {
                    $client->family_role_id = $role->id;
                    if (!$client->save()) {
                        Yii::warning(
                            "Failed to update client $client->id role to $role->id",
                            __METHOD__);
                    }
                }
            }
        }
    }
}
