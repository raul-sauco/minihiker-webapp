<?php
namespace common\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class MasonryAsset
 * @package common\assets
 */
class MasonryAsset extends AssetBundle
{
    public $js = [
        'https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.' .
        (YII_DEBUG ? 'js' : 'min.js'),
        'https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.' .
        (YII_DEBUG ? 'js' : 'min.js')
    ];

    public $depends = [
        JqueryAsset::class
    ];
}
