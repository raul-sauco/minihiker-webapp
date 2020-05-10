<?php

namespace apivp1\controllers;

use apivp1\models\Client;
use common\helpers\ProgramHelper;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class ClientController
 * @package apivp1\controllers
 */
class ClientController extends ActiveBaseController
{
    public $modelClass = Client::class;

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

            if (Yii::$app->user->can('viewClient', ['client_id' => $model->id])) {
                return true;
            }

            throw new ForbiddenHttpException(Yii::t('app',
                'You are not allowed to view client {client}',
                ['client' => $model->id]));

        }

        if ($action === 'update') {

            if (!Yii::$app->user->can('updateClient', ['client_id' => $model->id])) {
                return true;
            }

            throw new ForbiddenHttpException(Yii::t('app',
                'You are not allowed to update client {client}',
                ['client' => $model->id]));

        }

        return parent::checkAccess($action, $model, $params);
    }


    /**
     * @param $id
     * @return Client|null
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws InvalidConfigException
     */
    public function actionUpdate ($id): Client
    {
        if (($model = Client::findOne($id)) === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'The resource requested does not exist on this server.')
            );
        }

        if (!Yii::$app->user->can('user')) {

            // The user application is a client
            $model->setScenario(Client::SCENARIO_FAMILY_UPDATE_MEMBER);

        }

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException(
                Yii::t('app', 'Failed to update the resource for an unknown reason.')
            );
        }

        // Update the 'updated_at' attribute on related programs
        ProgramHelper::markClientProgramAsUpdated($model);

        return $model;
    }
}
