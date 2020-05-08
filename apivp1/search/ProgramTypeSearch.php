<?php

namespace apivp1\search;

use apivp1\models\ProgramType;
use yii\data\ActiveDataProvider;

/**
 * Class ProgramTypeSearch
 * @package apivp1\search
 */
class ProgramTypeSearch
{
    /**
     * Filter ProgramType results based on the given parameters.
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public static function search(array $params)
    {
        $query = ProgramType::find();

        if (!empty($params['weapp-visible']) && $params['weapp-visible'] === 'true') {

            $query->joinWith('programGroups.location');

            $query->where(['program_group.weapp_visible' => true]);

            if (!empty($params['int'])) {

                if ($params['int'] === 'true') {
                    $query->andWhere(['location.international' => 1]);
                } elseif ($params['int'] === 'false') {
                    $query->andWhere(['location.international' => 0]);
                }
            }
        }

        return new ActiveDataProvider(['query' => $query]);
    }
}
