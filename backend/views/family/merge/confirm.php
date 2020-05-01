<?php

/* @var $this \yii\web\View */
/* @var $original \common\models\Family */
/* @var $duplicate \common\models\Family */

$this->title = Yii::t('app',
    'Confirm merge of {original} and {duplicate}',
    [
        'original' => $original->name,
        'duplicate' => $duplicate->name
    ]);
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Families'),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = [
    'label' => $original->name,
    'url' => ['view', 'id' => $original->id]
];
$this->params['breadcrumbs'][] = $this->title;

use yii\bootstrap\Html; ?>

<div class="family-merge-confirm">

    <div class="family-merge-container row">

        <div class="orginal-family-container col-lg-6">

            <h2><?= Yii::t('app', 'Original Record') ?></h2>

            <?= $this->render('_details', ['model' => $original]) ?>

        </div>

        <div class="duplicate-family-container col-lg-6">

            <h2><?= Yii::t('app', 'Duplicate Record') ?></h2>

            <?= $this->render('_details', ['model' => $duplicate]) ?>

        </div>

    </div>

    <div class="row">

        <div class="col-lg-12">

            <?= Html::a(
                    Yii::t('app', 'Merge'),
                    ['merge-confirm', 'id' => $original->id, 'dup' => $duplicate->id], [
                        'class' => 'btn btn-success',
                        'id' => 'family-confirm-merge-button',
                        'data' => [
                            'confirm' => $this->title,
                            'method' => 'post',
                        ],
                    ]
            ) ?>

        </div>

    </div>

</div>
