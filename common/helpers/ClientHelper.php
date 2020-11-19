<?php

namespace common\helpers;

use common\models\Client;
use common\models\Family;
use common\models\FamilyRole;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\StaleObjectException;
use yii\web\ServerErrorHttpException;

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
    public static function getRole(Client $client): string
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
    public static function getFamilyWechatId(Client $client): string
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
        return "\u{200C}" .
            self::getFamilyGlobalAttribute($client, 'phone_number');
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
    protected static function getFamilyGlobalAttribute(Client $client, string $attr): string
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
     * @throws StaleObjectException
     * @throws Throwable
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

    /**
     * Return an ActiveQuery for Orphaned clients.
     * @return ActiveQuery
     */
    public static function getOrphanedClients(): ActiveQuery
    {
        return Client::find()->where(['family_id' => null]);
    }

    /**
     * Return all Family records that could be the orphaned client's intended Family.
     *
     * @param Client $client
     * @return ActiveQuery
     */
    public static function findOrphanedClientPossibleFamilies(Client $client): ActiveQuery
    {
        // Define an interval to search on.
        $seconds = 10;
        $max = $client->created_at + $seconds;
        $min = $client->created_at - 10;
        // https://stackoverflow.com/a/37941492/2557030
        return Family::find()->where(['between', 'created_at', $min, $max]);
    }

    /**
     * Fix Orphaned clients created by bug #13.
     * https://github.com/raul-sauco/minihiker-webapp/issues/13
     * @throws ServerErrorHttpException
     */
    public static function fixOrphanedClients(): void
    {
        /** @var Client $client */
        foreach (self::getOrphanedClients()->each() as $client) {
            // If we find a possible family, mark it but do nothing.
            $familyQuery = self::findOrphanedClientPossibleFamilies($client);
            $count = $familyQuery->count();
            if ($count > 0) {
                $family = $familyQuery->one();
                $notes = "\nFound $count possible matching family/ies like $family->id";
                $notes .= "\nRequires manual update, will not create new family.";
                $client->remarks .= $notes;
            } else {
                // No possible matches found, go ahead with the creation.
                $family = new Family();
                $family->name = $client->getName();
                $family->avatar = Yii::$app->params['defaultAvatar'];
                $family->membership_date = date('Y-m-d', $client->created_at);
                $family->remarks = Yii::t('app',
                    'Created automatically for openid {openid} on {date}',
                    ['openid' => $client->openid, 'date' => date('Y-m-d')]);
                $family->category = '非会员';
                if (!$family->save()) {
                    $msg = "There was an error creating a new Family for OpenID $client->openid";
                    Yii::error($msg, __METHOD__);
                    Yii::error($family->getErrors(), __METHOD__);
                    throw new ServerErrorHttpException($msg);
                }
                $client->family_id = $family->id;
                // Save the client.
                if (!$client->save()) {
                    $msg = "There was an error fixing orphaned client $client->id";
                    Yii::error($msg, __METHOD__);
                    Yii::error($client->getErrors(), __METHOD__);
                    throw new ServerErrorHttpException($msg);
                }
            }
        }
    }
}
