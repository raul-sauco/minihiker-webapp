<?php

namespace apivp1\models;

use yii\db\ActiveQuery;

/**
 * Class WxUnifiedPaymentOrder
 * @package apivp1\models
 */
class WxUnifiedPaymentOrder extends \common\models\WxUnifiedPaymentOrder
{
    /**
     * @return ActiveQuery
     */
    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFamily(): ActiveQuery
    {
        return $this->hasOne(Family::class, ['id' => 'family_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPrice(): ActiveQuery
    {
        return $this->hasOne(ProgramPrice::class, ['id' => 'price_id']);
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
            $fields['appid'],
            $fields['mch_id'],
            $fields['device_info'],
            $fields['nonce_str'],
            $fields['sign'],
            $fields['sign_type'],
            $fields['out_trade_no'],
            $fields['fee_type'],
            $fields['spbill_create_ip'],
            $fields['notify_url'],
            $fields['openid'],
            $fields['prepay_id'],
            $fields['prepay_sign'],
            $fields['prepay_timestamp'],
            $fields['notify_xml'],
            $fields['notify_result_code'],
            $fields['notify_return_code'],
            $fields['notify_err_code'],
            $fields['notify_err_code_des'],
            // $fields['created_at'],
            $fields['created_by'],
            $fields['updated_at'],
            $fields['updated_by']
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
            'client',
            'family',
            'price'
        ];
    }
}
