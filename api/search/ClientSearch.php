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
        if (empty($params['id']) &&
            empty($params['passport']) &&
            empty($params['name'])) {
                throw new BadRequestHttpException(
                    Yii::t('app', 'Missing required parameters')
                );
        }

        $query = Client::find();

        if (!empty($params['id'])) {
            $query->where(['id_card_number' => $params['id']]);
        } elseif (!empty($params['passport'])) {
            $query->where(['passport_number' => $params['passport']]);
        } else {
            $query->where(['like', 'name_zh', $params['name']]);
        }

        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
}
