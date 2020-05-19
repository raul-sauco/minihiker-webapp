<?php

namespace api\search;

use api\models\Client;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;

class ClientSearch
{
    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws BadRequestHttpException
     */
    public static function search($params): ActiveDataProvider
    {
        if (empty($params['id'])) {
            throw new BadRequestHttpException(
                Yii::t('app', 'Missing required parameters')
            );
        }

        $query = Client::find()
            ->where(['id_card_number' => $params['id']]);

        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
}
