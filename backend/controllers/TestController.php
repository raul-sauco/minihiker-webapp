<?php

namespace backend\controllers;

use common\helpers\ClientHelper;
use common\helpers\ProgramFamilyHelper;
use common\helpers\WxPaymentHelper;
use common\models\WxUnifiedPaymentOrder;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\ServerErrorHttpException;

/**
 * Class TestController.
 * Visual feedback and GUI controls for some backend maintenance tasks.
 * @package backend\controllers
 */
class TestController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'fix-orphaned-program-families',
                            'fix-orphaned-clients',
                            'payment-order',
                            'update-payment-order-status'
                        ],
                        'allow' => true,
                        'roles' => ['admin']
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
//                    'logout' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Run a few tests on the app and display the results on-screen.
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }

    /**
     * Display Wx Unified Payment orders that may have issues with them.
     * @return string
     */
    public function actionPaymentOrder() : string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => WxPaymentHelper::getOrdersWithAbnormalStatus()
        ]);
        return $this->render('payment-order', ['dataProvider' => $dataProvider]);
    }

    /**
     * Fix orphaned program families action.
     * @throws ServerErrorHttpException
     */
    public function actionUpdatePaymentOrderStatus(): void
    {
        WxPaymentHelper::updateExpiredOrdersStatus();
        $this->redirect('payment-order');
    }

    /**
     * Fix orphaned program families action.
     * @throws ServerErrorHttpException
     */
    public function actionFixOrphanedProgramFamilies(): void
    {
        // Delegate on the helper, throws if fail
        ProgramFamilyHelper::fixOrphanedProgramFamilies();
        $this->redirect('index');
    }

    /**
     * Fix orphaned clients action.
     * @throws ServerErrorHttpException
     */
    public function actionFixOrphanedClients(): void
    {
        ClientHelper::fixOrphanedClients();
        $this->redirect('index');
    }
}
