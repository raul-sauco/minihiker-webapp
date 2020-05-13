<?php

namespace apivp1\controllers;

use apivp1\models\Client;
use apivp1\models\Program;
use common\controllers\BaseController;
use common\models\ProgramClient;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class ParticipantController
 * @package app\controllers
 */
class ParticipantController extends BaseController
{
    protected $_verbs = ['GET','OPTIONS','POST','DELETE'];

    /**
     * @param $action
     * @param $client_id
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function checkAccess($action, $client_id): bool
    {
        if ($action === 'view' && Yii::$app->user->can('client')) {
            return true;
        }

        if ($action === 'create' || $action === 'delete') {

            if (Yii::$app->user->can('updateClient',
                ['client_id' => $client_id])) {
                return true;
            }

        }

        throw new ForbiddenHttpException(Yii::t('app',
            'You are not allowed to {action} {resource}.',
            ['action' => $action, 'resource' => Yii::t('app', 'Participants')]));
    }

    /**
     * Find the members of the user family that are participating in a program.
     *
     * @param $program_id int The program id.
     * @return ActiveDataProvider
     * @throws InvalidConfigException
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView($program_id): ActiveDataProvider
    {
        $this->checkAccess('view', '');

        if (($client = Client::findOne(['user_id' => Yii::$app->user->id])) === null) {
            throw new ForbiddenHttpException(
                Yii::t('app',
                    'Only family members can request participant lists')
            );
        }

        if (($program = Program::findOne($program_id)) === null) {
            throw new NotFoundHttpException(
                Yii::t('app',
                    'The resource requested does not exist on this server.')
            );
        }

        // Only return info for the user's family
        $clientQuery = $program->getClients()
            ->where(['family_id' => $client->family_id]);

        return new ActiveDataProvider([
            'query' => $clientQuery
        ]);

    }

    /**
     * Adds a participant from a program.
     *
     * @param $client_id
     * @param $program_id
     * @return array|ProgramClient
     * @throws ForbiddenHttpException
     */
    public function actionCreate ($client_id, $program_id)
    {
        $this->checkAccess('create', $client_id);

        $programClient = new ProgramClient();
        $programClient->program_id = $program_id;
        $programClient->client_id = $client_id;

        if (!$programClient->save()) {
            Yii::$app->response->setStatusCode(422);
            return $programClient->errors;
        }

        Yii::$app->response->setStatusCode(201);
        return $programClient;
    }

    /**
     * Remove a participant from a program.
     *
     * @param $client_id
     * @param $program_id
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete ($client_id, $program_id)
    {
        $this->checkAccess('delete', $client_id);

        if (($programClient = ProgramClient::findOne([
                'program_id' => $program_id,
                'client_id' => $client_id])) === null) {
            throw new NotFoundHttpException(
                Yii::t('app',
                    'The resource requested does not exist on this server.')
            );
        }

        if (!$programClient->delete()) {
            throw new ServerErrorHttpException(
                Yii::t('yii', 'An internal server error occurred.')
            );
        }

        Yii::$app->response->setStatusCode(204);

        return null;
    }
}
