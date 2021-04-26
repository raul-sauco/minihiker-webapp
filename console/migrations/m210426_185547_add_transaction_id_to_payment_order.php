<?php

use yii\db\Migration;

/**
 * Class m210426_185547_add_transaction_id_to_payment_order
 */
class m210426_185547_add_transaction_id_to_payment_order extends Migration
{
    private $tableName = 'wx_unified_payment_order';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'transaction_id',
            $this->string(32)->null()->defaultValue(null)->after('attach')
                ->comment('WeChat Pay order number'));
        $this->addColumn($this->tableName, 'time_end',
            $this->string(14)->null()->defaultValue(null)->after('transaction_id')
                ->comment('Payment completion time, the format is yyyyMMddHHmmss'));
        $this->addColumn($this->tableName, 'bank_type',
            $this->string(32)->null()->defaultValue(null)->after('time_end')
                ->comment('Bank type, use string type bank identification, see bank list for bank type'));
        $this->addColumn($this->tableName, 'is_subscribe',
            $this->string(1)->null()->defaultValue(null)->after('bank_type')
                ->comment('Does the user follow the public account, Y-follow, N-not follow'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'is_subscribe');
        $this->dropColumn($this->tableName, 'bank_type');
        $this->dropColumn($this->tableName, 'transaction_id');
        $this->dropColumn($this->tableName, 'time_end');
    }
}
