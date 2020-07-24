<?php

namespace api\controllers;

use api\models\Qa;
use common\controllers\ActiveBaseController;

/**
 * Class QaController
 * @package api\controllers
 */
class QaController extends ActiveBaseController
{
    public $modelClass = Qa::class;
}