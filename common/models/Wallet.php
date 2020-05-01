<?php

namespace common\models;

use Yii;

/**
 * Defines the types of "wallets" that the clients can use to store value
 * and pay for expenses.
 * 
 * @author i
 *
 */
class Wallet
{
    const WALLET_TYPE_CARD = 1;
    const WALLET_TYPE_RESERVE = 2;
    
    /**
     * Returns the commercial names of the Wallet types currently in use.
     * 
     * @return string[]
     */
    static function getWalletTypes ()
    {
        return [
            Wallet::WALLET_TYPE_CARD => Yii::t('app', 'Outdoor class stored value card'),
            Wallet::WALLET_TYPE_RESERVE => Yii::t('app', 'Set aside value'),
        ];
    }
}