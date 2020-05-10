<?php

namespace apivp1\controllers;

use apivp1\models\Family;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class FamilyController
 * @package apivp1\controllers
 */
class FamilyController extends ActiveBaseController
{
    public $modelClass = Family::class;

    protected $_verbs = ['GET', 'OPTIONS','PUT','PATCH'];

    /**
     * Unable actions that modify the data.
     *
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['update'], $actions['create']);

        return $actions;
    }

    /**
     * @param string $action
     * @param null $model
     * @param array $params
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = []): bool
    {
        if ($action === 'view') {

            if (Yii::$app->user->can('viewFamily', ['family_id' => $model->id])) {
                return true;
            }

            throw new ForbiddenHttpException(Yii::t('app',
                'You are not allowed to view family {family}',
                ['family' => $model->id]));

        }

        if ($action === 'update') {

            if (!Yii::$app->user->can('updateFamily', ['family_id' => $model->id])) {
                return true;
            }

            throw new ForbiddenHttpException(Yii::t('app',
                'You are not allowed to update family {family}',
                ['family' => $model->id]));

        }

        return parent::checkAccess($action, $model, $params);
    }

    /**
     * Customize the update action to set correct scenario when clients update their own data.
     *
     * @param $id
     * @return Family
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws InvalidConfigException
     */
    public function actionUpdate($id): Family
    {
        if (($model = Family::findOne($id)) === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'The resource requested does not exist on this server.')
            );
        }

        if (!Yii::$app->user->can('user')) {

            // The current user is not MH staff
            $model->setScenario(Family::SCENARIO_UPDATE_SELF_ACCOUNT);

        }

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException(
                Yii::t('app', 'Failed to update the resource for an unknown reason.')
            );
        }

        return $model;
    }
}
