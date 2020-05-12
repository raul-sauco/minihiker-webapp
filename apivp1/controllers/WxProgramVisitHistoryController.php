<?php

namespace apivp1\controllers;

use apivp1\models\ProgramGroup;
use apivp1\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;

/**
 * Class WxProgramVisitHistoryController
 * Route requests for programs that have been visited by
 * the current application user.
 *
 * @package app\controllers
 */
class WxProgramVisitHistoryController extends ActiveBaseController
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
        $actions['index']['prepareDataProvider'] =
            [$this, 'prepareDataProvider'];
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
        if (($action === 'index') &&
            Yii::$app->user->can('client')) {
            return true;
        }
        return parent::checkAccess($action, $model, $params);
    }

    /**
     * Prepare the data that will be returned by the index action.
     *
     * Find the application user and return a data provider containing
     * ProgramGroups that the user has visited.
     *
     * @return ActiveDataProvider
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     */
    public function prepareDataProvider()
    {
        // Yii::$app->user->identity returns \common\models\User
        $user = User::findOne(Yii::$app->user->id);

        if ($user === null) {
            throw new ForbiddenHttpException(
                Yii::t('app',
                    'You do not have a client account, ' .
                    'we cannot find linked programs.')
            );
        }

        return new ActiveDataProvider([
            'query' => $user->getProgramGroupsViewed()
                ->joinWith('programGroupViews')
                ->where(['weapp_visible' => 1])
                //->orderBy(['program_group_view.timestamp' => SORT_DESC])
                ->distinct(),
            'pagination' => [
                'params' => Yii::$app->request->get()
            ]
        ]);
    }
}
