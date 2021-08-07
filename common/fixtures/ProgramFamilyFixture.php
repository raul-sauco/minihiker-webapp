<?php

namespace common\fixtures;

use common\models\ProgramFamily;
use yii\base\InvalidConfigException;
use yii\test\ActiveFixture;

/**
 * Class ProgramFamilyFixture
 * @package common\fixtures
 */
class ProgramFamilyFixture extends ActiveFixture
{
    public $modelClass = ProgramFamily::class;

    public $depends = [
        FamilyFixture::class,
        ProgramFixture::class,
        UserFixture::class,
    ];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->dataFile = codecept_data_dir('program_family.php');
    }
}
