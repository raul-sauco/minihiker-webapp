<?php
// Use ImageUploadForm for the images
use common\models\ImageUploadForm;
use dosamigos\fileupload\FileUploadUI;

/* @var $this yii\web\View */
/* @var $model common\models\ProgramGroup */

$js = "getProgramGroupImagesPreview($model->id);";
$this->registerJs($js);

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
]);
