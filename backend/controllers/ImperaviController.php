<?php
namespace backend\controllers;

use yii\helpers\Url;
use yii\web\Controller;
use vova07\imperavi\actions\GetImagesAction;
use vova07\imperavi\actions\UploadFileAction;
use vova07\imperavi\actions\DeleteFileAction;

/**
 * Class ImperaviController
 * Let users view, upload and delete images on the imperavi editor.
 *
 * @package backend\controllers
 */
class ImperaviController extends Controller
{
    public function actions()
    {
        return [
            'images-get' => [
                'class' => GetImagesAction::class,
                'url' => Url::to('@imgUrl/imperavi', true), // Directory URL address, where files are stored.
                'path' => '@imgPath/imperavi', // Or absolute path to directory where files are stored.
                'options' => ['only' => ['*.jpg', '*.jpeg', '*.png', '*.gif', '*.ico']], // These options are by default.
            ],
            'image-upload' => [
                'class' => UploadFileAction::class,
                'url' => Url::to('@imgUrl/imperavi', true), // Directory URL address, where files are stored.
                'path' => '@imgPath/imperavi', // Or absolute path to directory where files are stored.
            ],
            'file-delete' => [
                'class' => DeleteFileAction::class,
                'url' => Url::to('@imgUrl/imperavi', true), // Directory URL address, where files are stored.
                'path' => '@imgPath/imperavi', // Or absolute path to directory where files are stored.
            ],
        ];
    }
}
