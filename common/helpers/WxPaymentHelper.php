<?php

namespace common\helpers;

use common\models\WxUnifiedPaymentOrder;
use Yii;
use yii\db\ActiveQuery;
use yii\web\ServerErrorHttpException;

/**
 * Class WxPaymentHelper
 * Helper functionality related to mini program payments and Wx unified payment orders.
 * @package common\helpers
 */
class WxPaymentHelper
{
    /**
     * Return an i18n label for an order status
     *
     * @param int $i The status
     * @return string The label for the order status
     */
    public static function getStatusLabel (int $i): string
    {
        $labels = [
            0 => Yii::t('app', 'Order Created'),
            1 => Yii::t('app', 'Error generating prepay ID'),
            2 => Yii::t('app', 'Waiting for user confirmation'),
            3 => Yii::t('app', 'Confirmation error'),
            4 => Yii::t('app', 'Payment confirmed'),
            5 => Yii::t('app', 'Order expired'),
            6 => Yii::t('app', 'Payment cancelled by client'),
        ];

        return $labels[$i];
    }

    /**
     * Get a query for Wx Unified Payment Orders with abnormal status.
     * @return ActiveQuery
     */
    public static function getOrdersWithAbnormalStatus(): ActiveQuery
    {
        // Orders that are more than 12 hours old and have a "pending" status
        return WxUnifiedPaymentOrder::find()
            ->where(['<=', 'created_at', strtotime('-12 hours')])
            ->andWhere(['in', 'status', [
                WxUnifiedPaymentOrder::STATUS_CREATED,
                WxUnifiedPaymentOrder::STATUS_PREPAY_ERROR,
                WxUnifiedPaymentOrder::STATUS_WAITING_CONFIRMATION,
                WxUnifiedPaymentOrder::STATUS_CANCELLED_BY_CLIENT
            ]]);
    }

    /**
     * Update pending
     * @return bool
     * @throws ServerErrorHttpException
     */
    public static function updateExpiredOrdersStatus(): bool
    {
        $orderQuery = self::getOrdersWithAbnormalStatus();
        /** @var WxUnifiedPaymentOrder $order */
        foreach ($orderQuery->each() as $order) {
            // The order is more than 12 hours old, has expired.
            $order->status = WxUnifiedPaymentOrder::STATUS_ORDER_EXPIRED;
            if (!$order->save()) {
                $msg = "Error updating wx payment order $order->id status to expired";
                Yii::error($msg, __METHOD__);
                Yii::error($order->errors, __METHOD__);
                throw new ServerErrorHttpException($msg);
            }
        }
        return true;
    }
}
