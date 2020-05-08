<?php

namespace apivp1\models;

/**
 * Class ProgramPeriod
 * @package apivp1\models
 */
class ProgramPeriod extends \common\models\ProgramPeriod
{
    /**
     * Remove fields that are not relevant to API consumers.
     *
     * @return array
     */
    public function fields()
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
