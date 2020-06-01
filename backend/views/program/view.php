<?php

use common\models\ProgramGuide;
use common\models\User;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Markdown;

/* @var $this yii\web\View */
/* @var $model common\models\Program */

$this->title = $model->getNamei18n();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-view">

	<?= $this->render('/layouts/_createInfo', ['model' => $model]) ?>

    <p>
        <?= Html::a(Yii::t('app', 'Add Period'),
            ['create', 'group_id' => $model->program_group_id],
            ['class' => 'btn btn-success']) ?>

        <?= Html::a(Yii::t('app', 'Update'),
            ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?php
        $unanswered = $model->programGroup->getQas()
            ->where(['answer' => null])
            ->orWhere(['answer' => ''])
            ->count();

        echo Html::a(
            ($unanswered > 0 ? Html::icon('warning-sign') . ' '  : '') .
            Yii::t('app', 'FAQ') .
            ($unanswered > 0 ? " ($unanswered)" : ''),
            ['program-group/qas', 'id' => $model->program_group_id], [
                    'class' => 'btn ' . ($unanswered > 0 ? 'btn-warning' : 'btn-primary'),
                ]
        ) ?>

        <?= Html::a(
            Yii::t('app', 'Update Participants'),
            ['program-client/update-program-clients', 'program_id' => $model->id],
            ['class' => 'btn btn-primary']
        ) ?>

        <?= Html::a(
            Yii::t('app', 'Update Guides'),
            ['update-guides', 'id' => $model->id],
            ['class' => 'btn btn-primary']
        ) ?>

        <?= Html::a(
            Yii::t('app', 'Export'),
            ['export', 'id' => $model->id],
            ['class' => 'btn btn-primary']
        ) ?>

        <?= Html::a(
            Yii::t('app', 'Delete'),
            ['delete', 'id' => $model->id],
            [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app',
                        'Are you sure you want to delete this item?'),
                    'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="program-group-links">

    	<?php
        $programs = $model->programGroup->getPrograms()
            ->orderBy('program_period_id')->all();

    	foreach ($programs as $program) {

    	    $link = $program->programPeriod->name;
    	    $link .= '. ';
    	    $link .= Yii::$app->formatter->asDate($program->start_date, 'short');
    	    $link .= ' - ';
    	    $link .= Yii::$app->formatter->asDate($program->end_date, 'short');

    	    $link .= Html::tag('div', $program->getLongParticipantCount());

    	    echo Html::a(
    	        $link,
    	        ['view', 'id' => $program->id],
    	        [
    	            'class' => 'btn btn-sm ' .
    	            ($model->id === $program->id ? 'btn-info' : 'btn-default'),
    	        ]) . ' ';

    	}
    	?>

    </div>

    <?php
    if (!empty($model->remarks)) {
        echo Html::tag('div' ,
            Markdown::process(Html::encode($model->remarks)),
            [
                'class' => 'alert alert-info program-remarks-container' ,
                'role' => 'alert',
                'id' => "program-$model->id-remarks"
            ]);
    }
    ?>

    <table class="program-view-table table table-bordered table-striped">
        <thead><tr>
            <th><?= Yii::t('app', 'Participant Count') ?></th>
            <th><?= Yii::t('app', 'Total Contract Balance') ?></th>
            <th><?= Yii::t('app', 'Total Amount Paid') ?></th>
            <th><?= Yii::t('app', 'Total Waiting Payment') ?></th>
        </tr></thead>
        <tbody><tr>
            <td id="program-view-overview-participants"><?= $model->getLongParticipantCount() ?></td>
            <td id="program-view-overview-due"></td>
            <td id="program-view-overview-paid"></td>
            <td id="program-view-overview-balance"></td>
        </tr></tbody>
    </table>

    <table class="program-view-table table table-bordered">

    	<thead>

    		<tr class="shaded-row">

    			<th class="family-serial-column">
    				<?= Yii::t('app', 'Family') ?></th>

    			<th class="family-name-column">
    				<?= Yii::t('app', 'Family Name') ?></th>

                <th class="family-balance-column">
                    <?= Yii::t('app', 'Contract Amount') ?></th>

                <th class="family-balance-column">
                    <?= Yii::t('app', 'Already Paid') ?></th>

                <th class="family-balance-column">
                    <?= Yii::t('app', 'Waiting Payment') ?></th>

    			<th class="family-status-column">
    				<?= Yii::t('app', 'Remarks') ?></th>

    			<th class="serial-column">
    				<?= Yii::t('app', 'Client') ?></th>

    			<th><?= Yii::t('app', 'Participant') ?></th>

    			<th><?= Yii::t('app', 'Remarks') ?></th>

    		</tr>

    	</thead>


    	<tbody>

    		<?php
    		$serial = 0;

    		$familySerial = 0;

    		$participantIds = $model->getClients()->select('id')->column();

    		foreach ($model->getFamilies()->all() as $family) {

    		    $clientQuery = $family->getClients()->where(['id' => $participantIds]);

    		    $clients = $clientQuery->all();

    		    $participantMemberCount = $clientQuery->count();

    		    $kidCount = $clientQuery->andWhere(['is_kid' => true])->count();

    		    $adultCount = $family->getClients()
    		                      ->where(['id' => $participantIds])
    		                      ->andWhere(['is_kid' => false])
    		                      ->count();

    		    echo $this->render('_familyRow', [
    		        'clients' => $clients,
    		        'participantCount' => $participantMemberCount,
    		        'programId' => $model->id,
    		        'model' => $family,
    		        'serial' => $serial,
    		        'familySerial' => ++$familySerial
    		    ]);

    		    $serial += $participantMemberCount;

    		}
    		?>

    	</tbody>

    </table>

    <?php
    $guideDataProvider = new ActiveDataProvider([
        'query' => $model->getGuides(),
    ]);

    echo GridView::widget([
        'dataProvider' => $guideDataProvider,
        'columns' => [
            [
                'label' => Yii::t('app', 'Guide Name'),
                'attribute' => 'username',
            ],
            [
                'label' => Yii::t('app', 'Remarks'),
                'value' => static function(User $data) use ($model) {
                    $pg = ProgramGuide::findOne(['program_id' => $model->id, 'user_id' => $data->id]);
                    return $pg->notes ?? '';
                }
            ],
        ],
    ]);

    ?>

    <div class="weapp-info">
        <?php
        if ($model->programGroup->weapp_visible) {
            $message = Yii::t('app',
                'This program is currently displayed on the Weapp');
        } else {
            $message = Yii::t('app',
                'This program is not currently displayed on the Weapp');
        }

        echo Html::tag('span', $message) . '. ';
        echo Html::a(
                Yii::t('app', 'View'),
                ['weapp/view', 'id' => $model->program_group_id])
        ?>
    </div>

</div>

<?php
$js = 'calculateOverallFinancial();';
$this->registerJs($js);
