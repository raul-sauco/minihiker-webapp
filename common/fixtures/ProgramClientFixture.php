<?php

namespace common\fixtures;

use common\models\ProgramClient;
use yii\base\InvalidConfigException;
use yii\test\ActiveFixture;

/**
 * Class ProgramClientFixture
 * @package common\fixtures
 */
class ProgramClientFixture extends ActiveFixture
{
    public $modelClass = ProgramClient::class;

    public $depends = [
        ClientFixture::class,
        ProgramFixture::class,
        UserFixture::class,
    ];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->dataFile = codecept_data_dir('program_client.php');
    }
}
