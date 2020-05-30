<?php

namespace backend\helpers;

use common\models\Image;
use common\models\ImageUploadForm;
use common\models\ProgramGroupImage;
use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

/**
 * Class BlueImpImageUploadHelper
 * @package backend\helpers
 */
class BlueImpImageUploadHelper
{
    /**
     * Save an image file and return the response that the
     * BlueImp file upload plugin expects
     * @param ImageUploadForm $iuf
     * @param int $id the ID of the ProgramGroup the images belong to
     * @return array|array[] in the form BlueImp file upload plugin requires
     * @throws ServerErrorHttpException if no file was obtained
     * @throws Exception
     */
    public static function uploadImage(ImageUploadForm $iuf, int $id): array
    {
        if ($iuf->file === null) {
            throw new ServerErrorHttpException(
                Yii::t('app', 'File upload failed')
            );
        }
        if (!$iuf->validate()) {
            return [
                'error' => true,
                'message' => 'Error saving image model',
                'errors' => $iuf->getErrors()
            ];
        }
        $dir = Yii::getAlias('@imgPath/pg/') . $id . '/';
        if (!file_exists($dir)) {
            FileHelper::createDirectory($dir);
        }
        if (!$iuf->save($dir)) {
            return [
                'error' => true,
                'message' => 'Error saving image file',
                'errors' => $iuf->getErrors()
            ];
        }

        $url = Url::to("@imgUrl/pg/$id/" . $iuf->file_name, true);

        // Link the models with the image
        $imageModel = new Image();
        $imageModel->name = $iuf->file_name;
        $imageModel->type = $iuf->file->type;

        if (!$imageModel->save()) {
            Yii::error([
                'Error saving image model ' . $iuf->file_name,
                $imageModel->getErrors()], __METHOD__);
            return [
                'error' => true,
                'message' => 'Error saving image model',
                'errors' => $imageModel->getErrors()
            ];
        }

        $pgi = new ProgramGroupImage();
        $pgi->program_group_id = $id;
        $pgi->image_id = $imageModel->id;

        if (!$pgi->save()) {
            Yii::error(
                "Error saving ProgramGroup $id Image $imageModel->id " .
                'junction model', __METHOD__);
            return [
                'error' => true,
                'message' => 'Error saving ProgramGroupImage model',
                'errors' => $pgi->getErrors()
            ];
        }
        @unlink($iuf->file->tempName);
        return ['files' => [[
            'name' => $iuf->file_name,
            'type' => $iuf->file->type,
            'size' => $iuf->file->size,
            'url' => $url,
            'thumbnailUrl' => $url,
            'deleteUrl' => Yii::$app->params['apiUrl'] . 'bu/' . $imageModel->id,
            'deleteType' => 'DELETE'
        ]]];
    }
}
