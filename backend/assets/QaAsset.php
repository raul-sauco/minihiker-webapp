<?php

namespace backend\assets;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * Class QaAsset
 * @package backend\assets
 */
class QaAsset extends AssetBundle
{
    public $basePath = '@staticPath';
    public $baseUrl = '@staticUrl';
    public $js = [
        YII_DEBUG ? 'js/qa.js' : 'js/qa.min.js'
    ];
    public $depends = [
        BackendAsset::class,
        YiiAsset::class,
        BootstrapAsset::class,
    ];
}
