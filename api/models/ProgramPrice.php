<?php

namespace api\models;

/**
 * Class ProgramPrice
 * @package api\models
 */
class ProgramPrice extends \common\models\ProgramPrice
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
