<?php
namespace common\helpers;

use common\models\User;
use Yii;

/**
 * Class UserHelper
 *
 * Encapsulates helper functionality related to app\models\User
 *
 * @package app\helpers
 */
class UserHelper
{
    /**
     * Return the i18n version of the labels for the different user types.
     *
     * @return array
     */
    public static function getUserTypeLabels () :array {

        return [
            User::TYPE_ADMIN => Yii::t('app', 'Administrator'),
            User::TYPE_USER => Yii::t('app', 'Common User'),
            User::TYPE_SUSPENDED => Yii::t('app', 'Suspended'),
            User::TYPE_CLIENT => Yii::t('app', 'Client'),
        ];

    }

    /**
     * Return the i18n version of one user type's label.
     *
     * @param int $userType
     * @return mixed
     */
    public static function getUserTypeLabel (int $userType) : string {

        return self::getUserTypeLabels()[$userType];

    }
}