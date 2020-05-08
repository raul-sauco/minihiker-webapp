<?php

namespace apivp1\controllers;

use apivp1\models\ProgramGroup;
use apivp1\models\ProgramType;
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
    public function actions()
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
    public function prepareDataProvider()
    {
        $get = Yii::$app->request->get();

        // Check if we are querying only for international or national programs
        if (!empty($get['int']) && $get['int'] === 'true') {

            $locationQuery = ['location.international' => true];

        } else {

            $locationQuery = ['location.international' => false];

        }

        // Find all the weapp visible programs starting today of after. Int or Nat.
        $query = ProgramGroup::find()->joinWith(['location', 'programs'])
            ->where([
                'and',
                $locationQuery,
                // ['>=', 'program.start_date', date('Y-m-d')],
                ['weapp_visible' => true]
            ])->distinct();

        // Check for type filtering
        if (!empty($get['type'])) {

            // Check that we can find a matching program_type
            if (($pt = ProgramType::findOne(['name' => $get['type']])) !== null) {

                $query->andWhere(['type_id' => $pt->id]);

            } else {

                // We couldn't find the corresponding programType
                Yii::warning(
                    'Could not find ProgramType for string: ' . $get['type'],
                    __METHOD__
                );

            }

        }

        if (!empty($get['q'])) {

            // $tokens = explode(',', $get['q']);
            $tokens = preg_split('/[\s\n,.，。]+/u', $get['q'], -1, PREG_SPLIT_NO_EMPTY);

            $attrs = [
                'theme','summary','keywords','location_id','location.name_en'
            ];

            $searchQuery = ['or'];

            foreach ($tokens as $token) {

                $token = trim($token);

                if (!empty($token) && $token !== '单飞' && $token !== '亲子') {

                    foreach ($attrs as $attr) {

                        $searchQuery[] = ['like', $attr, $token];

                    }

                }

            }

            $query->andWhere($searchQuery);

            if (strpos($get['q'], '单飞') !== false) {

                $query->andWhere(['accompanied' => false]);

            } elseif (strpos($get['q'], '亲子') !== false) {

                $query->andWhere(['accompanied' => true]);

            }

        }

        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
}
