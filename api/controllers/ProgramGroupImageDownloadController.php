<?php

namespace api\controllers;

use api\models\ProgramGroup;
use common\controllers\BaseController;
use common\helpers\WxContentHelper;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class ProgramGroupImageDownload
 * @package api\controllers
 */
class ProgramGroupImageDownloadController extends BaseController
{
    protected $_verbs = ['PATCH', 'OPTIONS'];

    /**
     * Try to download all the images in a program group
     * that are hosted in external servers to the
     * local server.
     * @param $id
     * @return ProgramGroup
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionUpdate($id): ProgramGroup
    {
        $model = ProgramGroup::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException(
                Yii::t('app',
                    'The resource requested does not exist on this server.'
                )
            );
        }
        if (!WxContentHelper::copyImagesToLocalServer($model)) {
            Yii::warning(
                'Failed to copy images to local server for ' .
                "ProgramGroup $model->id", __METHOD__);
            throw new ServerErrorHttpException(
                Yii::t('yii', 'An internal server error occurred.')
            );
        }
        return $model;
    }
}
