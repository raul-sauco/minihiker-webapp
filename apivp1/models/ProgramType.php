<?php

namespace apivp1\models;


class ProgramType extends \common\models\ProgramType
{
    /**
     * Remove fields that are not relevant to public API consumers.
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();
        unset(
            $fields['created_at'],
            $fields['created_by'],
            $fields['updated_at'],
            $fields['updated_by']);
        return $fields;
    }
}
