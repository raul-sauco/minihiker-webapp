<?php

namespace api\controllers;

use api\models\ProgramPrice;
use common\controllers\ActiveBaseController;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class ProgramPriceController
 * @package api\controllers
 */
class ProgramPriceController extends ActiveBaseController
{
    public $modelClass = ProgramPrice::class;

    /**
     * Unable actions that modify the data.
     *
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    /**
     * @return ActiveDataProvider
     */
    public function prepareDataProvider(): ActiveDataProvider
    {
        $query = ProgramPrice::find();
        if (Yii::$app->request->get('program-id') !== null) {
            $query->where([
                'program_id' => Yii::$app->request->get('program-id')
            ]);
        }
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [1,1000],
                'defaultPageSize' => 1000
            ],
        ]);
    }
}
