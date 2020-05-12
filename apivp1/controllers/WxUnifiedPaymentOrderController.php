<?php

namespace apivp1\controllers;

use apivp1\models\Client;
use apivp1\models\WxUnifiedPaymentOrder;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;

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
        if ($action === 'index') {

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
            ->with('price.program.programGroup');

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);
    }
}
