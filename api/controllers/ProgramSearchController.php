<?php

namespace api\controllers;

use api\search\ProgramSearch;
use common\controllers\BaseController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

/**
 * Class ProgramSearchController
 * @package api\controllers
 */
class ProgramSearchController extends BaseController
{
    /**
     * @return ActiveDataProvider
     * @throws ForbiddenHttpException
     * @throws BadRequestHttpException
     */
    public function actionIndex(): ActiveDataProvider
    {
        if (!Yii::$app->user->can('user')) {
            throw new ForbiddenHttpException(
                Yii::t('app',
                    'You are not allowed to access this resource'
                )
            );
        }

        $get = Yii::$app->request->get();
        if (empty($get['start-date']) || empty($get['end-date'])) {
            throw new BadRequestHttpException(
                Yii::t('app', 'Missing required parameters')
            );
        }
        return ProgramSearch::search(Yii::$app->request->get());
    }
}
