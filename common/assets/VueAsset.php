<?php
namespace common\assets;

use yii\web\AssetBundle;

class VueAsset extends AssetBundle
{
    public $js = [
        'https://cdn.jsdelivr.net/npm/vue' . (YII_DEBUG ? '/dist/vue.js' : '')
    ];

    public $depends = [
        AxiosAsset::class,
        LodashAsset::class,
        // VuexAsset::class
    ];
}
