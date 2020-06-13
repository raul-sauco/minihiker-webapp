<?php

use yii\db\Migration;

/**
 * Class m200613_070319_add_wx_order_hidden_column
 */
class m200613_070319_add_wx_order_hidden_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'wx_unified_payment_order',
            'hidden',
            $this->boolean()->after('status')->defaultValue(false)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(
            'wx_unified_payment_order',
            'hidden'
        );
    }
}
