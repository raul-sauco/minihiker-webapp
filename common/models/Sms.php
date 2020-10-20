<?php
/**
 * Created by PhpStorm.
 * User: xklxq
 * Date: 2020/9/10
 * Time: 17:50
 * Desc：
 */

namespace common\models;


use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Sms extends ActiveRecord {
	public static function tableName() {
		return 'sms';
	}

	public function behaviors() {
		return [
			BlameableBehavior::className(),
			TimestampBehavior::className(),
		];
	}
}