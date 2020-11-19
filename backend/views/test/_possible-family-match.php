<?php

use common\models\Family;
use yii\bootstrap\Html;
use yii\web\View;

/* @var $this View */
/* @var $family Family */
$link = Html::a("Family $family->name ($family->id)", ['family/view', 'id' => $family->id]);

?>
<div class="possible-family-match">
    <div>Possible match <?= $link ?></div>
    <?= $this->render('/layouts/_createInfo', ['model' => $family]) ?>
</div>
