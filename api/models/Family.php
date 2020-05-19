<?php

namespace api\models;

/**
 * Class Family
 * @package api\models
 */
class Family extends \common\models\Family
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
