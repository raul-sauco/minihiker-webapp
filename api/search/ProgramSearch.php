<?php

namespace api\search;

use api\models\Location;
use api\models\Program;
use api\models\ProgramType;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

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
        $query = Program::find()->joinWith('programGroup pg')
            ->where(['>=', 'start_date', $params['start-date']])
            ->andWhere(['<=', 'end_date', $params['end-date']]);

        // Try to filter by type
        if ($query->count() > 1) {
            if (strpos($params['name'], '亲子') !== false) {
                $query->andWhere(['pg.accompanied' => 1]);
            } elseif (strpos($params['name'], '单飞') !== false) {
                $query->andWhere(['pg.accompanied' => 0]);
            }
        }

        // Filter by vacation-type
        if ($query->count() > 1) {
            $query->andWhere([
                'pg.type_id' => self::getMatchingTypeIds($params['name'])
            ]);
        }

        // Filter by location
        if ($query->count() > 1) {
            // Location is too aggressive sometimes
            $backupQuery = clone $query;
            $query->andWhere([
                'pg.location_id' => self::getMatchingLocationIds($params['name'], $query)
            ]);
            if ($query->count() < 1) {
                // We have gone too far
                $query = $backupQuery;
            }
        }

        return new ActiveDataProvider([
            'query' => $query
        ]);
    }

    /**
     * Get an array of ProgramType ids that match the name
     * parameter
     * @param $name
     * @return array
     */
    private static function getMatchingTypeIds($name): array
    {
        $ids = [];
        foreach (ProgramType::find()->each() as $pt) {
            if (strpos($name, $pt->name) !== false) {
                $ids[] = $pt->id;
            }
        }
        // Staff sometimes use the short version of 单日活动
        if ((strpos($name, '单日') !== false) &&
            !in_array(11, $ids, true)) {
            $ids[] = 11;
        }
        return $ids;
    }

    /**
     * Return a list of Location ids that match the name parameter
     * @param $name string
     * @param $query ActiveQuery
     * @return array
     */
    private static function getMatchingLocationIds($name, $query): array
    {
        $matchedIds = [];
        /** @var Program $p */
        foreach ($query->all() as $p) {
            // Location uses the Chinese name as the primary key
            $location = $p->programGroup->location_id;
            if (!in_array($location, $matchedIds, true) &&
                (strpos($name, $location) !== false)) {
                $matchedIds[] = $location;
            }
        }
        return $matchedIds;
    }
}
