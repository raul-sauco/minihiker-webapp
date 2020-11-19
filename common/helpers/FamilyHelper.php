<?php
namespace common\helpers;

use common\models\Family;
use common\models\WxUnifiedPaymentOrder;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\ServerErrorHttpException;

/**
 * Helper functionality for Family model and FamilyController
 *
 * @author Raul Sauco
 */
class FamilyHelper
{
    public const TOTAL_DIGITS_ON_SERIAL_NUMBER = 6;

    /**
     * Takes a string representing the category and returns a
     * string containing the next serial number to be assigned
     * on that category.
     *
     * @param $category string the category to generate for.
     * @return string|null The generated serial or null if no serials were
     * found on the given category.
     */
    public static function generateSerialNumber(string $category): ?string
    {
        $family = Family::find()   
            ->where(['category' => $category])
            ->orderBy('serial_number DESC')
            ->one();
        
        $lastSerialNumber = $family->serial_number;
        
        if ($lastSerialNumber === null) {
            
            return null;
            
        }

        $helper = new self();

        return $helper->getNext($lastSerialNumber, $category);
    }

    /**
     * Takes a serial number of the form:
     *
     *      A000056 or
     *      B000125
     *
     * And returns the corresponding next serial number.
     *
     * @param string $serialNumber The current last serial number.
     * @param $category string The category the family belongs to.
     * @return string The next corresponding serial number.
     */
    protected function getNext(string $serialNumber, string $category): string
    {
        // The above behavior gives problems if a user changes an
        // existing family from one category to another.
        // $letter = $this->getLetter($serialNumber);

        $letter = ($category === '会员') ? 'A' : 'B';

        $number = $this->extractNumber($serialNumber) + 1;

        $digits = self::TOTAL_DIGITS_ON_SERIAL_NUMBER;

        return $letter . sprintf('%0'. $digits . 'd', ($number));
    }

    /**
     * Takes in a serial number and returns the same number
     * padded with the correct number of 0s.
     *
     * @param string $serialNumber
     * @return string The serial number padded with 0s
     */
    public static function padSerial (string $serialNumber): ?string
    {
        $pattern = '~^\w\d{1,6}$~';
        
        if (!preg_match($pattern, $serialNumber)) {
            
            Yii::error(
                Yii::t('app', 'Wrong parameter serial number {serialNumber}.' .
                    'This parameter can only be one character followed by 1 to 6 digits.',
                    ['serialNumber' => $serialNumber])
                ,__METHOD__);
            
            return null;
            
        }

        $helper = new self();

        $letter = $helper->getLetter($serialNumber);

        $number = $helper->extractNumber($serialNumber);

        $number = sprintf('%0'. self::TOTAL_DIGITS_ON_SERIAL_NUMBER . 'd', ($number));

        return $letter . $number;
    }

    /**
     * Remove all non-digit characters from a string.
     *
     * @param string $serialNumber
     * @return integer The serial number with all non-digits removed from it.
     */
    protected function extractNumber(string $serialNumber): int
    {
        // Find non digits 
        $pattern = '~\D~';
        
        // Replace with nothing
        $replacement = '';
        
        return preg_replace($pattern, $replacement, $serialNumber);
    }

    /**
     * Return the first character of the serial number
     * corresponding to the letter.
     *
     * @param string $serialNumber
     * @return string The serial number's letter.
     */
    protected function getLetter(string $serialNumber): string
    {
        return $serialNumber[0];
    }

    /**
     * Returns the Minihiker style serial number for the given family.
     *
     * @param $family Family
     * @return string The Minihiker formatted version of the ID with information
     * on the family's category.
     */
    public static function getFormattedSerial($family)
    {
        $number = str_pad($family->id, 6, 0, STR_PAD_LEFT);

        switch ($family->category)
        {
            case '会员' :
                $letter = 'A';
                break;
            case '非会员':
                $letter = 'B';
                break;
            default:
                $letter = 'Z';
                break;
        }

        return $letter . $number;
    }

    /**
     * Merge a family record's data into another and delete the duplicate.
     *
     * @param Family $original
     * @param Family $duplicate
     * @return bool
     * @throws ServerErrorHttpException
     */
    public static function mergeFamilies(Family $original, Family $duplicate): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        if ($transaction === null) {
            throw new ServerErrorHttpException(
                'Could not initiate database transaction'
            );
        }

        $touched = false;

        if (!empty($duplicate->membership_date &&
                (empty($original->membership_date) ||
                $original->membership_date > $duplicate->membership_date))) {
            $original->membership_date = $duplicate->membership_date;
            $touched = true;
        }

        if (!empty($duplicate->remarks)) {
            $original->remarks .= "\n$duplicate->remarks";
            $touched = true;
        }

        if ($touched && !$original->save()) {
            Yii::error("Error saving family $original->id data",
                __METHOD__);
        }

        // Update related records
        foreach ($duplicate->clients as $client) {
            $client->family_id = $original->id;
            if (!$client->save()) {
                Yii::error("Error saving Client $client->id", __METHOD__);
                Yii::error($client->errors, __METHOD__);
                $transaction->rollBack();
                return false;
            }
        }

        foreach ($duplicate->expenses as $expense) {
            $expense->family_id = $original->id;
            if (!$expense->save()) {
                Yii::error("Error saving Expense $expense->id", __METHOD__);
                Yii::error($expense->errors, __METHOD__);
                $transaction->rollBack();
                return false;
            }
        }

        foreach ($duplicate->importErrors as $importError) {
            $importError->family_id = $original->id;
            if (!$importError->save()) {
                Yii::error("Error saving ImportError $importError->id", __METHOD__);
                Yii::error($importError->errors, __METHOD__);
                $transaction->rollBack();
                return false;
            }
        }

        foreach ($duplicate->payments as $payment) {
            $payment->family_id = $original->id;
            if (!$payment->save()) {
                Yii::error("Error saving Payment $payment->id", __METHOD__);
                Yii::error($payment->errors, __METHOD__);
                $transaction->rollBack();
                return false;
            }
        }

        foreach ($duplicate->programFamilies as $programFamily) {

            $programFamily->family_id = $original->id;
            if (!$programFamily->save()) {
                Yii::error("Error saving ProgramFamily " .
                    "p $programFamily->program_id f $programFamily->family_id",
                    __METHOD__);
                Yii::error($programFamily->errors, __METHOD__);
                $transaction->rollBack();
                return false;
            }
        }

        foreach ($duplicate->wxUnifiedPaymentOrders as $wxUnifiedPaymentOrder) {
            $wxUnifiedPaymentOrder->family_id = $original->id;
            if (!$wxUnifiedPaymentOrder->save()) {
                Yii::error("Error saving WxUnifiedPaymentOrder $wxUnifiedPaymentOrder->id",
                    __METHOD__);
                Yii::error($wxUnifiedPaymentOrder->errors, __METHOD__);
                $transaction->rollBack();
                return false;
            }
        }

        try {
            $duplicate->delete();
        } catch (StaleObjectException $e) {
            Yii::error($e->getMessage(), __METHOD__);
            $transaction->rollBack();
            return false;
        } catch (Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);
            $transaction->rollBack();
            return false;
        }

        try {
            $transaction->commit();
        } catch (Exception $e) {
            Yii::error(
                "Error committing transaction to merge families " .
                "$original->id and $duplicate->id",
                __METHOD__);
            return false;
        }
        return true;
    }

    /**
     * Clean the database from all models that depend on this family.
     * @param Family $family
     * @throws ServerErrorHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public static function prepareForDeletion(Family $family): void
    {
        /** @var WxUnifiedPaymentOrder $order */
        foreach ($family->getWxUnifiedPaymentOrders()->each() as $order) {
            $order->family_id = null;
            if (!$order->save()) {
                $msg = "Error nullifying WxUnifiedPaymentOrder $order->id Family ID";
                Yii::error($msg, __METHOD__);
                throw new ServerErrorHttpException($msg);
            }
        }
        // Delete programFamily records.
    }
}
