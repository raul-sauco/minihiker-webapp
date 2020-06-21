<?php

namespace backend\assets;

use common\assets\XlsxAsset;
use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * Class TableExportAsset
 * @package backend\assets
 */
class TableExportAsset extends AssetBundle
{
    public $baseUrl = '@staticUrl';

    public $css = [
        'https://unpkg.com/tableexport/dist/css/tableexport.min.css',
    ];

    public $js = [
        'https://unpkg.com/file-saver@2.0.2/dist/FileSaver.min.js',
        'https://unpkg.com/tableexport/dist/js/tableexport.min.js'
    ];

    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
        XlsxAsset::class
    ];
}
