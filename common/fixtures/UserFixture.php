<?php

namespace common\fixtures;

use common\models\User;
use yii\base\InvalidConfigException;
use yii\test\ActiveFixture;

/**
 * Class UserFixture
 * @package common\fixtures
 */
class UserFixture extends ActiveFixture
{
    public $modelClass = User::class;

    /**
     * Some of these are circular dependencies, ask for them here and not on the auth tables.
     * @var string[]
     */
    public $depends = [
        AuthItemFixture::class,
        AuthItemChildFixture::class,
        AuthRuleFixture::class,
        AuthAssignmentFixture::class,
    ];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->dataFile = codecept_data_dir('user.php');
    }
}