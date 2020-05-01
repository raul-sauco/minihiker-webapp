<?php

namespace backend\controllers;

use Yii;
use common\models\WxUnifiedPaymentOrder;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WxUnifiedPaymentOrderController implements the CRUD actions for WxUnifiedPaymentOrder model.
 */
class WxUnifiedPaymentOrderController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index','view'],
                        'roles' => ['admin','user'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all WxUnifiedPaymentOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => WxUnifiedPaymentOrder::find(),
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WxUnifiedPaymentOrder model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the WxUnifiedPaymentOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WxUnifiedPaymentOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): WxUnifiedPaymentOrder
    {
        if (($model = WxUnifiedPaymentOrder::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
