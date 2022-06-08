<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Class XlsxAsset
 * @package common\assets
 * @author Raul Sauco <sauco.raul@gmail.com>
 */
class XlsxAsset extends AssetBundle
{
    public $sourcePath = '@npm/xlsx';
    public $js = [YII_DEBUG ? 'xlsx.js' : 'dist/xlsx.full.min.js'];
}
