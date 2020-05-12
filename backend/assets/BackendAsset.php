<?php

namespace backend\assets;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * Main backend application asset bundle.
 */
class BackendAsset extends AssetBundle
{
    public $basePath = '@staticPath';
    public $baseUrl = '@staticUrl';
    public $css = [
        YII_DEBUG ? 'css/backend.css' : 'css/backend.min.css',
    ];
    public $js = [
        YII_DEBUG ? 'js/app.js' : 'js/app.min.js',
    ];
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
    ];
}
