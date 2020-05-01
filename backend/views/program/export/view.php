<?php

use yii\helpers\Html;
use yii\helpers\Markdown;

/* @var $this yii\web\View */
/* @var $model common\models\Program */

$this->title = Yii::t('app', 'Export') . ' ' . $model->getNamei18n();

$this->params['breadcrumbs'][] =
    [
        'label' => Yii::t('app', 'Programs'),
        'url' => ['index']
    ];

$this->params['breadcrumbs'][] = $this->title;

// Register the export plugin
\backend\assets\TableExportAsset::register($this);
?>

<div class="program-export">

	<?= $this->render('/layouts/_createInfo', ['model' => $model]) ?>

    <div class="program-group-links">

        <?php
        foreach ($model->programGroup->programs as $program) {

            $link = $program->programPeriod->name;
            $link .= '. ';
            $link .= Yii::$app->formatter->asDate($program->start_date, 'short');
            $link .= ' - ';
            $link .= Yii::$app->formatter->asDate($program->end_date, 'short');

            $link .= Html::tag('div', $program->getLongParticipantCount());



            echo Html::a(
                    $link,
                    ['export', 'id' => $program->id],
                    [
                        'class' => 'btn btn-sm ' .
                            ($model->id == $program->id ? 'btn-info' : 'btn-default'),
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

    <!-- --------------------------------------------------
    -------- From here down is the new view code ----------
    --------------------------------------------------- -->

    <table class="program-view-table program-view-table-old">

    	<thead>

    		<tr>

    			<th class="serial-number-column">
    				<?= Yii::t('app', 'Serial Number') ?></th>

    			<th class="wechat-column">
    				<?= Yii::t('app', 'Wechat Username') ?></th>

    			<th class="participant-count-column">
    				<?= Yii::t('app', 'Participant Count') ?></th>

    			<th><?= Yii::t('app', 'Participant Name') ?></th>

    			<th><?= Yii::t('app', 'Passport Expiry Date') ?></th>

    			<th><?= Yii::t('app', 'Phone Number') ?></th>

    			<th><?= Yii::t('app', 'Email') ?></th>

    			<th><?= Yii::t('app', 'Already Paid') ?></th>

    			<th><?= Yii::t('app', 'Program Fee') ?></th>

    			<th><?= Yii::t('app', 'Address') ?></th>

    			<th><?= Yii::t('app', 'Remarks') ?></th>

    			<th><?= Yii::t('app', 'Id Card Number') ?></th>

    		</tr>

    	</thead>


    	<tbody>

    		<?php
    		$serial = 0;

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
    		        'participantIds' =>$participantIds,
    		        'kidCount' => $kidCount,
    		        'adultCount' => $adultCount,
    		    ]);

    		    $serial += $participantMemberCount;

    		}
    		?>

    	</tbody>

    </table>

</div>

<?php
$js = "$('table.program-view-table-old').tableExport({
    filename: 'program$model->id',
    bootstrap: true,
    position: 'top',
});";

$this->registerJs($js);
