<?php

use common\models\Client;
use common\models\User;
use common\models\WeappLog;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\WeappLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Weapp Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="weapp-log-view">

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
            'message',
            'extra:ntext',
            'res:ntext',
            'req:ntext',
            'level',
            'page',
            'method',
            'line',
            [
                'attribute' => 'timestamp',
                'value' => static function (WeappLog $model) {
                    if (!empty($model->timestamp)) {
                        return Yii::$app->formatter->asDate($model->timestamp / 1000, 'long');
                    }
                    return '';
                }
            ],
            'created_at:datetime',
            'updated_at:relativeTime',
            [
                'label' => Yii::t('app', 'Client'),
                'value' => static function (WeappLog $model) {
                    if (($client = Client::findOne(['user_id' => $model->created_by])) !== null) {
                        return Html::a($client->getName(), ['client/view', 'id' => $client->id]);
                    }
                    return '';
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'created_by',
                'value' => static function (WeappLog $model) {
                    if (($user = $model->createdBy) !== null) {
                        return $model->created_by . ': ' .
                            Html::a($user->username, ['user/view', 'id' => $model->created_by]);
                    }
                    return '';
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (WeappLog $model) {
                    if (($user = $model->updatedBy) !== null) {
                        return $model->updated_by . ': ' .
                            Html::a($user->username, ['user/view', 'id' => $model->updated_by]);
                    }
                    return '';
                },
                'format' => 'html'
            ],
        ],
    ]) ?>

</div>
