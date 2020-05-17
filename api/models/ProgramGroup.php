<?php

namespace api\models;

use yii\db\ActiveQuery;

/**
 * Class ProgramGroup
 * @package api\models
 *
 * @property ActiveQuery $location
 * @property ActiveQuery $programs
 */
class ProgramGroup extends \common\models\ProgramGroup
{
    /**
     * @return ActiveQuery
     */
    public function getLocation(): ActiveQuery
    {
        return $this->hasOne(Location::class, ['name_zh' => 'location_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPrograms(): ActiveQuery
    {
        return $this->hasMany(Program::class, ['program_group_id' => 'id']);
    }

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
            $fields['updated_by'],

            // Unset this three fields to send less data to the consumer
            $fields['weapp_description'],
            $fields['price_description'],
            $fields['refund_description']
        );
        return $fields;
    }

    /**
     * @return array|string[]
     */
    public function extraFields(): array
    {
        return [
            'location',
            'programs'
        ];
    }
}
