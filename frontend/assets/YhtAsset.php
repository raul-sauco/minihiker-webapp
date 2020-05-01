<?php

namespace frontend\assets;

use common\assets\VueAsset;
use yii\web\AssetBundle;

/**
 * Yunhetong object methods
 */
class YhtAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/yht.js'
    ];
    public $depends = [
        VueAsset::class
    ];
}
