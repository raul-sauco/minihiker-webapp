<?php

namespace apivp1\helpers;

use common\models\Payment;
use common\models\ProgramClient;
use common\models\ProgramFamily;
use common\models\WxUnifiedPaymentOrder;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * Class PaymentHelper
 * @package apivp1\helpers
 */
class PaymentHelper
{
    /**
     * Create a MH payment to reflect a successful WxPayment
     *
     * @param WxUnifiedPaymentOrder $order
     * @return bool
     */
    public static function registerWxPayment(WxUnifiedPaymentOrder $order): bool
    {
        if (($family = $order->family) === null) {
            Yii::error(
                "Wx Unified Order $order->id does not have a family, cannot create MhPayment",
                __METHOD__);
            return false;
        }

        if (($client = $order->client) === null) {
            Yii::error(
                "Wx Unified Order $order->id does not have a client, cannot create MhPayment",
                __METHOD__);
            return false;
        }

        if (empty($order->total_fee)) {
            Yii::error(
                "Wx Unified Order $order->id does not have total_fee, cannot create MhPayment",
                __METHOD__);
            return false;
        }

        if ($order->price === null || ($program = $order->price->program) === null) {
            Yii::error(
                "Wx Unified Order $order->id is not linked to a program, cannot create MhPayment",
                __METHOD__);
            return false;
        }

        // Modify the updated_at attribute of program.
        /* @var $program TimestampBehavior */
        $program->touch('updated_at');

        // Create and save a new payment.
        $payment = new Payment();
        $payment->family_id = $order->family_id;
        $payment->amount = $order->getOrderAmountRmb();
        $payment->date = date('Y-m-d');
        $payment->program_id = $order->price->program_id;
        $payment->remarks = Yii::t('app',
            'Record autogenerated on success notification received for WxUnifiedPaymentOrder {order}.',
            ['order' => $order->id]);

        if (!$payment->save()) {
            Yii::error(
                "Error saving payment for WxPaymentOrder $order->id.",
                __METHOD__
            );
            Yii::error($payment->errors, __METHOD__);
        }

        // Link the client with the program
        $programClient = new ProgramClient();
        $programClient->program_id = $program->id;
        $programClient->client_id = $client->id;
        $programClient->status = 7;

        // Link the family with the program
        if (($programFamily = ProgramFamily::findOne([
            'program_id' => $program->id,
            'family_id' => $family->id
        ])) === null) {
            $programFamily = new ProgramFamily();
            $programFamily->family_id = $family->id;
            $programFamily->program_id = $program->id;
            $programFamily->status = 7; // All done status, view mh app\helpers\ProgramHelper
            // Do not automatically apply discounts, this could come later
            $programFamily->cost = $order->price->price;
            $programFamily->final_cost = $order->price->price;
            $programFamily->discount = 0;
            $programFamily->remarks = '';
        }
        $programFamily->remarks .= Yii::t('app','Order {order}.',
            ['order' => $order->id]) . ' ' . Yii::t('app', 'Paid') .
            ' ' . $family->category;

        if (!$programFamily->save()) {
            Yii::error(
                "Error saving ProgramFamily for Wx Unified Payment Order $order->id",
                __METHOD__
            );
            Yii::error($programFamily->errors, __METHOD__);
        }

        return true;
    }
}
