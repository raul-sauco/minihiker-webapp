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
    <div id="login-container">
        You are <span v-html="loginStatus"></span>
        <button id="post-auth" v-on:click="login" v-if="!loggedIn">Login</button>
    </div>
    <div id="user-container">
        <span v-if="userName">User is <strong v-html="userName"></strong></span>
        <span v-else>No user <button v-on:click="createUser">Create User</button></span>
    </div>
</div>
