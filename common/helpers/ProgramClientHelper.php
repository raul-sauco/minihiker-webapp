<?php

namespace common\helpers;

use common\models\Client;
use common\models\Program;
use common\models\ProgramClient;
use Yii;
use yii\db\ActiveQuery;
use yii\db\StaleObjectException;
use yii\web\ServerErrorHttpException;

/**
 * Helper functionality for ProgramClient model.
 *
 * Class ProgramClientHelper
 * @package common\helpers
 */
class ProgramClientHelper
{
    /**
     * Links a Program model with a Client model by creating the corresponding record on
     * the program_client table. Performs the necessary checks and updates accordingly the
     * program_family table.
     * @param integer $programId The id of the Program to link
     * @param integer $clientId The id of the Client to link
     * @return ProgramClient|null
     * @throws ServerErrorHttpException
     */
    public static function safeLink(int $programId, int $clientId): ?ProgramClient
    {
        Yii::debug(
            "Linking program $programId and client $clientId",
            __METHOD__
        );

        if (Program::findOne($programId) === null) {
            $msg = "Trying to create a ProgramClient with program id $programId (null)";
            Yii::error($msg,__METHOD__);
            throw new ServerErrorHttpException($msg);
        }

        if (Client::findOne($clientId) === null) {
            $msg = "Trying to create a ProgramClient with client id $clientId (null)";
            Yii::error($msg,__METHOD__);
            throw new ServerErrorHttpException($msg);
        }

        if (($programClient = ProgramClient::findOne(['program_id' => $programId,
                'client_id' => $clientId])) !== null) {
            Yii::warning(
                "Trying to link not null ProgramClient p $programId c $clientId",
                __METHOD__
            );
            return $programClient;
        }

        // There were no errors, both models exists and the link does not
        // $model->link() fails to update timestamp and blamable
        // $program->link('clients', $client);
        $programClient = new ProgramClient();
        $programClient->program_id = $programId;
        $programClient->client_id = $clientId;
        // ProgramClient::afterSave checks the ProgramFamily link.
        if (!$programClient->save()) {
            $msg = "Error linking program $programId and client $clientId";
            Yii::error($msg, __METHOD__);
            throw new ServerErrorHttpException($msg);
        }
        return $programClient;
    }


    /**
     * Unlinks a Program record and a Client record by deleting the corresponding
     * entry on the program_client table.
     * @param integer $programId
     * @param integer $clientId
     * @return boolean wheter the deletion was successful.
     * @throws ServerErrorHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public static function safeUnLink(int $programId, int $clientId): ?bool
    {
        Yii::debug(
            "Unlinking Program $programId and client $clientId",
            __METHOD__
        );

        if (Program::findOne($programId) === null) {
            Yii::error(
                "Trying to unlink a ProgramClient with program id $programId (null)",
                __METHOD__);
            return false;
        }

        if (Client::findOne($clientId) === null) {
            Yii::error(
                "Trying to unlink a ProgramClient with client id $clientId (null)",
                __METHOD__);
            return false;
        }

        if (($programClient = ProgramClient::findOne(['program_id' => $programId,
                'client_id' => $clientId])) === null) {
            Yii::warning('Trying to delete an null ProgramClient record, ' .
                "Program $programId, Client $clientId." , __METHOD__);
            return false;
        }

        // There were no errors, both models and the link exist
        if (!$programClient->delete()) {
            $msg = "Error unlinking Program $programId and Client $clientId";
            Yii::error($msg, __METHOD__);
            throw new ServerErrorHttpException($msg);
        }

        return true;
    }

    /**
     * Fix orphaned ProgramClients.
     * @param Program $program
     */
    public static function fixedOrphanedProgramClients(Program $program): void
    {
        /** @var ProgramClient $programClient */
        foreach ($program->getProgramClients()->each() as $programClient) {
            if ($program->getProgramFamilies()
                ->where(['family_id' => $programClient->client->family_id])
                ->one() === null) {
                Yii::warning(
                    "Fixed orphaned ProgramClient p $program->id c $programClient->client_id",
                    __METHOD__);
                ProgramFamilyHelper::safeLink($program->id, $programClient->client->family_id);
            }
        }
    }

    /**
     * Return an ActiveQuery for ProgramClient records where the corresponding
     * ProgramFamily record does not exist.
     * @return ActiveQuery
     */
    public static function getOrphanedProgramClients(): ActiveQuery
    {
        $sql = 'select pc.* from program_client as pc, client as c ' .
            'where pc.client_id=c.id and not exists(' .
            'select 1 from program_family as pf where pf.family_id=c.family_id ' .
            'and pf.program_id=pc.program_id)';
        return ProgramClient::findBySql($sql);
    }
}
