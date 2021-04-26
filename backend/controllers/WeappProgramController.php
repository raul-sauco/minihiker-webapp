<?php

namespace backend\controllers;

use common\models\WeappProgramSearch;
use Yii;
use yii\web\Controller;

/**
 * Class WeappProgramController
 * Actions related to visualizing Programs in the Wechat Mini program.
 *
 * @package backend\controllers
 * @author Raul Sauco
 */
class WeappProgramController extends Controller
{
    public function actionIndex($q = '')
    {
        $searchModel = new WeappProgramSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'q' => $q
        ]);

    }
}
