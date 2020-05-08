<?php

namespace apivp1\search;

use apivp1\models\ProgramGroup;
use yii\data\ActiveDataProvider;

class ProgramGroupSearch
{
    /**
     * Filter ProgramGroup results based on being visible in the banner
     * and the given parameters.
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public static function searchInBanner(array $params)
    {
        $query = ProgramGroup::find()->joinWith('location')
            ->where(['weapp_in_banner' => true, 'weapp_visible' => true]);

        // Check if we are querying only for international or national programs
        if (!empty($params['int'])) {

            $query->andWhere(['location.international' => $params['int'] === 'true']);

        }

        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
}
