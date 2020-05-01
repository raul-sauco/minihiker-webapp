<?php

use yii\bootstrap\Html;
use common\models\Contact;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Contact */

$this->title = $model->name;

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Suppliers'),
    'url' => ['supplier/index']
];

$this->params['breadcrumbs'][] = [
    'label' => $model->supplier->name,
    'url' => ['supplier/view', 'id' => $model->supplier_id]
];

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="contact-view">

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

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'role',
            [
                'label' => Yii::t('app', 'Supplier'),
                'value' => function (Contact $data) {
                    return Html::encode($data->supplier->name);
                }
            ],
            [
                'attribute' => 'wechat_id',
                'value' => function (Contact $data) {
                    if ($data->wechat_id !== null) {
                        return Html::icon('message') . ' ' .
                            Html::encode($data->wechat_id);
                    }

                    return '';
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'phone',
                'value' => function (Contact $data) {
                    if ($data->phone !== null) {
                        $phoneUrl = preg_replace('~\s~', '',
                            trim($data->phone));
                        return Html::a(Html::icon('earphone') . ' ' .
                            $data->phone, "tel:$phoneUrl");
                    }

                    return '';
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'email',
                'value' => function (Contact $data) {
                    if ($data->email !== null) {

                        $email = Html::encode($data->email);
                        $text = Html::icon('envelope') . ' ' .
                            ' ' . $email;
                        return Html::a($text, "mailto:$email");
                    }

                    return '';
                },
                'format' => 'html'
            ],
            'remarks:ntext',
        ],
    ]) ?>

</div>
