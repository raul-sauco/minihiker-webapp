<?php

namespace api\models;

/**
 * Class Qa
 * @package api\models
 */
class Qa extends \common\models\Qa
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
