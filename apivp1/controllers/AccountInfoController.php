<?php

namespace apivp1\controllers;

use apivp1\models\Client;
use apivp1\models\Family;
use common\controllers\BaseController;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

/**
 * Class AccountInfoController
 * @package apivp1\controllers
 */
class AccountInfoController extends BaseController
{
    protected $_verbs = ['GET', 'OPTIONS'];

    /**
     * @return Family
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionIndex(): Family
    {
        if (($user = Yii::$app->user->identity) === null) {
            throw new BadRequestHttpException(
                'You need to authenticate to access your own account information'
            );
        }

        if (($client = Client::findOne(['user_id' => $user->id])) === null) {
            throw new ForbiddenHttpException(
                'No client account found for current user'
            );
        }

        return $client->family;
    }
}
