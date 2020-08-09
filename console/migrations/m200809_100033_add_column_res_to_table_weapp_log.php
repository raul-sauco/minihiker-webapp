<?php

use yii\db\Migration;

/**
 * Class m200809_100033_add_column_res_to_table_weapp_log
 */
class m200809_100033_add_column_res_to_table_weapp_log extends Migration
{
    private $tableName = '{{%weapp_log}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            $this->tableName,
            'req',
            $this->text()->defaultValue(null)->after('res')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'req');
    }
}
