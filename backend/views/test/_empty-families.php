<?php

use common\models\Family;
use yii\bootstrap\Html;
use yii\web\View;

/* @var $this View */
$sql = 'SELECT * FROM family where not exists (' .
    'select client.id from client where client.family_id=family.id)';
$familyQuery = Family::findBySql($sql);
$count = $familyQuery->count();

echo Html::beginTag('div', ['id' => 'empty-families']);
if ($count > 0) {
    echo Html::tag('h4', "$count empty families");
    /** @var Family $family */
    foreach ($familyQuery->each() as $family) {
        $link = Html::a("family $family->id", ['family/view', 'id' => $family->id]);
        echo Html::tag('div', "Empty $link");
        echo $this->render('/layouts/_createInfo', ['model' => $family]);
    }
} else {
    echo Html::tag('h4', 'No empty families');
}
echo Html::endTag('div');
