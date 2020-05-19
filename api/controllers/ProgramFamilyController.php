<?php

namespace api\controllers;

use api\models\ProgramFamily;
use common\controllers\ActiveBaseController;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * Class ProgramFamilyController
 * @package api\controllers
 */
class ProgramFamilyController extends ActiveBaseController
{
    public $modelClass = ProgramFamily::class;

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
