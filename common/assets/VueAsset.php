<?php

namespace common\assets;

use backend\assets\BackendAsset;
use yii\web\AssetBundle;

/**
 * Class VueAsset
 * @package common\assets
 * @author Raul Sauco <sauco.raul@gmail.com>
 */
class VueAsset extends AssetBundle
{
    public $sourcePath = '@npm/vue/dist';
    public $js = ['vue.' . (YII_DEBUG ? 'js' : 'min.js')];
    public $depends = [
        BackendAsset::class,
        AxiosAsset::class,
        LodashAsset::class,
    ];
}
