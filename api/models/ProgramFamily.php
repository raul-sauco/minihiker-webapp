<?php

namespace api\models;
use yii\db\ActiveQuery;

/**
 * @property Program $program
 * @property Family $family
 *
 * Class ProgramFamily
 * @package api\models
 */
class ProgramFamily extends \common\models\ProgramFamily
{
    /**
     * @return ActiveQuery
     */
    public function getProgram(): ActiveQuery
    {
        return $this->hasOne(Program::class, ['id' => 'program_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFamily(): ActiveQuery
    {
        return $this->hasOne(Family::class, ['id' => 'family_id']);
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();
        unset(
            $fields['created_at'],
            $fields['created_by'],
            $fields['updated_at'],
            $fields['updated_by']
        );
        return $fields;
    }
}
