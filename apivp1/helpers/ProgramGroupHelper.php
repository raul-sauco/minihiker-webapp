<?php

namespace apivp1\helpers;

use apivp1\models\ProgramGroup;
use common\models\User;
use Yii;
use yii\db\ActiveQuery;

/**
 * Helper methods for Program Group models.
 *
 * Class ProgramGroupHelper
 * @package app\helpers
 * @author Raul Sauco
 */
class ProgramGroupHelper
{
    /**
     * Check if any of the programs belonging to this program group has the
     * registration_open value set to true and the client limit has not
     * been reached yet.
     * If true for any of the programs, return true, otherwise return false.
     *
     * @param ProgramGroup $programGroup
     * @return bool whether any of the programs in this program group
     * has a registration_open value of true
     */
    public static function isRegistrationOpen(ProgramGroup $programGroup): bool
    {
        foreach ($programGroup->programs as $program) {
            // If any program's registration is open and the client limit has not been reached, return true
            if ($program->registration_open === 1 &&
                (int)$program->client_limit > (int)$program->registrations) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return an ActiveQuery that fetches ProgramGroups recommended for the
     * user passed on as a parameter. It will fetch based on previous navigation
     * history.
     *
     * @param User $user user to query for
     * @return ActiveQuery
     */
    public static function getRecommendedQuery(User $user): ActiveQuery
    {
        Yii::debug("Finding recommended programs for user $user->id", __METHOD__);

        // Find out which programs the user has visited
        $visitedPGs = $user->getProgramGroupsViewed();

        // Create variables to store visited info
        $locationIds = [];
        $typeIds = [];

        /* @var ProgramGroup $pg */
        foreach ($visitedPGs->each() as $pg) {

            $locationIds[] = $pg->location_id;
            $typeIds[] = $pg->type_id;

        }

        // TODO check if we want to return only ProgramGroups that have not taken place yet
        $query = ProgramGroup::find()
            ->where(['weapp_visible' => true])
            ->andWhere(['in', 'location_id', $locationIds])
            ->andWhere(['in', 'type_id', $typeIds]);

        if ($query->count() < 3) {

            Yii::debug(
                'User history does not give enough results, fetch popular',
                __METHOD__
            );

            // TODO fetch programs based on popularity, ie. number of views
            $query = ProgramGroup::find()->where(['weapp_visible' => true])->orderBy('id DESC');

        }

        return $query;
    }
}
