<?php

namespace apivp1\controllers;

use apivp1\models\WeappLog;
use common\controllers\ActiveBaseController;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * Class WeappLogController
 * @package apivp1\controllers
 */
class WeappLogController extends ActiveBaseController
{
    public $modelClass = WeappLog::class;

    /**
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['update'],$actions['delete']);
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
        if ($action === 'create') { return true; }
        if ($action === 'view' || $action === 'index') {
            if (!Yii::$app->user->can('user')) {
                return true;
            }
            throw new ForbiddenHttpException(Yii::t('app',
                'You are not allowed to view logs',
                ['client' => $model->id]));
        }
        return parent::checkAccess($action, $model, $params);
    }
}
