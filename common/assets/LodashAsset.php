<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Class LodashAsset
 * @package common\assets
 * @author Raul Sauco <sauco.raul@gmail.com>
 */
class LodashAsset extends AssetBundle
{
    public $sourcePath = '@npm/lodash';
    public $js = ['lodash.' . (YII_DEBUG ? 'js' : 'min.js')];
}
