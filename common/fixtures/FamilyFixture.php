<?php

namespace common\fixtures;

use common\models\Family;
use yii\base\InvalidConfigException;
use yii\test\ActiveFixture;

/**
 * Class FamilyFixture
 * @package common\fixtures
 */
class FamilyFixture extends ActiveFixture
{
    public $modelClass = Family::class;

    public $depends = [
        UserFixture::class,
    ];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->dataFile = codecept_data_dir('family.php');
    }
}
