<?php

namespace apivp1\controllers;

use apivp1\helpers\ProgramSearchHelper;
use apivp1\models\ProgramGroup;
use apivp1\models\ProgramType;
use common\controllers\ActiveBaseController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;

/**
 * Class WeappProgramSearchController
 * @package app\controllers
 */
class ProgramSearchController extends ActiveBaseController
{
    public $modelClass = ProgramGroup::class;

    protected $_verbs = ['GET','OPTIONS'];

    /**
     * Unable actions that modify the data.
     *
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();

        unset(
            $actions['delete'],
            $actions['create'],
            $actions['update']
        );

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

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
        if ($action === 'options' || $action === 'index') {
            return true;
        }

        return parent::checkAccess($action, $model, $params);
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['options','index'];
        return $behaviors;
    }

    /**
     * Prepare the data that will be returned by the index action.
     *
     * @return ActiveDataProvider
     */
    public function prepareDataProvider(): ActiveDataProvider
    {
        return ProgramSearchHelper::getDataProvider(Yii::$app->request->get());
    }
}
