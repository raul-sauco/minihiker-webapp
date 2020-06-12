<?php

use yii\db\Migration;

/**
 * Class m200612_065933_add_weapp_deposit_info_columns
 */
class m200612_065933_add_weapp_deposit_info_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('program', 'deposit', $this->integer()->after('price'));
        $this->addColumn('program', 'deposit_message', $this->text()->after('deposit'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('program', 'deposit_message');
        $this->dropColumn('program', 'deposit');
    }
}
