<?php
namespace backend\assets;

use yii\web\AssetBundle;

class TableExportAsset extends AssetBundle
{
    public $sourcePath = '@npm';
    public $baseUrl = '@web';

    public $css = [
        'node_modules/tableexport/dist/css/tableexport.' . (YII_DEBUG ? 'css' : 'min.css'),
    ];

    public $js = [
        'node_modules/blobjs/Blob.' . (YII_DEBUG ? 'js' : 'min.js'),
        'node_modules/xlsx/dist/cpexcel.js',
        'node_modules/xlsx/dist/jszip.js',
        'node_modules/xlsx/dist/xlsx.' . (YII_DEBUG ? 'js' : 'min.js'),
        'node_modules/file-saverjs/FileSaver.' . (YII_DEBUG ? 'js' : 'min.js'),
        'node_modules/tableexport/dist/js/tableexport.' . (YII_DEBUG ? 'js' : 'min.js'),
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}