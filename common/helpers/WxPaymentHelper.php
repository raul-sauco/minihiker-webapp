<?php
namespace common\helpers;

use Yii;

class WxPaymentHelper
{
    /**
     * Return an i18n label for an order status
     *
     * @param int $i The status
     * @return string The label for the order status
     */
    public static function getStatusLabel (int $i)
    {
        $labels = [
            0 => Yii::t('app', 'Order Created'),
            1 => Yii::t('app', 'Error generating prepay ID'),
            2 => Yii::t('app', 'Waiting for user confirmation'),
            3 => Yii::t('app', 'Confirmation error'),
            4 => Yii::t('app', 'Payment confirmed')
        ];

        return $labels[$i];
    }
}
