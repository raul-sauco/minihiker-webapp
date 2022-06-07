<?php

namespace api\controllers;

use common\models\Log;

/**
 * Class LogController
 * @package common\controllers
 * @author Raul Sauco <sauco.raul@gmail.com>
 */
class LogController extends ActiveBaseController
{
    public $modelClass = Log::class;
}
