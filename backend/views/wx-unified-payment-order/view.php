<?php

use backend\assets\WxPaymentOrderAsset;
use common\helpers\WxPaymentHelper;
use common\models\WxUnifiedPaymentOrder;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\WxUnifiedPaymentOrder */

$this->title = Yii::t('app', 'Wx Unified Payment Order') . ' - ' . $model->id;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Wx Unified Payment Orders'),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
WxPaymentOrderAsset::register($this);
?>
<div class="wx-unified-payment-order-view">

    <p>
        <?= Html::a(
            Yii::t('app', 'Update'),
            ['update', 'id' => $model->id],
            ['class' => 'btn btn-primary'])
        ?>
        <button class="btn btn-success" id="check-order-status-button"
                data-order-id="<?= $model->id ?>">
            <?= Yii::t('app', 'Check status') ?>
        </button>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
            [
                'label' => Yii::t('app', 'Status'),
                'value' => WxPaymentHelper::getStatusLabel($model->status),
            ],
            'trade_state',
            'trade_state_desc',
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
            [
                'attribute' => 'total_fee',
                'value' => static function ($model) {
                    // Total fee is in cents but we want to display rmb
                    /** @var WxUnifiedPaymentOrder $feeInRmb */
                    return Yii::$app->formatter->asCurrency($model->getOrderAmountRmb());
                }
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
            'transaction_id',
            'bank_type',
            'is_subscribe',
            'fee_type',
            'cash_fee',
            'cash_fee_type',
            'spbill_create_ip',
            'time_start',
            'time_expire',
            'time_end',
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
