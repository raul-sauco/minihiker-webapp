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
 * Class FamilyAvatarController
 * @package apivp1\controllers
 */
class FamilyAvatarController extends BaseController
{
    /**
     * Let's a client update their account's avatar.
     * This should use HTTP PUT and be handled by actionUpdate, but  wx.uploadFile() cannot be configured
     * and only sends data using POST
     * 
     * @throws ForbiddenHttpException
     * @throws ServerErrorHttpException
     * @throws Exception
     */
    public function actionCreate()
    {
        /* @var User $user */
        if (($user = Yii::$app->user->identity) === null) {
            throw new ForbiddenHttpException(
                Yii::t('Only logged in users can update their own avatar', __METHOD__)
            );
        }

        if (($client = Client::findOne(['user_id' => $user->id])) === null || $client->family === null) {
            throw new ForbiddenHttpException(
                Yii::t('app', 'Current user does not have a valid account')
            );
        }

        Yii::debug("Updating family $client->family_id's avatar", __METHOD__);

        $file = UploadedFile::getInstanceByName('image');
        if ($file === null) {
            throw new ServerErrorHttpException(
                Yii::t('app', 'Error obtaining image file data')
            );
        }

        // Generate a random name
        $fileName = StringHelper::randomStr(32) . '.' . $file->getExtension();

        // Create the destination directory if it does not exist
        $filePath = Yii::getAlias('@imgPath/f/');
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

        $oldFileName = $client->family->avatar;

        // Try to update the client's data
        $client->family->avatar = $fileName;
        if (!$client->family->save()) {
            Yii::error([
                'Error updating Account avatar image',
                $client->family->errors,
                $client->family
            ], __METHOD__);
            return $client->family->errors;
        }

        // Delete the old avatar if not default
        $oldFile = Yii::getAlias('@imgPath/f/') . $oldFileName;
        if ($oldFileName !== 'user.jpeg' && file_exists($oldFile) && !unlink($oldFile)) {
            Yii::warning("There was a problem deleting old family avatar $oldFile", __METHOD__);
        }

        // If we had no errors, return the updated data
        return $client->family;
    }
}
