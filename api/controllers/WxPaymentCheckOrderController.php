<?php

namespace api\controllers;

use common\helpers\WxPaymentHelper;
use common\models\WxUnifiedPaymentOrder;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class WxPaymentCheckOrderController
 * @package api\controllers
 */
class WxPaymentCheckOrderController extends \common\controllers\ActiveBaseController
{
    public $modelClass = WxUnifiedPaymentOrder::class;
    protected $_verbs = ['PATCH','PUT','OPTIONS'];

    /**
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();
        unset(
            $actions['delete'],
            $actions['create'],
            $actions['update'],
            $actions['view'],
            $actions['index'],
        );
        return $actions;
    }

    /**
     * @param string $action
     * @param null $model
     * @param array $params
     * @return bool
     * @throws yii\web\ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = []): bool
    {
        // Let staff check the current status of an order.
        if ($action === 'update') {
            return Yii::$app->user->can('user');
        }
        return parent::checkAccess($action, $model, $params);
    }

    /**
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws yii\base\Exception
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('user')) {
            throw new ForbiddenHttpException(
                Yii::t('app',
                    'You are not allowed to access this resource')
            );
        }
        $order = WxUnifiedPaymentOrder::findOne($id);
        if ($order === null) {
            throw new NotFoundHttpException(
                Yii::t('app',
                    'The resource requested does not exist on this server.')
            );
        }

        $response = WxPaymentHelper::checkOrderStatus($order);
        if ($response === null) {
            return [
                'error' => true,
                'message' => 'Got null response'
            ];
        }
        return $response;
    }
}
