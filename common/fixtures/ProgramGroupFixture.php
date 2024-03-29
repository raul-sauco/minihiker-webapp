<?php

namespace common\fixtures;

use common\models\ProgramGroup;
use yii\base\InvalidConfigException;
use yii\test\ActiveFixture;

/**
 * Class ProgramGroupFixture
 * @package common\fixtures
 */
class ProgramGroupFixture extends ActiveFixture
{
    public $modelClass = ProgramGroup::class;

    public $depends = [
        LocationFixture::class,
        ProgramTypeFixture::class,
        UserFixture::class,
    ];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->dataFile = codecept_data_dir('program_group.php');
    }
}
