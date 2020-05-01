<?php

use yii\bootstrap\Html;
use common\models\Contact;

/* @var $this yii\web\View */
/* @var $model common\models\Supplier */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Suppliers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$contactDataProvider = new \yii\data\ActiveDataProvider([
        'query' => $model->getContacts(),
]);
?>

<div class="supplier-view">

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <header><?= Html::encode($model->name) ?></header>

    <p><?= Html::encode($model->address) ?></p>

    <p class="alert alert-info">
        <?= $model->remarks !== null ? Html::encode($model->remarks) : '' ?>
    </p>

    <header>

        <?= Yii::t('app', 'Contacts') ?>

        <?= Html::a(
            ' ' . Html::icon('glyphicon glyphicon-plus-sign'),
            ['contact/create' , 'supplier_id' => $model->id],
            [
                'title' => Yii::t('app', 'Add Contact'),
                'id' => 'supplier-view-add-contact-link'
            ]
        ) ?>

    </header>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $contactDataProvider,
        'columns' => [
            [
                'attribute' => 'name',
                'value' => function (Contact $data) {

                    if ($data->name !== null) {

                        return Html::a(Html::encode($data->name),
                            ['contact/view', 'id' => $data->id]);

                    } else {

                        return '';

                    }

                },
                'format' => 'html',
            ],
            'role',
            'phone',
            'wechat_id',
            'email',
            'remarks',
        ],
    ]) ?>

</div>
