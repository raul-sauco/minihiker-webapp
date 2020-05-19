<?php

namespace api\models;

/**
 * Class ProgramClient
 * @package api\models
 */
class ProgramClient extends \common\models\ProgramClient
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
