<?php

namespace apivp1\models;

use yii\db\ActiveQuery;

/**
 * Class ProgramPrice
 * @package apivp1\models
 */
class ProgramPrice extends \common\models\ProgramPrice
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram(): ActiveQuery
    {
        return $this->hasOne(Program::class, ['id' => 'program_id']);
    }

    /**
     * Remove fields that are not relevant to API consumers.
     *
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
        $fields['name'] = static function ($model) {
            return $model->getNamei18n();
        };
        $fields['program_weapp_name'] = static function ($model) {
            return $model->program->programGroup->weapp_display_name . ' ' .
                $model->program->programPeriod->name;
        };

        return $fields;
    }

    /**
     * Add some extra fields, provided on expand
     *
     * @return array
     */
    public function extraFields(): array
    {
        return [
            'program'
        ];
    }
}
