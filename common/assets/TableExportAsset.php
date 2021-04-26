<?php

namespace common\assets;

use yii\web\AssetBundle;

class TableExportAsset extends AssetBundle
{
    public $sourcePath = '@npm';
    public $baseUrl = '@staticUrl';

    public $css = [
        'node_modules/datatables.net-dt/css/jquery.dataTables.' . (YII_DEBUG ? 'css' : 'min.css'),
        'node_modules/datatables.net-buttons-dt/css/buttons.dataTables.' . (YII_DEBUG ? 'css' : 'min.css')
    ];

    public $js = [
        'node_modules/jszip/dist/jszip.' . (YII_DEBUG ? 'js' : 'min.js'),
        'node_modules/datatables.net/js/jquery.dataTables.' . (YII_DEBUG ? 'js' : 'min.js'),
        'node_modules/datatables.net-dt/js/dataTables.dataTables.' . (YII_DEBUG ? 'js' : 'min.js'),
        'node_modules/datatables.net-buttons-dt/js/buttons.dataTables.' . (YII_DEBUG ? 'js' : 'min.js'),
        'node_modules/datatables.net-buttons/js/dataTables.buttons.' . (YII_DEBUG ? 'js' : 'min.js'),
        'node_modules/datatables.net-buttons/js/buttons.html5.' . (YII_DEBUG ? 'js' : 'min.js'),
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
