<?php

use yii\db\Migration;

/**
 * Class m210427_170356_add_trade_columns_to_wx_unified_payment_order
 */
class m210427_170356_add_trade_columns_to_wx_unified_payment_order extends Migration
{
    private $tableName = 'wx_unified_payment_order';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'trade_state',
            $this->string(32)->null()->defaultValue(null)->after('status')
                ->comment('WeChat Pay order actual status'));
        $this->addColumn($this->tableName, 'trade_state_desc',
            $this->string(256)->null()->defaultValue(null)->after('trade_state')
                ->comment('Description of the current query order status and guidance for the next step'));
        $this->addColumn($this->tableName, 'cash_fee',
            $this->integer()->null()->defaultValue(null)->after('total_fee')
                ->comment('Cash payment amount The cash payment amount of the order, refer to the payment amount for details'));
        $this->addColumn($this->tableName, 'cash_fee_type',
            $this->string(16)->null()->defaultValue(null)->after('cash_fee')
                ->comment('Currency type, a three-letter code conforming to the ISO 4217 standard, default RMB: CNY'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'cash_fee_type');
        $this->dropColumn($this->tableName, 'cash_fee');
        $this->dropColumn($this->tableName, 'trade_state_desc');
        $this->dropColumn($this->tableName, 'trade_state');
    }
}
