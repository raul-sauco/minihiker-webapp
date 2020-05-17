<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Class XlsxAsset
 * @package common\assets
 */
class XlsxAsset extends AssetBundle
{
    public $js = [
        'https://unpkg.com/xlsx@0.16.0/dist/xlsx.full.min.js'
    ];
}
