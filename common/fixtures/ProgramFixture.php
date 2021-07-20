<?php

namespace common\fixtures;

use common\models\Program;
use yii\base\InvalidConfigException;
use yii\test\ActiveFixture;

/**
 * Class ProgramFixture
 * @package common\fixtures
 */
class ProgramFixture extends ActiveFixture
{
    public $modelClass = Program::class;

    public $depends = [
        ProgramGroupFixture::class,
        ProgramPeriodFixture::class,
        UserFixture::class,
    ];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->dataFile = codecept_data_dir('program.php');
    }
}
