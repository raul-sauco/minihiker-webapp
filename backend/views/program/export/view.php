<?php

use backend\assets\TableExportAsset;

/* @var $this yii\web\View */
/* @var $model common\models\Program */

$this->title = Yii::t('app', 'Export') . ' ' . $model->getNamei18n();

$this->params['breadcrumbs'][] =
    [
        'label' => Yii::t('app', 'Programs'),
        'url' => ['index']
    ];

$this->params['breadcrumbs'][] = $this->title;

TableExportAsset::register($this);
?>

<div class="program-export">
	<?= $this->render('/layouts/_createInfo', ['model' => $model]) ?>
    <?= $this->render('/program/_program-group-links', ['model' => $model]) ?>
    <?= $this->render('/program/_remarks', ['model' => $model]) ?>

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
