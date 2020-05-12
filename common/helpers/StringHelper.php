<?php

namespace common\helpers;

use DateTime;
use Exception;
use Yii;

class StringHelper
{
    /**
     * Generate a random string, using a cryptographically secure
     * pseudorandom number generator (random_int)
     *
     * For PHP 7, random_int is a PHP core function
     * For PHP 5.x, depends on https://github.com/paragonie/random_compat
     *
     * @param int $length How many characters do we want?
     * @param string $keyspace A string of all possible characters
     *                         to select from
     * @return string
     * @throws \yii\base\Exception
     */
    public static function randomStr(
        $length,
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string
    {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            try {
                $pieces [] = $keyspace[random_int(0, $max)];
            } catch (Exception $e) {
                throw new \yii\base\Exception($e->getMessage());
            }
        }
        return implode('', $pieces);
    }

    /**
     * Return a human readable string representing the time since a given moment.
     *
     *     https://stackoverflow.com/a/18602474/2557030
     *
     * @param $datetime $string any format supported by DateTime
     *     https://www.php.net/manual/en/datetime.formats.compound.php
     * @param bool $full boolean
     * @return string
     */
    public static function timeElapsedString($datetime, $full = false) : string
    {
        $now = new DateTime;
        try {
            $ago = new DateTime($datetime);
        } catch (Exception $e) {
            Yii::error(
                ["Error creating new DateTime with '$datetime' string.", $e],
                __METHOD__);
            return '';
        }
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => Yii::t('app', 'year'),
            'm' => Yii::t('app', 'month'),
            'w' => Yii::t('app', 'week'),
            'd' => Yii::t('app', 'day'),
            'h' => Yii::t('app', 'hour'),
            'i' => Yii::t('app', 'minute'),
            's' => Yii::t('app', 'second'),
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v .
                    (($diff->$k > 1 && Yii::$app->language !== 'zh-CN') ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);

        return $string ? implode(', ', $string) . (' ' . Yii::t('app', 'ago')) :
            Yii::t('app', 'just now');
    }

    /**
     * Return an array with all the words in a string passed as
     * a parameter.
     *
     * @param string $s
     * @return array
     */
    public static function tokenizeString(string $s): array
    {
        // Remove extra spaces
        $s = preg_replace('~\s+~', ' ',
            str_replace(['.',','], ' ', $s));

        // Return all individual words between spaces
        return explode(' ', $s);
    }
}
