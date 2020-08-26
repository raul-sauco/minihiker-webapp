<?php

namespace apivp1\models;

use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Class Client
 * @property Family $family
 * @package apivp1\models
 */
class Client extends \common\models\Client
{
    const SCENARIO_FAMILY_UPDATE_MEMBER = 'FAMILY_UPDATE_MEMBER';
    const SCENARIO_FAMILY_CREATE_MEMBER = 'FAMILY_CREATE_MEMBER';

    /**
     * Add custom scenarios.
     *
     * @return array
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_FAMILY_UPDATE_MEMBER] = [
            'name_zh', 'nickname', 'name_pinyin', 'name_en', 'birthdate', 'is_male', 'is_kid', 'family_role_id',
            'remarks', 'phone_number', 'phone_number_2', 'email', 'wechat_id', 'id_card_number', 'passport_number',
            'passport_issue_date', 'passport_expire_date', 'passport_place_of_issue', 'passport_issuing_authority',
            'place_of_birth', 'allergies', 'dietary_restrictions'
        ];
        $scenarios[self::SCENARIO_FAMILY_CREATE_MEMBER] =
            ArrayHelper::merge($scenarios[self::SCENARIO_FAMILY_UPDATE_MEMBER], ['family_id']);
        return $scenarios;
    }

    /**
     * @return ActiveQuery
     */
    public function getFamily(): ActiveQuery
    {
        return $this->hasOne(Family::class, ['id' => 'family_id']);
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
            $fields['openid'],
            $fields['wx_session_key'],
            $fields['wx_session_key_obtained_at'],
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
     * @return array
     */
    public function extraFields(): array
    {
        return [
            'hasInt'
        ];
    }
}
