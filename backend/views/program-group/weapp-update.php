<?php

use common\models\ImageUploadForm;
use common\models\User;
use dosamigos\fileupload\FileUploadUI;
use vova07\imperavi\Widget;
use yii\bootstrap\Html;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProgramGroup */

$model_name = $model->weapp_display_name ?? $model->getNamei18n();

$this->title = Yii::t('app', 'Update Weapp Data {programName}', [
    'programName' => $model_name,
]);
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Programs'),
    'url' => ['program/index']
];

$this->params['breadcrumbs'][] = [
    'label' => $model_name,
    'url' => ['program/view', 'id' => $model->getPrograms()->one()->id]
];

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Weapp Data'),
    'url' => ['weapp-view', 'id' => $model->id]
];

$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div id="program-group-weapp-data-update">


    <!-- The modal -->
    <div class="modal fade" id="cover-image-selection-modal"
         tabindex="-1" role="dialog" aria-labelledby="modalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modalLabel">
                        <?= Yii::t('app', 'Select Cover Image') ?>
                    </h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <?= Yii::t('app', 'Close') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?= $this->render('/layouts/_createInfo', ['model' => $model]) ?>

    <?php $form = ActiveForm::begin(['id' => 'program-group-form']); ?>

    <div class="row">

        <div class="col-lg-6 left-container">

            <div class="row">

                <div class="col-lg-6">
                    <?= $form->field($model, 'weapp_visible')->dropDownList([
                        0 => Yii::t('app', 'No'),
                        1 => Yii::t('app', 'Yes')
                    ]) ?>
                </div>

                <div class="col-lg-6">
                    <?= $form->field($model, 'weapp_in_banner')->dropDownList([
                        0 => Yii::t('app', 'No'),
                        1 => Yii::t('app', 'Yes')
                    ]) ?>
                </div>

            </div>

            <div class="row">

                <div class="col-lg-12">
                    <?= $form->field($model, 'weapp_display_name')->textInput() ?>
                </div>

            </div>

            <div class="row">

                <div class="col-lg-2">
                    <?= $form->field($model, 'min_age')->textInput() ?>
                </div>

                <div class="col-lg-2">
                    <?= $form->field($model, 'max_age')->textInput() ?>
                </div>

                <div class="col-lg-8">
                    <?= $form->field($model, 'accompanied')
                        ->dropDownList([
                            '0' => Yii::t('app', 'Only childs'),
                            '1' => Yii::t('app', 'With parents')
                        ])->label(Yii::t('app', 'Program Type')) ?>
                </div>

            </div>

            <div class="row">

                <div class="col-lg-12">
                    <?= $form->field($model, 'theme')->textInput() ?>
                </div>

            </div>

            <div class="row">

                <div class="col-lg-12">

                    <?= $form->field($model, 'summary')->textarea(['cols' => 3]) ?>

                    <?= $form->field($model, 'keywords')->textarea(['cols' => 3]) ?>

                </div>

            </div>

        </div>

        <div class="col-lg-6 right-container">

            <div class="form-group field-programgroup-weapp_display_image">

                <label for="programgroup-weapp_cover_image" class="control-label">
                    <?= $model->getAttributeLabel('weapp_cover_image') ?>
                </label>

                <?= Html::activeHiddenInput($model, 'weapp_cover_image' , []) ?>

                <?php
                if (!empty($model->weapp_cover_image)) {

                    // There is a current cover image, display it
                    echo Html::img('@web/img/pg/' . $model->id . '/' .
                        $model->weapp_cover_image, [
                        'id' => 'pg-weapp-cover-image',
                        'alt' => Yii::t('app',
                            '{program-group}\'s cover image', ['program-group' => $model->weapp_display_name]),
                        'data-url' => Url::to('@web/img/pg/' . $model->id . '/')
                    ]);

                    // Update button instead of create
                    echo Html::button(
                            Yii::t('app', 'Update Cover Image'),
                            [
                                'class' => 'btn btn-primary',
                                'id' => 'update-weapp-cover-image',
                                'data-pg-id' => $model->id,
                                'data-url' => Url::to('@web/img/pg/' . $model->id . '/')
                            ]
                    );

                } else {

                    // No current cover image display a placeholder

                    // There is a current cover image, display it
                    echo Html::img('@web/img/no_image.png', [
                        'id' => 'pg-weapp-cover-image',
                        'alt' => Yii::t('app',
                            '{program-group}\'s cover image placeholder', ['program-group' => $model->weapp_display_name]),
                        'data-url' => Url::to('@web/img/pg/' . $model->id . '/')
                    ]);

                    // Update button instead of create
                    echo Html::button(
                        Yii::t('app', 'Select Cover Image'),
                        [
                            'class' => 'btn btn-success',
                            'id' => 'update-weapp-cover-image',
                            'data-pg-id' => $model->id,
                            'data-url' => Url::to('@web/img/pg/' . $model->id . '/')
                        ]
                    );

                }

                ?>

            </div>
        </div>

    </div>

    <div class="row">

        <div class="col-lg-12">

            <?= Tabs::widget([
                'items' => [
                    [
                        'label' => Yii::t('app', 'Weapp Description'),
                        'content' => $form->field($model, 'weapp_description')
                            ->widget(Widget::class, [
                                'settings' => [
                                    'lang' => Yii::$app->language === 'zh-CN' ? 'zh_cn' : 'en',
                                    'minHeight' => 200,
                                    'maxHeight' => 800,
                                    'imageUpload' => Url::to(['/imperavi/image-upload']),
                                    'imageManagerJson' => Url::to(['/imperavi/images-get']),
                                    'imageDelete' => Url::to(['/imperavi/file-delete']),
                                    'plugins' => [
                                        'fontsize',
                                        'clips',
                                        'fullscreen',
                                        'imagemanager'
                                    ],
                                    'clips' => [
                                        ['Lorem ipsum...', 'Lorem...'],
                                        ['red', '<span class="label-red">red</span>'],
                                        ['green', '<span class="label-green">green</span>'],
                                        ['blue', '<span class="label-blue">blue</span>'],
                                    ],
                                ],
                            ]),
                        'active' => true
                    ],
                    [
                        'label' => Yii::t('app', 'Refund Description'),
                        'content' => $form->field($model, 'refund_description')
                            ->widget(Widget::class, [
                                'settings' => [
                                    'lang' => Yii::$app->language === 'zh-CN' ? 'zh_cn' : 'en',
                                    'minHeight' => 200,
                                    'maxHeight' => 800,
                                    'imageUpload' => Url::to(['/imperavi/image-upload']),
                                    'imageManagerJson' => Url::to(['/imperavi/images-get']),
                                    'imageDelete' => Url::to(['/imperavi/file-delete']),
                                    'plugins' => [
                                        'fontsize',
                                        'clips',
                                        'fullscreen',
                                        'imagemanager'
                                    ],
                                    'clips' => [
                                        ['Lorem ipsum...', 'Lorem...'],
                                        ['red', '<span class="label-red">red</span>'],
                                        ['green', '<span class="label-green">green</span>'],
                                        ['blue', '<span class="label-blue">blue</span>'],
                                    ],
                                ],
                            ])
                    ],
                ]
            ]) ?>
        </div>

    </div>

    <div class="row">

        <div class="col-lg-12">
            <?php

            // Find the user access token if set
            $token = null;
            if (isset(Yii::$app->user->id)) {
                if (($user = User::findOne(Yii::$app->user->id)) !== null) {
                    $token = $user->access_token;
                }
            }

            // Use ImageUploadForm for the images
            $iuf = new ImageUploadForm();

            echo FileUploadUI::widget([
                'model' => $iuf,
                'attribute' => 'file',
                'url' => ['weapp/upload-image', 'id' => $model->id],
                'gallery' => false,
                'fieldOptions' => [
                    'accept' => 'image/*'
                ],
                'clientOptions' => [
                    'maxFileSize' => 2000000,
                    // TODO add authentication header here or on clientEvents
                ],
                // ...
                'clientEvents' => [
                    'fileuploaddone' => 'function(e, data) {
                                console.log(e);
                                console.log(data);
                            }',
                    'fileuploadfail' => 'function(e, data) {
                                console.log(e);
                                console.log(data);
                            }',
                ],
            ]); ?>
        </div>

    </div>

    <div class="form-group row">

        <div class="col-lg-12">
            <?= Html::submitButton(
                Yii::t('app', 'Save'),
                ['class' => 'btn btn-success']
            ) ?>
        </div>

    </div>

    <?php

    echo Html::submitButton(
        Yii::t('app', 'Save'),
        ['class' => 'btn btn-success btn-lg fixed-position-save-button']
    );

    ActiveForm::end();

    ?>

    <?php
    $js = "getProgramGroupImagesPreview($model->id);";
    $this->registerJs($js);
    ?>

</div>
