<?php

namespace api\controllers;

use api\search\ClientSearch;
use common\controllers\BaseController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

/**
 * Class ClientSearchController
 * @package api\controllers
 */
class ClientSearchController extends BaseController
{
    /**
     * @return ActiveDataProvider
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
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

        return ClientSearch::search(Yii::$app->request->get());
    }
}
