<?php

namespace common\helpers;

use common\models\ProgramGroup;
use common\models\ProgramGroupView;
use common\models\Qa;
use Yii;
use yii\db\StaleObjectException;
use yii\helpers\FileHelper;

/**
 * Class ProgramGroupHelper
 * @package common\helpers
 */
class ProgramGroupHelper
{
    /**
     * Delete all related records that could make program group deletion fail.
     * For each related record, recursively delete related records that
     * reference them.
     *
     * https://stackoverflow.com/a/1133461/2557030
     *
     * @param ProgramGroup $programGroup
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public static function deleteAllRelatedRecords (ProgramGroup $programGroup): void
    {
        foreach ($programGroup->programs as $program) {
            ProgramHelper::deleteAllRelatedRecords($program);
            $program->delete();
        }
        foreach ($programGroup->programGroupImages as $programGroupImage) {
            $programGroupImage->delete();
        }
        FileHelper::removeDirectory(Yii::getAlias('@imgPath/pg/' . $programGroup->id));
        ProgramGroupView::deleteAll(['program_group_id' => $programGroup->id]);
        Qa::deleteAll(['program_group_id' => $programGroup->id]);
    }
}
