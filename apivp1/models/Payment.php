<?php

namespace apivp1\models;

use yii\db\ActiveQuery;

/**
 * Class Payment
 * @package apivp1\models
 */
class Payment extends \common\models\Payment
{
    /**
     * @return ActiveQuery
     */
    public function getProgram(): ActiveQuery
    {
        return $this->hasOne(Program::class, ['id' => 'program_id']);
    }

    /**
     * Remove fields that are not relevant to API consumers.
     *
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
