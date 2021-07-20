<?php

namespace common\fixtures;

use common\models\Location;
use yii\base\InvalidConfigException;
use yii\test\ActiveFixture;

/**
 * Class LocationFixture
 * @package common\fixtures
 */
class LocationFixture extends ActiveFixture
{
    public $modelClass = Location::class;

    public $depends = [
        UserFixture::class,
    ];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->dataFile = codecept_data_dir('location.php');
    }
}
