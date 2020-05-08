<?php

namespace apivp1\helpers;

use apivp1\models\ProgramGroup;
use common\models\ProgramGroupView;
use Yii;

/**
 * Class ProgramViewHelper
 * @package apivp1\helpers
 */
class ProgramViewHelper
{
    /**
     * Register the fact that a user has viewed a ProgramGroup.
     *
     * @param ProgramGroup $pg
     * @return bool
     */
    public static function recordView(ProgramGroup $pg): bool
    {
        if (($user = Yii::$app->user->identity) === null) {
            return true;
        }

        // Try to find an existing model
        $pgv = ProgramGroupView::findOne([
            'program_group_id' => $pg->id,
            'user_id' => $user->id
        ]);

        // Create a new record if we didn't find one
        if ($pgv === null) {
            $pgv = new ProgramGroupView();
            $pgv->program_group_id = $pg->id;
            $pgv->user_id = $user->id;
        }

        // Add / update the timestamp
        $pgv->timestamp = time();

        if (!$pgv->save()) {
            Yii::warning([
                    'Error saving ProgramGroupView', $pgv->toArray(), $pgv->errors
                ], __METHOD__);
            return false;
        }

        return true;
    }
}
