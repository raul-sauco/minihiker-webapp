<?php

namespace apivp1\models;

use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * Class User
 * @package apivp1\models
 */
class User extends \common\models\User
{
    /**
     * @return ActiveQuery
     */
    public function getProgramGroupViews(): ActiveQuery
    {
        return $this->hasMany(ProgramGroupView::class, ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getProgramGroupsViewed(): ActiveQuery
    {
        return $this->hasMany(ProgramGroup::class, ['id' => 'program_group_id'])
            ->viaTable('program_group_view', ['user_id' => 'id']);
    }
}
