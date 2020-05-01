<?php

namespace common\assets;

use yii\web\AssetBundle;

class AxiosAsset extends AssetBundle
{
    public $js = [
        'https://cdn.jsdelivr.net/npm/axios/dist/axios' . (YII_DEBUG ? '' : '.min') . '.js'
    ];
}
