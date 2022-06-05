<?php
/**
 * This file is a copy of vendor/yii2/log/migrations/m141106_185632_log_init.php
 * which can be used calling `./yii migrate --migrationPath=@yii/log/migrations/`
 * I decided to copy the code to simplify keeping the database and code in sync
 * committing this migration to the repository, and to also simplify getting new
 * systems up and running, avoiding having to remember having to add one extra
 * migration in each script that initializes the database or the test database.
 * Raul Sauco <sauco.raul@gmail.com>
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\log\DbTarget;

/**
 * Initializes log table.
 *
 * The indexes declared are not required. They are mainly used to improve the performance
 * of some queries about message levels and categories. Depending on your actual needs, you may
 * want to create additional indexes (e.g. index on `log_time`).
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @since 2.0.1
 */
class m220605_204139_create_table_log extends Migration
{
    /**
     * @var DbTarget[] Targets to create log table for
     */
    private array $dbTargets = [];

    /**
     * @throws InvalidConfigException
     */
    public function up()
    {
        foreach ($this->getDbTargets() as $target) {
            $this->db = $target->db;

            $tableOptions = null;
            if ($this->db->driverName === 'mysql') {
                // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
                $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
            }

            $this->createTable($target->logTable, [
                'id' => $this->bigPrimaryKey(),
                'level' => $this->integer(),
                'category' => $this->string(),
                'log_time' => $this->double(),
                'prefix' => $this->text(),
                'message' => $this->text(),
                'created_at' => $this->integer()->unsigned(),
                'updated_at' => $this->integer()->unsigned(),
                'created_by' => $this->integer()->unsigned(),
                'updated_by' => $this->integer()->unsigned(),
            ], $tableOptions);

            $this->addForeignKey(
                'fk_log_created_by',
                $target->logTable,
                'created_by',
                'user',
                'id',
                'SET NULL',
                'CASCADE',
            );
            $this->addForeignKey(
                'fk_log_updated_by',
                $target->logTable,
                'updated_by',
                'user',
                'id',
                'SET NULL',
                'CASCADE',
            );

            $this->createIndex('idx_log_level', $target->logTable, 'level');
            $this->createIndex('idx_log_category', $target->logTable, 'category');
            $this->createIndex('idx_log_created_at', $target->logTable, 'created_at');
            $this->createIndex('idx_log_created_by', $target->logTable, 'created_by');
        }
    }

    /**
     * @return DbTarget[]
     * @throws InvalidConfigException
     */
    protected function getDbTargets()
    {
        if ($this->dbTargets === []) {
            $log = Yii::$app->getLog();

            $usedTargets = [];
            foreach ($log->targets as $target) {
                if ($target instanceof DbTarget) {
                    $currentTarget = [
                        $target->db,
                        $target->logTable,
                    ];
                    if (!in_array($currentTarget, $usedTargets, true)) {
                        // do not create same table twice
                        $usedTargets[] = $currentTarget;
                        $this->dbTargets[] = $target;
                    }
                }
            }

            if ($this->dbTargets === []) {
                throw new InvalidConfigException('You should configure "log" component to use one or more database targets before executing this migration.');
            }
        }

        return $this->dbTargets;
    }

    public function down()
    {
        foreach ($this->getDbTargets() as $target) {
            $this->db = $target->db;

            $this->dropTable($target->logTable);
        }
    }
}
