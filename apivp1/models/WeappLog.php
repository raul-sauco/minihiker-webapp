<?php

namespace apivp1\models;

/**
 * Class WeappLog
 * @package apivp1\models
 */
class WeappLog extends \common\models\WeappLog
{
    /**
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();
        unset(
            $fields['updated_at'],
            $fields['updated_by']
        );
        return $fields;
    }
}
