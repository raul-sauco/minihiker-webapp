<?php

namespace apivp1\models;

use yii\db\ActiveQuery;

/**
 * Class ProgramGroupView
 * @package apivp1\models
 */
class ProgramGroupView extends \common\models\ProgramGroupView
{
    /**
     * @return ActiveQuery
     */
    public function getProgramGroup(): ActiveQuery
    {
        return $this->hasOne(ProgramGroup::class, ['id' => 'program_group_id']);
    }
}
