<?php

namespace api\controllers;

use api\models\ProgramClient;
use common\controllers\ActiveBaseController;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class ProgramClientController
 * @package api\controllers
 */
class ProgramClientController extends ActiveBaseController
{
    public $modelClass = ProgramClient::class;

    /**
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();
        unset(
            $actions['delete'],
            $actions['update'],
            $actions['view']
        );
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
        if ($action === 'create') {
            return Yii::$app->user->can('user');
        }
        return parent::checkAccess($action, $model, $params);
    }

    /**
     * @param $program_id
     * @param $client_id
     * @return ProgramClient
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView($program_id, $client_id): ProgramClient
    {
        if (!Yii::$app->user->can('updateProgram')) {
            throw new ForbiddenHttpException(
                Yii::t('app',
                    'You are not allowed to access this resource')
            );
        }
        $programClient = ProgramClient::findOne([
            'program_id' => $program_id,
            'client_id' => $client_id
        ]);
        if ($programClient === null) {
            throw new NotFoundHttpException(
                Yii::t('app',
                    'The resource requested does not exist on this server.')
            );
        }
        return $programClient;
    }
}
