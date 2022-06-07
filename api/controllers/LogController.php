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

    // It is possible that in the future we want to allow non-logged in users to submit logs
//    public function behaviors(): array
//    {
//        $behaviors = parent::behaviors();
//        $behaviors['authenticator']['optional'] = ['create'];
//        return $behaviors;
//    }
}
