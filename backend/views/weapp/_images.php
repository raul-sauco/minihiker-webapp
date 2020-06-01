<?php

use common\helpers\StringHelper;
use common\models\ProgramGroup;
use common\models\ProgramGroupImage;
use yii\bootstrap\Html;
use yii\web\View;

/* @var $this View */
/* @var $model ProgramGroup */

?>
<div class="image-grid">
    <?php
    foreach ($model->images as $image) {
        echo Html::beginTag('div', ['class' => 'grid-item']);
        echo Html::img(
            "@imgUrl/pg/$model->id/" . $image->name, [
            'alt' => $image->name,
            'class' => 'grid-item-image'
        ]);
        echo Html::tag('div', $image->type);
        $pgi = ProgramGroupImage::findOne([
            'image_id' => $image->id,
            'program_group_id' => $model->id
        ]);
        if ($pgi !== null) {
            $file = Yii::getAlias('@imgPath/pg/') .
                $model->id . '/' . $image->name;
            echo Html::tag('div', StringHelper::fileSize($file));
        }
        echo Html::endTag('div');
    }
    ?>
</div>
