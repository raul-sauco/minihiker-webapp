<?php

use common\models\Program;
use common\models\User;
use yii\bootstrap\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Program */

// Display conditionally, right now only admins see the info
if ($model->created_by !== null && Yii::$app->user->can('admin')) {
    $output = '';
    $created_by = Html::a(User::findOne($model->created_by)->username, 
        ['/user/view', 'id' => $model->created_by]);
    $created_at = $model->created_at === null ? Yii::t('app', 'N/A') :
        Yii::$app->formatter->asDatetime($model->created_at);
    $output .= Yii::t('app', 'Created by {created_by} {created_at}. ', [
        'created_by' => $created_by,
        'created_at' => $created_at,
    ]);
    if (!empty($model->updated_at) && ((int)$model->updated_at !== (int)$model->created_at)) {
        $updated_at = Yii::$app->formatter->asDatetime($model->updated_at);
        if (!empty($model->updated_by) && ((int)$model->updated_by !== (int)$model->created_by)) {
            $updated_by = Html::a(User::findOne($model->updated_by)->username, [
                '/user/view', 'id' => $model->updated_by
            ]);
            $output .= Yii::t('app', 'Updated by {updated_by} {updated_at}.', [
                'updated_by' => $updated_by,
                'updated_at' => $updated_at,
            ]);
        } else {
            // Update user is the same as creator, don't display
            $output .= Yii::t('app', 'Updated {updated_at}', [
                'updated_at' => $updated_at,
            ]);
        }
    }
    echo Html::tag('p', $output, ['class' => 'create-info']);
}
