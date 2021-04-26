<?php

/* @var $this yii\web\View */
/* @var $model common\models\ProgramGroup */

use yii\bootstrap\Html; ?>

<div class="list-item">

    <div class="weapp-program-group-container">

        <div class="pg-image-container">
            <?= Html::img("@web/img/pg/$model->id/" . $model->weapp_cover_image, [
                    'alt' => Yii::t('app', '{item}\'s image',
                        ['item' => $model->weapp_display_name])
            ]) ?>
        </div>

        <div class="pg-details-container">

            <div class="pg-program-name pg-attribute-container">

                <?= Html::a(
                        $model->weapp_display_name,
                        ['program-group/weapp-view', 'id' => $model->id]
                ) ?>

            </div>

            <?php
            $attrs = ['summary','theme','location_id','accompanied','age','period','registration','keywords'];

            foreach ($attrs as $attr) {

                echo $this->render('_attribute', ['model' => $model, 'attr' => $attr]);

            }
            ?>


        </div>

    </div>

</div>




