<?php

namespace api\models;

/**
 * Class ProgramType
 * @package api\models
 */
class ProgramType extends \common\models\ProgramType
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
