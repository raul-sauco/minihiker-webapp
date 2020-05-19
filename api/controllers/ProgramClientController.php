<?php

namespace api\controllers;

use api\models\ProgramClient;
use common\controllers\ActiveBaseController;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * Class ProgramClientController
 * @package api\controllers
 */
class ProgramClientController extends ActiveBaseController
{
    public $modelClass = ProgramClient::class;

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
}
