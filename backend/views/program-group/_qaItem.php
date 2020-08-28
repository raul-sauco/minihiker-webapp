<?php

/* @var $this yii\web\View */
/* @var $model common\models\Qa */
/* @var $dataProvider yii\data\ActiveDataProvider */

use common\helpers\QaHelper;
use yii\bootstrap\Html;
?>

<div class="program-group-qa-container <?= empty($model->answer) ? 'qa-unanswered' : '' ?>"
    id="program-group-qa-container-<?= $model->id ?>">

    <div class="qa-avatar-container">
        <?= Html::img('@imgUrl/f/' . QaHelper::getWxAccountAvatar($model) , [
                'alt' => QaHelper::getWxAccountNickname($model) . '\'s avatar'
        ]) ?>
    </div>

    <div class="qa-text-container">

        <div class="qa-main-header">
            <div class="qa-asked-by-info">
                <?= Yii::t(
                    'app',
                    '{username} {asked}', [
                    'username' => QaHelper::getWxAccountNickname($model),
                    'asked' => Yii::$app->formatter->asDatetime($model->created_at)
                ]) ?>
            </div>
            <div class="qa-action-buttons">
                <button class="btn btn-sm btn-success save-qa-answer-button"
                        id="save-qa-answer-button-<?= $model->id ?>"
                        data-text="<?= Yii::t('app', 'Save') ?>"
                        data-id="<?= $model->id ?>">
                    <?= Yii::t('app', 'Save') ?>
                </button>
                <button class="btn btn-sm btn-danger delete-qa-answer-button"
                        id="delete-qa-answer-button-<?= $model->id ?>"
                        data-text="<?= Yii::t('app', 'Delete') ?>"
                        data-id="<?= $model->id ?>">
                    <?= Html::icon('trash') ?>
                    <?= Yii::t('app', 'Delete') ?>
                </button>
            </div>
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
