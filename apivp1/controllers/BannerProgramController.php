<?php

namespace apivp1\controllers;

use apivp1\models\ProgramGroup;
use apivp1\search\ProgramGroupSearch;
use common\controllers\ActiveBaseController;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class BannerProgramGroupController
 * @package apivp1\controllers
 */
class BannerProgramController extends ActiveBaseController
{
    public $modelClass = ProgramGroup::class;

    protected $_verbs = ['GET','OPTIONS'];

    /**
     * Unable actions that modify the data.
     *
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['delete'], $actions['create'], $actions['update']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
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
     * @param string $action
     * @param null $model
     * @param array $params
     * @return bool
     * @throws \yii\web\ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = []): bool
    {
        if ($action === 'options' || $action === 'index') {
            return true;
        }

        return parent::checkAccess($action, $model, $params);
    }

    /**
     * Prepare the data to be returned by the index action.
     * @return ActiveDataProvider
     */
    public function prepareDataProvider()
    {
        return ProgramGroupSearch::searchInBanner(Yii::$app->request->get());
    }
}
