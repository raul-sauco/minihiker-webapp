<?php

namespace api\controllers;

use api\models\Client;
use common\controllers\ActiveBaseController;

/**
 * Class ClientController
 * @package api\controllers
 */
class ClientController extends ActiveBaseController
{
    public $modelClass = Client::class;
}
