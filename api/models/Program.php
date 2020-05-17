<?php

namespace api\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * Class Program
 * @package api\models
 *
 * @property null|ActiveQuery $participants
 */
class Program extends \common\models\Program
{
    /**
     * @return ActiveQuery
     */
    public function getProgramGroup(): ActiveQuery
    {
        return $this->hasOne(ProgramGroup::class, ['id' => 'program_group_id']);
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
            $fields['updated_by']
        );
        return $fields;
    }

    /**
     * @return array|string[]
     */
    public function extraFields(): array
    {
        return [
            'programGroup'
        ];
    }
}
