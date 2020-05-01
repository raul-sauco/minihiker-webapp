<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\ProgramClient;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $program common\models\Program */

$this->title = Yii::t('app', 'Update program {program} clients' , 
    ['program' => $program->getNamei18n()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Programs') , 'url' => ['program/index']];
$this->params['breadcrumbs'][] = ['label' => $program->getNamei18n() , 'url' => ['program/view' , 'id' => $program->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update clients');
?>
<div class="client-index">

<?php Pjax::begin(); ?><?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            // 'id',
            // 'name_zh',
            [
                'attribute' => 'name_zh', 
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a(
                        Html::encode($data->getName()), 
                        ['client/view' , 'id' => $data->id] , 
                        ['data' => ['pjax' => 0]]);
                }
            ],  
            [
                // 'attribute' => 'family_id',
                'label' => Yii::t('app', 'Family'),
                'value' => function ($data) {
                return $data->family_id === null?'':
                    Html::a(
                        Html::encode($data->family->name) . ' (' . $data->family->category . ')',
                            ['family/view' , 'id' => $data->family_id],
                            ['data' => ['pjax' => 0]]);
                },
                'format' => 'raw',
            ],
            [
                'label' => Yii::t('app', 'Edit Participants'),
                'value' => function ($client) use ($program){
                    if (ProgramClient::findOne([
                        'program_id' => $program->id , 
                        'client_id' => $client->id]) !== null) {
                            return Html::button(Yii::t('app', 'Remove') , [
                                'class' => 'btn btn-danger btn-sm remove-client-btn manage-client-btn' ,
                                'id' => "manage-program-$program->id-client-$client->id",
                                'data' => [
                                    'program-id' => $program->id,
                                    'client-id' => $client->id,
                                    'create-url' => Yii::$app->urlManager->createUrl(['program-client/create']),
                                    'delete-url' => Yii::$app->urlManager->createUrl(['program-client/delete']),
                                    'add-link-text' => Yii::t('app', 'Add'),
                                    'remove-link-text' => Yii::t('app', 'Remove'),
                                ],
                            ]);
                    } else {
                        return Html::button(Yii::t('app', 'Add') , [
                            'class' => 'btn btn-success btn-sm add-client-btn manage-client-btn' ,
                            'id' => "manage-program-$program->id-client-$client->id",
                            'data' => [
                                'program-id' => $program->id,
                                'client-id' => $client->id,
                                'create-url' => Yii::$app->urlManager->createUrl(['program-client/create']),
                                'delete-url' => Yii::$app->urlManager->createUrl(['program-client/delete']),
                                'add-link-text' => Yii::t('app', 'Add'),
                                'remove-link-text' => Yii::t('app', 'Remove'),
                            ],
                        ]);
                    }
                },
                'format' => 'raw',
            ],
            // 'membership_date',
            // 'is_male:boolean',
            // 'remarks:ntext',
            // 'place_of_residence',
            // 'phone_number',
            // 'phone_number_2',
            // 'email:email',
            // 'wechat_id',
            // 'id_card_number',
            // 'passport_number',
            // 'passport_issue_date',
            // 'passport_expire_date',
            // 'passport_place_of_issue',
            // 'passport_issuing_authority',
            // 'place_of_birth',
            // 'family_id',
            // 'serial_number',
            // 'created_by',
            // 'updated_by',
            // 'created_at',
            // 'updated_at',

            /*[
                'class' => 'yii\grid\ActionColumn',
                'controller' => 'client',
                'template' => '{view}'
            ],*/
        ],
    ]); ?>
<?php 
// Register a click handler anytime that the data gets refreshed
$js = "$('.manage-client-btn').click(function () {manageProgramClient($(this));});";
$this->registerJs($js, View::POS_READY, 'manageProgramClient');

Pjax::end(); ?></div>
