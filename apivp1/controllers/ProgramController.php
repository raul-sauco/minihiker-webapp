<?php

namespace apivp1\controllers;

use apivp1\models\Program;
use common\controllers\ActiveBaseController;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * Class ProgramController
 * @package apivp1\controllers
 */
class ProgramController extends ActiveBaseController
{
    public $modelClass = Program::class;

    protected $_verbs = ['GET','OPTIONS'];

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

            // Let clients see their own orders
            if (Yii::$app->user->can('client')) {
                // TODO may need to make sure what data gets served here
                return true;
            }

            throw new ForbiddenHttpException(
                Yii::t('yii',
                    'You are not allowed to perform this action.')
            );
        }

        return parent::checkAccess($action, $model, $params);
    }

    /**
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();
        unset(
            $actions['index'],
            $actions['delete'],
            $actions['create'],
            $actions['update']
        );
        return $actions;
    }
}
