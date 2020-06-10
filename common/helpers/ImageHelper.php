<?php

namespace common\helpers;

use common\models\Image;
use common\models\ProgramGroupImage;
use Yii;
use yii\helpers\FileHelper;

/**
 * Class ImageHelper
 * @package common\helpers
 */
class ImageHelper
{
    /**
     * Remove an image file from the file system.
     * @param Image $image
     * @param ProgramGroupImage $programGroupImage
     */
    public static function removeProgramGroupImage(
        Image $image, ProgramGroupImage $programGroupImage): void
    {
        $basePath = Yii::getAlias('@imgPath/pg/') .
            $programGroupImage->program_group_id . '/' ;
        $path = $basePath . $image->name;
        $thumbnailPath = $basePath . '/th/' . $image->name;
        if (file_exists($path)) {
            FileHelper::unlink($path);
        }
        if (file_exists($thumbnailPath)) {
            FileHelper::unlink($thumbnailPath);
        }
    }
}
