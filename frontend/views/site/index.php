<?php

/* @var $this yii\web\View */

use common\assets\VueAsset;
use frontend\assets\AppAsset;
use frontend\assets\YhtAsset;

$this->title = 'Frontend test page';
$this->registerJsFile('@web/js/index.js', [
    'depends' => [
        YhtAsset::class,
        AppAsset::class,
        VueAsset::class // Includes AxiosAsset,
    ]]);
?>
<div id="site-index">
    <button id="post-auth" v-on:click="login">Post Auth</button>
</div>
