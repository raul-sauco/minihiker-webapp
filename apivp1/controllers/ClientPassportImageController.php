<?php

namespace apivp1\controllers;

use apivp1\models\Client;
use common\controllers\BaseController;
use common\helpers\StringHelper;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

/**
 * Class ClientPassportImageController
 * @package apivp1\controllers
 */
class ClientPassportImageController extends BaseController
{
    /**
     * Let's a client upload a passport image
     * @throws ForbiddenHttpException
     * @throws ServerErrorHttpException
     * @throws Exception
     */
    public function actionCreate()
    {
        $user = Yii::$app->user->identity;

        /* @var User $user */
        if ($user === null) {
            throw new ForbiddenHttpException(
                Yii::t('Only logged in users can upload passport images', __METHOD__)
            );
        }

        $userClient = Client::findOne(['user_id' => $user->id]);
        if ($userClient === null) {
            throw new ForbiddenHttpException(
                Yii::t('app', 'Current user does not have a valid Client account')
            );
        }

        $clientId = Yii::$app->request->post('client');
        if ($clientId === null) {
            throw new ForbiddenHttpException(
                Yii::t(
                    'app',
                    'Need client ID parameter to know which client the image belongs to')
            );
        }

        $client = Client::findOne($clientId);
        if ($client === null) {
            throw new ForbiddenHttpException(
                Yii::t('app', 'No client account found for the current parameter')
            );
        }

        if ($userClient->family_id !== $client->family_id) {
            throw new ForbiddenHttpException(
                Yii::t('app', 'Wrong Client ID parameter')
            );
        }

        Yii::debug("User $user->id Uploading image for client $clientId", __METHOD__);

        $file = UploadedFile::getInstanceByName('image');
        if ($file === null) {
            throw new ServerErrorHttpException(
                Yii::t('app', 'Error obtaining image file data')
            );
        }

        // Generate a random name
        $fileName = StringHelper::randomStr(32) . '.' . $file->getExtension();

        // Create the destination directory if it does not exist
        $filePath = Yii::getAlias('@imgPath/c/p/');
        if (!file_exists($filePath) && !mkdir($filePath, 0777, true) && !is_dir($filePath)) {
            throw new ServerErrorHttpException(
                Yii::t(
                    'app',
                    'Error creating directory {directory}',
                    ['directory' => $filePath]
                )
            );
        }

        // Try to save the file
        if (!$file->saveAs($filePath . $fileName)) {

            Yii::error("Error $file->error saving file $fileName", __METHOD__);

            $msg = Yii::t('app', 'Error uploading image file');

            if ($file->error === 1) {
                $msg = Yii::t(
                    'app',
                    'Filesize {size} exceeds the maximum upload size allowed',
                    ['size' => $file->size]
                );
            }

            throw new ServerErrorHttpException($msg);

        }

        // Try to update the client's data
        $client->passport_image = $fileName;
        if (!$client->save()) {
            Yii::error([
                'Error updating Client passport image',
                $client->errors,
                $client
            ], __METHOD__);
            return $client->errors;
        }

        // If we had no errors, return the updated data
        return $client;
    }
}
