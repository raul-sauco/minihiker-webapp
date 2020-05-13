<?php

namespace apivp1\controllers;

use apivp1\helpers\ProgramGroupHelper;
use apivp1\models\ProgramGroup;
use apivp1\models\User;
use common\controllers\ActiveBaseController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;

/**
 * Class WxProgramVisitHistoryController
 * Route requests for programs that have been visited by
 * the current application user.
 *
 * @package app\controllers
 */
class WxProgramRecommendationsController extends ActiveBaseController
{
    public $modelClass = ProgramGroup::class;

    protected $_verbs = ['GET','OPTIONS'];

    /**
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['delete'], $actions['create'], $actions['update']);
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
        if ($action === 'index') {
            return Yii::$app->user->can('client');
        }
        return parent::checkAccess($action, $model, $params);
    }

    /**
     * Prepare the data that will be returned by the index action.
     *
     * Find the application user and return a data provider containing
     * ProgramGroups that the user may be interested on.
     *
     * @return ActiveDataProvider
     * @throws ForbiddenHttpException
     */
    public function prepareDataProvider(): ActiveDataProvider
    {
        // User::findOne returns an apivp1\models\User instead of common
        if (($user = User::findOne(Yii::$app->user->id)) === null) {
            throw new ForbiddenHttpException(
                Yii::t('app',
                    'You do not have a client account, we cannot find linked programs.'
                )
            );
        }

        return new ActiveDataProvider([
            'query' => ProgramGroupHelper::getRecommendedQuery($user)
        ]);
    }
}
