<?php

namespace common\helpers;

use common\models\Client;
use common\models\Program;
use common\models\ProgramClient;
use Yii;
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
     * @return bool
     * @throws ServerErrorHttpException
     */
    public static function safeLink(int $programId, int $clientId): bool
    {
        Yii::debug(
            "Linking program $programId and client $clientId",
            __METHOD__
        );

        if (Program::findOne($programId) === null) {
            Yii::error(
                "Trying to create a ProgramClient with program id $programId (null)",
                __METHOD__);
            return false;
        }

        if (Client::findOne($clientId) === null) {
            Yii::error(
                "Trying to create a ProgramClient with client id $clientId (null)",
                __METHOD__);
            return false;
        }

        if (ProgramClient::findOne(['program_id' => $programId, 'client_id' => $clientId]) !== null) {
            Yii::warning(
                'Trying to create an existing ProgramClient record',
                __METHOD__
            );
            return false;
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
        return true;
    }
}
