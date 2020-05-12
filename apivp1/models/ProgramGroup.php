<?php

namespace apivp1\models;

use apivp1\helpers\DomParserHelper;
use apivp1\helpers\ProgramGroupHelper;
use yii\db\ActiveQuery;

/**
 * Class ProgramGroup
 * @package apivp1\models
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
    public function getProgramGroupViews(): ActiveQuery
    {
        return $this->hasMany(ProgramGroupView::class, ['program_group_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPrograms(): ActiveQuery
    {
        return $this->hasMany(Program::class, ['program_group_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getQas(): ActiveQuery
    {
        return $this->hasMany(Qa::class, ['program_group_id' => 'id']);
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
            $fields['updated_by'],

            // Unset this three fields to send less data to the Mini program
            $fields['weapp_description'],
            $fields['price_description'],
            $fields['refund_description']
        );
        return $fields;
    }

    /**
     * Add some extra fields, provided on expand
     *
     * @return array
     */
    public function extraFields(): array
    {
        return [
            'location',
            'programs',
            'type',
            'arraywad' => function () {
                return DomParserHelper::parseIntoArray($this->weapp_description);
            },
            'arraywap' => function () {
                return DomParserHelper::parseIntoArray($this->price_description);
            },
            'arraywar' => function () {
                return DomParserHelper::parseIntoArray($this->refund_description);
            },
            'registration_open' => function () {
                return ProgramGroupHelper::isRegistrationOpen($this);
            }
        ];
    }
}
