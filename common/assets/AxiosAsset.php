<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Class AxiosAsset
 * @package common\assets
 * @author Raul Sauco <sauco.raul@gmail.com>
 */
class AxiosAsset extends AssetBundle
{
    public $sourcePath = '@npm/axios/dist';
    public $js = ['axios.' . (YII_DEBUG ? 'js' : 'min.js')];
}
