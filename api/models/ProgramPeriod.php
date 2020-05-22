<?php

namespace api\models;

/**
 * Class ProgramPeriod
 * @package api\models
 */
class ProgramPeriod extends \common\models\ProgramPeriod
{
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
