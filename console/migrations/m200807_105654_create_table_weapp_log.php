<?php

use yii\db\Migration;

/**
 * Class m200807_105654_create_table_weapp_log
 */
class m200807_105654_create_table_weapp_log extends Migration
{
    private $tableName = '{{%weapp_log}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'message' => $this->string(),
            'res' => $this->text()->defaultValue(null),
            'extra' => $this->text()->defaultValue(null),
            'level' => $this->tinyInteger()->unsigned(),
            'page' => $this->string(),
            'method' => $this->string(),
            'line' => $this->string(20),
            'timestamp' => $this->string(64),
            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'created_by' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned()
        ], $tableOptions);

        $this->addForeignKey(
            'fk_weapp_log_created_by',
            $this->tableName,
            'created_by',
            'user',
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_weapp_log_updated_by',
            $this->tableName,
            'updated_by',
            'user',
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->createIndex(
            'idx_weapp_log_created_at',
            $this->tableName,
            'created_at'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_weapp_log_created_at', $this->tableName);
        $this->dropForeignKey('fk_weapp_log_updated_by', $this->tableName);
        $this->dropForeignKey('fk_weapp_log_created_by', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
