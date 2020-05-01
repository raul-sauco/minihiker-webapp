<?php

namespace common\assets;

use yii\web\AssetBundle;

class RequireJsAsset extends AssetBundle
{
    public $sourcePath = '@npm';
    public $baseUrl = '@webroot';

    public $js = [
        'requirejs/require.js'
    ];
}
