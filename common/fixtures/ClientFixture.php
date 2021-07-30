<?php

namespace common\fixtures;

use common\models\Client;
use yii\base\InvalidConfigException;
use yii\test\ActiveFixture;

/**
 * Class ClientFixture
 * @package common\fixtures
 */
class ClientFixture extends ActiveFixture
{
    public $modelClass = Client::class;

    public $depends = [
        FamilyFixture::class,
        UserFixture::class,
    ];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->dataFile = codecept_data_dir('client.php');
    }
}
