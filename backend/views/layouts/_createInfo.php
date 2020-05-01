<?php

use common\models\User;
use yii\bootstrap\Html;

/* @var $this \yii\web\View */
/* @var $model \yii\base\Model */

$output = '';

if ($model->created_by !== null) {
    
    $created_by = Html::a(User::findOne($model->created_by)->username, 
        ['/user/view', 'id' => $model->created_by]);
    
    $created_at = $model->created_at === null ? Yii::t('app', 'N/A') :
        Yii::$app->formatter->asDatetime($model->created_at);
    
    $output .= Yii::t('app', 'Created by {created_by} {created_at}. ', [
        'created_by' => $created_by,
        'created_at' => $created_at,
    ]);
    
    if (!empty($model->updated_at) && ($model->updated_at != $model->created_at)) {
        
        $updated_at = Yii::$app->formatter->asDatetime($model->updated_at);
        
        if (!empty($model->updated_by) && ($model->updated_by) != $model->created_by) {
            
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
    
    // Display conditionally, right now only three users see the info
    if (in_array(Yii::$app->user->id, [1,3,4])) {
        
        echo Html::tag('p', $output, ['class' => 'create-info']);
        
    }
    
} else {
    
    // Don't display any information if missing $model->created_at timestamp
    
}

?>