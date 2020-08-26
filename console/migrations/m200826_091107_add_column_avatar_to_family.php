<?php

use yii\db\Migration;

/**
 * Class m200826_091107_add_column_avatar_to_family
 */
class m200826_091107_add_column_avatar_to_family extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'family',
            'avatar',
            $this->string(64)->after('name')
        );
        $this->update(
            'family',
            ['avatar' => 'user.jpeg'],
            ['is', 'avatar', null]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('family', 'avatar');
    }
}
