<?php

namespace api\helpers;

use common\models\ProgramGroupImage;
use Yii;

/**
 * Class BlueimpHelper
 * @package api\helpers
 */
class BlueimpHelper
{
    /**
     * Return the size of an image stored by Blueimp file upload
     * @param ProgramGroupImage $programGroupImage
     * @return bool|false|int
     */
    public static function getFileSize(ProgramGroupImage $programGroupImage)
    {
        $baseBath = Yii::$app->basePath;
        $path = $baseBath . '/web/img/pg/' . $programGroupImage->program_group_id . '/' .
            $programGroupImage->image->name;

        if (file_exists($path)) {
            return filesize($path);
        }

        Yii::error("File $path not found", __METHOD__);
        return false;
    }
}
