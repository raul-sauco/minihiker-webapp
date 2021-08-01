<?php

namespace common\helpers;

use common\models\Family;
use common\models\Program;
use common\models\ProgramClient;
use common\models\ProgramFamily;
use Throwable;
use Yii;
use yii\db\ActiveQuery;
use yii\db\StaleObjectException;
use yii\web\ServerErrorHttpException;

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


    /**
     * Unlink a Program record and a Family record by deleting the corresponding
     * entry on the program_family table.
     *
     * @param int $programId
     * @param int $familyId
     * @return boolean whether the deletion was successful.
     * @throws ServerErrorHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public static function safeUnLink(int $programId, int $familyId): bool
    {
        Yii::debug(
            "Unlinking Program $programId and family $familyId",
            __METHOD__
        );
        if (($program = Program::findOne($programId)) === null) {
            Yii::error(
                "Trying to unlink Family $familyId with null Program $programId",
                __METHOD__);
            return false;
        }
        if (($family = Family::findOne($familyId)) === null) {
            Yii::error(
                "Trying to unlink Program $programId with null Family $familyId",
                __METHOD__
            );
            return false;
        }
        if (($programFamily = ProgramFamily::findOne(['program_id' => $programId,
                'family_id' => $familyId])) === null) {
            Yii::error(
                "Trying to unlink null ProgramFamily p $programId f $familyId",
                __METHOD__);
            return false;
        }

        // There were no errors, both models and the link exists
        // Check to see if there are any other clients from that family on the program
        if (self::hasClientsInProgram($family, $program->id)) {
            Yii::debug(
                "Family $familyId still has clients in program $programId. Preventing unlink",
                __METHOD__);
            return true;

        }
        // No clients left from the current family. Unlink the Family from the Program.
        if (!$programFamily->delete()) {
            $msg = "Error unlinking Program $programId and Family $familyId";
            Yii::error($msg, __METHOD__);
            throw new ServerErrorHttpException($msg);
        }

        return true;
    }

    /**
     * Returns whether this Family has any member participating in a given program.
     *
     * @param Family $family
     * @param int $programId
     * @return boolean whether this Family still has any members in the program
     */
    protected static function hasClientsInProgram(Family $family, int $programId): bool
    {
        foreach ($family->clients as $client) {
            if (ProgramClient::findOne(['program_id' => $programId,
                    'client_id' => $client->id]) !== null) {
                // There is an entry for this family and program, don't delete the entry
                return true;
            }
        }
        return false;
    }

    /**
     * Return an ActiveQuery for ProgramFamily models where there are not related ProgramClient models.
     * @return ActiveQuery
     */
    public static function getOrphanedProgramFamilies(): ActiveQuery
    {
        $sql = 'select * from program_family as pf ' .
            'where not exists(select 1 from program_client as pc, client as c ' .
            'where pc.client_id=c.id and pc.program_id = pf.program_id and c.family_id=pf.family_id )';
        return ProgramFamily::findBySql($sql);
    }

    /**
     * @throws ServerErrorHttpException
     */
    public static function fixOrphanedProgramFamilies(): void
    {
        $count = 0;
        /** @var ProgramFamily $pf */
        foreach (self::getOrphanedProgramFamilies()->each() as $pf) {
            try {
                $pf->delete();
                $count++;
            } catch (Throwable $e) {
                $msg = "Error deleting program $pf->program_id family $pf->family_id";
                Yii::error($msg, __METHOD__);
                throw new ServerErrorHttpException($msg);
            }
        }
        Yii::debug(
            "Deleted $count orphaned ProgramFamily records",
            __METHOD__);
    }

    /**
     * Migrate a ProgramFamily record from their current family to a new one. It will try to preserve
     * as much of the information as possible.
     * @param ProgramFamily $programFamily
     * @param Family $family
     * @return bool whether migration was successful
     * @throws StaleObjectException
     * @throws Throwable
     */
    public static function migrateProgramFamilyToNewFamily(
        ProgramFamily $programFamily, Family $family): bool
    {
        if (($originalProgramFamily = ProgramFamily::findOne([
                'program_id' => $programFamily->program_id,
                'family_id' => $family->id])) !== null) {
            return self::mergeProgramFamilyRecords($originalProgramFamily, $programFamily);
        }
        // The original family did not have a link with this program
        $programFamily->family_id = $family->id;
        if (!$programFamily->save()) {
            Yii::error("Error saving ProgramFamily " .
                "p $programFamily->program_id f $programFamily->family_id",
                __METHOD__);
            Yii::error($programFamily->errors, __METHOD__);
            return false;
        }
        return true;
    }

    /**
     * @param ProgramFamily $original
     * @param ProgramFamily $duplicate
     * @return bool whether merge was successful
     * @throws StaleObjectException
     * @throws Throwable
     */
    public static function mergeProgramFamilyRecords(
        ProgramFamily $original, ProgramFamily $duplicate): bool
    {
        // todo complete implementation
        if (!$duplicate->delete()) {
            return false;
        }
        return true;
    }
}
