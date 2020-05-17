<?php

namespace api\controllers;

use api\search\ProgramSearch;
use common\controllers\BaseController;
use common\models\Program;
use Yii;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

/**
 * Class ProgramSearchController
 * @package api\controllers
 */
class ProgramSearchController extends BaseController
{
    /**
     * @return array|Program|ActiveRecord|null
     * @throws ForbiddenHttpException
     * @throws BadRequestHttpException
     */
    public function actionIndex()
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
        return ProgramSearch::searchOne(Yii::$app->request->get());
    }
}
