<?php

namespace common\helpers;

use common\models\Family;
use common\models\Program;
use common\models\ProgramFamily;
use Yii;

/**
 * Helper functionality for ProgramFamily model.
 *
 * Class ProgramFamilyHelper
 * @package common\helpers
 */
class ProgramFamilyHelper
{
    /**
     * Links a Program model and a Family model after performing the checks to make
     * sure it's safe to do so.
     *
     * @param int $programId
     * @param int $familyId
     * @return boolean
     */
    public static function safeLink(int $programId, int $familyId): bool
    {
        Yii::debug(
            "Linking Program $programId and Family $familyId",
            __METHOD__
        );

        if (($program = Program::findOne($programId)) === null) {
            Yii::error(
                "Trying to link Family $familyId with null Program $programId",
                __METHOD__);
            return false;
        }

        if (($family = Family::findOne($familyId)) === null) {
            Yii::error(
                "Trying to link Program $programId with null Family $familyId",
                __METHOD__
            );
            return false;
        }
        if (ProgramFamily::findOne([
            'program_id' => $program->id, 'family_id' => $family->id]) !== null) {
            Yii::info(
                "Already existing ProgramFamily ($programId,$familyId)",
                __METHOD__
            );
            return true;
        }
        // There were no errors, link the models. $model-link() fails to add timestamps and creator.
        $programFamily = new ProgramFamily();
        $programFamily->program_id = $programId;
        $programFamily->family_id = $familyId;
        if (!$programFamily->save()) {
            $msg = "Error linking program $programId and family $familyId";
            Yii::error($msg, __METHOD__);
            return false;
        }
        return true;
    }
}
