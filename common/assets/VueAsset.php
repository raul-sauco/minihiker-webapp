<?php
namespace common\assets;

use backend\assets\BackendAsset;
use yii\web\AssetBundle;

class VueAsset extends AssetBundle
{
    public $js = [
        'https://cdn.jsdelivr.net/npm/vue' . (YII_DEBUG ? '/dist/vue.js' : '')
    ];

    public $depends = [
        BackendAsset::class,
        AxiosAsset::class,
        LodashAsset::class,
        // VuexAsset::class
    ];
}
