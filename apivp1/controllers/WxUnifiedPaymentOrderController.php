<?php

namespace apivp1\controllers;

use apivp1\models\Client;
use apivp1\models\WxUnifiedPaymentOrder;
use common\controllers\ActiveBaseController;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class WxUnifiedPaymentOrderController
 * @package apivp1\controllers
 */
class WxUnifiedPaymentOrderController extends ActiveBaseController
{
    public $modelClass = WxUnifiedPaymentOrder::class;

    protected $_verbs = ['GET','OPTIONS'];

    /**
     * Disable actions that modify the data.
     *
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();

        // disable most actions
        unset(
            $actions['delete'],
            $actions['create'],
            $actions['update'],
            $actions['view']
        );

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
        if ($action === 'index' || $action === 'update') {

            // Let clients see their own orders
            if (Yii::$app->user->can('client')) {
                return true;
            }

            throw new ForbiddenHttpException(
                Yii::t('yii',
                    'You are not allowed to perform this action.')
            );
        }

        return parent::checkAccess($action, $model, $params);
    }

    /**
     * Fetch payment orders. There are two use cases:
     *
     *  Users/Admins - will get a full list of all the payments in the system.
     *  Clients      - will get a list of their own (Family) payments.
     *
     * @return ActiveDataProvider
     * @throws ForbiddenHttpException
     */
    public function prepareDataProvider()
    {
        if (($client = Client::findOne(['user_id' => Yii::$app->user->id])) === null) {
            throw new ForbiddenHttpException(
                Yii::t('yii',
                    'You are not allowed to perform this action.')
            );
        }

        $query = WxUnifiedPaymentOrder::find()
            ->where(['family_id' => $client->family_id])
            ->andWhere(['hidden' => false])
            ->with('price.program.programGroup');

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);
    }

    /**
     * @param $id
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws InvalidConfigException
     */
    public function actionUpdate($id): WxUnifiedPaymentOrder
    {
        $client = Client::findOne(['user_id' => Yii::$app->user->id]);
        $paymentOrder = WxUnifiedPaymentOrder::findOne($id);
        if ($paymentOrder === null) {
            throw new NotFoundHttpException(
                Yii::t('app',
                    'The resource requested does not exist on this server.')
            );
        }
        if ($client === null || $client->family_id !== $paymentOrder->family_id) {
            throw new ForbiddenHttpException(
                Yii::t('yii',
                    'You are not allowed to perform this action.')
            );
        }
        // Clients can only hide orders
        Yii::warning(Yii::$app->getRequest()->getBodyParams(), __METHOD__);
        $hidden = Yii::$app->getRequest()->getBodyParams()['hidden'];
        if ((int)$hidden === 1) {
            $paymentOrder->hidden = 1;
            if (!$paymentOrder->save()) {
                throw new ServerErrorHttpException(
                    Yii::t('yii', 'An internal server error occurred.')
                );
            }
        }
        return $paymentOrder;
    }
}
