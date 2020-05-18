<?php

namespace api\search;

use api\models\Program;
use yii\data\ActiveDataProvider;

/**
 * Class ProgramSearch
 * @package api\search
 */
class ProgramSearch
{
    /**
     * Given the search parameters, find programs that
     * fit the parameters.
     *
     * @param $params
     * @return ActiveDataProvider
     */
    public static function search($params): ActiveDataProvider
    {
        $query = Program::find()
            ->where(['>=', 'start_date', $params['start-date']])
            ->andWhere(['<=', 'end_date', $params['end-date']]);
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
}
