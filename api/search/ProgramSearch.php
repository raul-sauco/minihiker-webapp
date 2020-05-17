<?php

namespace api\search;

use api\models\Program;
use Yii;

/**
 * Class ProgramSearch
 * @package api\search
 */
class ProgramSearch
{
    /**
     * Given the search parameters, try to find one program
     * that fits them.
     *
     * @param $params
     * @return array|Program|\yii\db\ActiveRecord|null
     */
    public static function searchOne($params)
    {
        $query = Program::find()
            ->where(['>=', 'start_date', $params['start-date']])
            ->andWhere(['<=', 'end_date', $params['end-date']]);

        Yii::debug(
            'Search one query returned ' .
            $query->count() . ' results', __METHOD__);
        return $query->one();
    }
}
