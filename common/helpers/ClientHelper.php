<?php

namespace common\helpers;

use common\models\Client;
use common\models\FamilyRole;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;

/**
 * Class ClientHelper
 * Helper functionality for Client model and ClientController.
 *
 * @package common\helpers
 */
class ClientHelper
{
    /**
     * Return an array with all the entries on the FamilyRole table
     * indexed by their ids.
     *
     * @return array
     */
    public static function getClientRolesDropdown(): array
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
    public static function getRole($client): string
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

    /**
     * Return whether a client is signed up for any upcoming international programs.
     *
     * @param Client $client
     * @return bool
     * @throws InvalidConfigException
     */
    public static function hasInternationalProgram(Client $client): bool
    {
        return $client->getPrograms()
                ->joinWith(['programGroup', 'programGroup.location'])
                ->where(['location.international' => true])
                ->andWhere(['>=', 'program.start_date', date('Y-m-d')])
                ->count() > 0;
    }

    /**
     * Prepare a Client model to be deleted by updating related information
     * in the database.
     *
     * This method performs the following actions:
     * - Set given client's WxUnifiedPaymentOrder client_id attribute to null
     * - Set given client's ImportError client_id attribute to null
     * - Delete related ProgramClient records
     *
     * @param Client $client
     * @return bool true if all actions are performed without a problem, false otherwise
     * @throws StaleObjectException|\Throwable
     */
    public static function prepareForDeletion(Client $client): bool
    {
        // Set wx-unified-payment orders client ID attribute
        foreach ($client->wxUnifiedPaymentOrders as $order) {

            $order->client_id = null;
            if (!$order->save()) {
                Yii::error(
                    "Error setting client id ($client->id) to null " .
                    "on WxUnifiedPaymentOrder $order->id", __METHOD__
                );
                return false;
            }

        }

        // Set import-errors client ID to null
        foreach ($client->importErrors as $importError) {

            $importError->client_id = null;
            if (!$importError->save()) {
                Yii::error(
                    "Error setting client id ($client->id) to null " .
                    "on ImportError $importError->id", __METHOD__
                );
                return false;
            }

        }

        // Delete program client records
        foreach ($client->programClients as $programClient) {

            if (!$programClient->delete()) {
                Yii::error(
                    "Error deleting ProgramClient p: $programClient->program_id; " .
                    "c: $programClient->client_id", __METHOD__);
                return false;
            }

        }

        return true;
    }
}
