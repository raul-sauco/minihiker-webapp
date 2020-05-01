<?php

namespace backend\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\helpers\SearchHelper;

class SearchController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['admin', 'user'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    // 'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Displays the main search page.
     *
     * @return string
     */
    public function actionIndex($query = null)
    {
        $results = null;
        
        $helper = new SearchHelper();
        
        $results = $helper->search($query);
        
        return $this->render('index', [
            'results' => $results,
        ]);
    }
}

