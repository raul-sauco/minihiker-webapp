<?php

namespace apivp1\controllers;

use apivp1\helpers\ProgramViewHelper;
use apivp1\models\ProgramGroup;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class ProgramGroupController
 * @package apivp1\controllers
 */
class ProgramGroupController extends BaseController
{
    protected $_verbs = ['GET','OPTIONS'];

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['options'];
        return $behaviors;
    }

    /**
     * Handle program-group/{id} route.
     * This is a public route.
     *
     * @param $id
     * @return ProgramGroup|null
     * @throws NotFoundHttpException
     */
    public function actionView($id): ?ProgramGroup
    {
        if (($model = ProgramGroup::findOne($id)) === null) {
            throw new NotFoundHttpException(
                Yii::t('yii', 'No results found.')
            );
        }

        // Mark the ProgramGroup as viewed by the user now.
        ProgramViewHelper::recordView($model);

        return $model;
    }
}
