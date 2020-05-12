<?php

namespace apivp1\models;

/**
 * Class Qa
 * @package apivp1\models
 */
class Qa extends \common\models\Qa
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
            $fields['user_ip'],
            // $fields['created_at'],
            // $fields['updated_at'],
            $fields['created_by'],
            $fields['updated_by']
        );
        return $fields;
    }
}
