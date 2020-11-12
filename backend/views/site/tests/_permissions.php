<?php

/* @var $this yii\web\View */

use backend\helpers\RbacPermissionCheckerHelper;
use common\models\User;
use yii\bootstrap\Html;
?>
<div class="site-permissions-tests">
    <h3>Permissions</h3>
    <?php
    foreach (User::find()->each() as $user) {
        $error = RbacPermissionCheckerHelper::checkUserPermissions($user);
        if ($error !== null) {
            echo Html::tag('div', $error);
        }
    }
    ?>
</div>
