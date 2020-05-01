<?php
namespace common\assets;

use yii\web\AssetBundle;

class PerfectMarkDownAsset extends AssetBundle
{
    public $css = [
        'https://unpkg.com/perfect-markdown@1.0.8/lib/pmd.css'
    ];

    public $js = [
        'https://unpkg.com/perfect-markdown@1.0.8/lib/pmd.umd.min.js'
    ];

    public $depends = [
        VueAsset::class,
        VuexAsset::class
    ];
}
