<?php

namespace common\fixtures;

use common\models\ProgramPeriod;
use yii\base\InvalidConfigException;
use yii\test\ActiveFixture;

/**
 * Class ProgramPeriodFixture
 * @package common\fixtures
 */
class ProgramPeriodFixture extends ActiveFixture
{
    public $modelClass = ProgramPeriod::class;

    public $depends = [
        UserFixture::class,
    ];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->dataFile = codecept_data_dir('program_period.php');
    }
}
