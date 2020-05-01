<?php
namespace common\assets;

use yii\web\AssetBundle;

class VuexAsset extends AssetBundle
{
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/vuex/3.1.0/vuex.min.js'
    ];

    public $depends = [
    ];
}
