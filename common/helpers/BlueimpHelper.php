<?php

namespace common\helpers;

use common\models\ProgramGroupImage;
use Yii;

/**
 * Class BlueimpHelper
 * @package common\helpers
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
        $path = Yii::getAlias('@imgPath/pg/') .
            $programGroupImage->program_group_id . '/' .
            $programGroupImage->image->name;

        if (file_exists($path)) {
            return filesize($path);
        }

        Yii::error("File $path not found", __METHOD__);
        return false;
    }
}
