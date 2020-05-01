<?php

use common\helpers\WxPaymentHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\WxUnifiedPaymentOrder */

$this->title = Yii::t('app', 'Wx Unified Payment Order') . ' - ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wx Unified Payment Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="wx-unified-payment-order-view">

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
            // 'id',
            [
                'label' => Yii::t('app', 'Family'),
                'value' => empty($model->family_id) ? '' :
                    Html::a($model->family->name, ['family/view', 'id' => $model->family_id]),
                'format' => 'html'
            ],
            [
                'label' => Yii::t('app', 'Program'),
                'value' => empty($model->price_id) ? '' :
                    Html::a($model->price->program->getNamei18n(),
                        ['program/view', 'id' => $model->price->program_id]),
                'format' => 'html'
            ],
            [
                'label' => Yii::t('app', 'Product'),
                'value' => empty($model->price_id) ? '' :
                    $model->price->getNamei18n()
            ],
            'total_fee:currency',
            [
                'label' => Yii::t('app', 'Status'),
                'value' => WxPaymentHelper::getStatusLabel($model->status)
            ],
            'appid',
            'mch_id',
            'device_info',
            'nonce_str',
            'sign',
            'sign_type',
            'body',
            'detail:ntext',
            'attach',
            'out_trade_no',
            'fee_type',
            'spbill_create_ip',
            'time_start',
            'time_expire',
            'goods_tag',
            'notify_url:url',
            'trade_type',
            'product_id',
            'limit_pay',
            'openid',
            'receipt',
            'scene_info',
            'prepay_id',
            'prepay_sign',
            'prepay_timestamp',
            'notify_xml:ntext',
            'notify_result_code',
            'notify_return_code',
            'notify_err_code',
            'notify_err_code_des',
            'created_by',
            'updated_by',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
