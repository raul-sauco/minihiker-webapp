<?php

namespace api\controllers;

use api\models\Family;
use common\controllers\ActiveBaseController;

/**
 * Class FamilyController
 * @package api\controllers
 */
class FamilyController extends ActiveBaseController
{
    public $modelClass = Family::class;
}
