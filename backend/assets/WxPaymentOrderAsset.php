<?php

namespace backend\assets;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * Class WxPaymentOrderAsset
 * @package backend\assets
 */
class WxPaymentOrderAsset extends AssetBundle
{
    public $basePath = '@staticPath';
    public $baseUrl = '@staticUrl';
    public $js = [
        'js/wx-payment-order.' . (YII_DEBUG ? 'js' : 'min.js')
    ];
    public $depends = [
        BackendAsset::class,
        YiiAsset::class,
        BootstrapAsset::class,
    ];
}
