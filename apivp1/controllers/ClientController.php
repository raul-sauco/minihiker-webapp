<?php

namespace apivp1\controllers;

use apivp1\models\Client;
use common\helpers\ProgramHelper;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\helpers\Url;
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

        // Use the actions from this file
        unset($actions['update'], $actions['create'], $actions['delete']);

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

        return parent::checkAccess($action, $model, $params);
    }

    /**
     * Create a client from POST request.
     *
     * @return Client|array|null
     * @throws ForbiddenHttpException
     * @throws ServerErrorHttpException
     * @throws InvalidConfigException
     */
    public function actionCreate ()
    {
        if (!Yii::$app->user->can('createClient')) {
            throw new ForbiddenHttpException(Yii::t('app',
                'You are not allowed to create a new client.'));
        }

        $model = new Client();

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if (!Yii::$app->user->can('user')) {

            // Not an admin or user type, set the family
            $model->setScenario(Client::SCENARIO_FAMILY_CREATE_MEMBER);

            $client = Client::findOne(['user_id' => Yii::$app->user->id]);

            if ($client === null) {
                throw new ServerErrorHttpException(
                    Yii::t('app', 'Failed to update the resource for an unknown reason.')
                );
            }

            if ($client->family->getClients()->count() > Yii::$app->params['maxFamilyMembersAllowed']) {
                throw new ForbiddenHttpException(
                    Yii::t('app', 'You have exceeded the limit of family members allowed.')
                );
            }

            $model->family_id = $client->family_id;

        }

        if (!$model->save()) {

            if ($model->hasErrors()) {
                Yii::$app->response->setStatusCode(422);
                return $model->errors;
            }

            throw new ServerErrorHttpException(
                'Server error'
            );

        }

        // Model was save, prepare the response
        $response = Yii::$app->getResponse();
        $response->setStatusCode(201);
        $response->getHeaders()->set(
            'Location', Url::toRoute(['view', 'id' => $model->id], true));

        return $model;
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
        if (!Yii::$app->user->can('updateClient', ['client_id' => $id])) {
            throw new ForbiddenHttpException(Yii::t('app',
                'You are not allowed to update client {client}',
                ['client' => $id]));
        }

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

    /**
     * @param $id
     * @return Client|null
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionDelete ($id): ?Client
    {
        if (!Yii::$app->user->can('deleteClient', ['client_id' => $id])) {
            throw new ForbiddenHttpException(Yii::t('app',
                'You are not allowed to delete client {client}',
                ['client' => $id]));
        }

        if (($model = Client::findOne($id)) === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'The resource requested does not exist on this server.')
            );
        }

        try {
            if ($model->delete() === false) {
                throw new Exception('Client::delete returned false');
            }
        } catch (Exception $e) {
            Yii::warning(["Error deleting client $model->id", $e], __METHOD__);
            throw new ServerErrorHttpException(
                Yii::t('app', 'Server error deleting Client')
            );
        }

        Yii::$app->response->setStatusCode(204);
        return null;
    }
}
