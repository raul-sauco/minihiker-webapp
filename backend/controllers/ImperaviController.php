<?php
namespace backend\controllers;

use yii\helpers\Url;
use yii\web\Controller;

class ImperaviController extends Controller
{
    public function actions()
    {
        return [
            'images-get' => [
                'class' => 'vova07\imperavi\actions\GetImagesAction',
                'url' => Url::to('@web/img/imperavi', true), // Directory URL address, where files are stored.
                'path' => '@webroot/img/imperavi', // Or absolute path to directory where files are stored.
                'options' => ['only' => ['*.jpg', '*.jpeg', '*.png', '*.gif', '*.ico']], // These options are by default.
            ],
            'image-upload' => [
                'class' => 'vova07\imperavi\actions\UploadFileAction',
                'url' => Url::to('@web/img/imperavi', true), // Directory URL address, where files are stored.
                'path' => '@webroot/img/imperavi', // Or absolute path to directory where files are stored.
            ],
            'file-delete' => [
                'class' => 'vova07\imperavi\actions\DeleteFileAction',
                'url' => Url::to('@web/img/imperavi', true), // Directory URL address, where files are stored.
                'path' => '@webroot/img/imperavi', // Or absolute path to directory where files are stored.
            ],
        ];
    }
}
