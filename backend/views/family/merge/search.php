<?php

use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model common\models\Family */
/* @var $searchDataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Search duplicates for {family}', ['family' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Families'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Search duplicates');

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;use yii\widgets\ListView;
use yii\widgets\Pjax;
?>

<div class="family-merge-search">

    <div class="families-container row">

        <div class="original-family col-lg-6">

            <?= $this->render('_details', ['model' => $model]) ?>

        </div>

        <div class="search-family col-lg-6">

            <?php $form = ActiveForm::begin([
                'action' => ['merge-search', 'id' => $model->id],
                'method' => 'get'
            ]);
            ?>

            <div class="search-box input-group">

                <input type="search" class="form-control" name="q"
                       placeholder="<?= Yii::t('app', 'Search') ?>"
                       value="<?= $query ?? '' ?>">

                <span class="input-group-btn">
                    <?= Html::submitButton(
                        Html::icon('search'),
                        ['class' => 'btn btn-primary']) ?>
                </span>

            </div>

            <?php ActiveForm::end(); ?>

            <div class="search-results">

                <?php Pjax::begin(); ?>

                <?= ListView::widget([
                        'dataProvider' => $searchDataProvider,
                        'itemView' => '_item',
                        'itemOptions' => ['class' => 'list-item'],
                ]) ?>

                <?php Pjax::end(); ?>

            </div>

        </div>

    </div>

</div>
