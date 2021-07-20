<?php

namespace common\fixtures;

use common\models\ProgramType;
use yii\base\InvalidConfigException;
use yii\test\ActiveFixture;

/**
 * Class ProgramTypeFixture
 * @package common\fixtures
 */
class ProgramTypeFixture extends ActiveFixture
{
    public $modelClass = ProgramType::class;

    public $depends = [
    ];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->dataFile = codecept_data_dir('program_type.php');
    }
}
