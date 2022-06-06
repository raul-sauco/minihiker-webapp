<?php

namespace common\helpers;

use common\models\Log;
use Yii;
use yii\log\Logger;

/**
 * Class LogHelper
 * @package common\helpers
 * @author Raul Sauco <sauco.raul@gmail.com>
 */
class LogHelper
{
    /**
     * Return a textual representation of a log level.
     * @param Log $log
     * @return string
     */
    public static function getLevelText(Log $log): string
    {
        switch ($log->level) {
            case Logger::LEVEL_ERROR:
                $name = Yii::t('app', 'Error');
                break;
            case Logger::LEVEL_WARNING:
                $name = Yii::t('app', 'Warning');
                break;
            case Logger::LEVEL_INFO:
                $name = Yii::t('app', 'Info');
                break;
            case Logger::LEVEL_TRACE:
                $name = Yii::t('app', 'Trace');
                break;
            case Logger::LEVEL_PROFILE:
                $name = Yii::t('app', 'Profile');
                break;
            default:
                $name = Yii::t('app', 'Default');
        }
        return $name . " ($log->level)";
    }
}
