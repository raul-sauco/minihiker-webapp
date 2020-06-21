<?php

use backend\assets\TableExportAsset;

/* @var $this yii\web\View */
/* @var $model common\models\Program */

$this->title = Yii::t('app', 'Export') . ' ' .
    $model->getNamei18n();
$this->params['breadcrumbs'][] =
    [
        'label' => Yii::t('app', 'Programs'),
        'url' => ['index']
    ];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-export">
	<?= $this->render('/layouts/_createInfo', ['model' => $model]) ?>
    <table id="program-export-table" class="table table-bordered">
        <?= $this->render('_thead') ?>
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
$this->registerJsFile("@jsUrl/program-export.js", [
    'depends' => [TableExportAsset::class]
]);
