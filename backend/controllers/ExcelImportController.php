<?php

namespace backend\controllers;

use yii\web\Controller;

class ExcelImportController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
