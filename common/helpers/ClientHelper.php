<?php
namespace common\helpers;


use common\models\Client;
use common\models\FamilyRole;
use Yii;

/**
 * Helper functionality for Client model and ClientController
 * 
 * @author i - 2019
 *
 */
class ClientHelper
{
    /**
     * Return an array with all the entries on the FamilyRole table
     * indexed by their ids.
     *
     * @return array
     */
    public static function getClientRolesDropdown()
    {
        $rolesArray = [];

        /** @var FamilyRole $familyRole */
        foreach (FamilyRole::find()->each() as $familyRole) {

            $rolesArray[$familyRole->id] = $familyRole->getNamei18n();

        }

        return $rolesArray;
    }

    /**
     * Return a string representation of the family role.
     *
     * @param Client $client
     * @return string
     */
    public static function getRole($client)
    {
        if (empty($client->family_role_id)) {
            return '';
        }

        if ((int)$client->family_role_id === 8) {
            return $client->family_role_other ?? '';
        }

        return $client->familyRole->getNamei18n();
    }

    /**
     * Get the client's family Wechat ID.
     *
     * @param Client $client
     * @return string
     */
    public static function getFamilyWechatId($client): string
    {
        return self::getFamilyGlobalAttribute($client, 'wechat_id');
    }

    /**
     * Get the client's family phone number.
     *
     * @param $client
     * @return string
     */
    public static function getFamilyPhoneNumber($client): string
    {
        return self::getFamilyGlobalAttribute($client, 'phone_number');
    }

    /**
     * Get the value of an attribute for a Family.
     * It will check first the "mother" then "father" and last
     * the client passed as a parameter.
     *
     * @param Client $client
     * @param string $attr
     * @return string
     */
    protected static function getFamilyGlobalAttribute($client, $attr): string
    {
        /** @var Client $mother */
        $mother = $client->family->getClients()->where(['family_role_id' => 3])->one();
        if ($mother !== null && !empty($mother->getAttribute($attr))) {
            return $mother->getAttribute($attr);
        }

        /** @var Client $father */
        $father = $client->family->getClients()->where(['family_role_id' => 4])->one();
        if ($father !== null && !empty($father->getAttribute($attr))) {
            return $father->getAttribute($attr);
        }

        if (!empty($client->getAttribute($attr))) {
            return $client->getAttribute($attr);
        }

        return Yii::t('app', 'Not Set');
    }
}
