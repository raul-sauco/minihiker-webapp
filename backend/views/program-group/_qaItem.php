<?php

/* @var $this yii\web\View */
/* @var $model common\models\Qa */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\bootstrap\Html; ?>

<div class="program-group-qa-container <?= empty($model->answer) ? 'qa-unanswered' : '' ?>">

    <div class="qa-avatar-container">
        <?= Html::img($model->user_avatar_url, [
                'alt' => $model->user_nickname . '\'s avatar'
        ]) ?>
    </div>

    <div class="qa-text-container">

        <div class="qa-asked-by-info">
            <?= Yii::t(
            'app',
            '{username} {asked}', [
                    'username' => $model->user_nickname,
                    'asked' => Yii::$app->formatter->asDatetime($model->created_at)
            ]) ?>
        </div>

        <div class="qa-question-container">
            <?= Yii::t('app', 'Q: {question}', [
                    'question' => $model->question
            ]) ?>
        </div>

        <div class="qa-answer-container">
            <?= Html::textarea(
                    'qa-answer',
                    $model->answer, [
                        'id' => 'qa-answer-' . $model->id,
                        'class' => 'qa-answer-textarea',
                        'data-qa-id' => $model->id,
                        'placeholder' => Yii::t('app', 'Write Answer Here')
            ]) ?>
        </div>

    </div>

</div>
