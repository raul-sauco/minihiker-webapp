<?php

namespace apivp1\models;

use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * Class Family
 * @package apivp1\models
 */
class Family extends \common\models\Family
{
    public const SCENARIO_UPDATE_SELF_ACCOUNT = 'FAMILY_UPDATE_SELF_ACCOUNT';

    /**
     * Allow account details updates from Weapp.
     *
     * @return array
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE_SELF_ACCOUNT] = [
            'name', 'phone', 'wechat'
        ];
        return $scenarios;
    }

    /**
     * @return ActiveQuery
     */
    public function getClients(): ActiveQuery
    {
        return $this->hasMany(Client::class, ['family_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPayments(): ActiveQuery
    {
        return $this->hasMany(Payment::class, ['family_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getPrograms(): ActiveQuery
    {
        return $this->hasMany(Program::class, ['id' => 'program_id'])
            ->viaTable('program_family', ['family_id' => 'id']);
    }

    /**
     * Remove fields that are not relevant to API consumers.
     *
     * @return array
     */
    public function fields()
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
     * Add some extra fields, provided on expand.
     *
     * @return array|false
     */
    public function extraFields()
    {
        return [
            'clients'
        ];
    }
}
