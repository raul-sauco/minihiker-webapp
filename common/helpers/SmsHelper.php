<?php
/**
 * Created by PhpStorm.
 * User: xklxq
 * Date: 2020/9/11
 * Time: 13:41
 * Desc：
 */

namespace common\helpers;

use Yii;

class SmsHelper {

	const SmsUrl = 'http://121.40.69.178:8888/sms.aspx';

	public static function send($data) {
		$param['userid']   = Yii::$app->params['SMS_USERID'];
		$param['account']  = Yii::$app->params['SMS_ACCOUNT'];
		$param['password'] = Yii::$app->params['SMS_PASSWORD'];
		$param['mobile']   = $data['mobile'];
		$param['content']  = $data['content'];
		$param['action']   = 'send';

		$res    = HttpHelper::get(self::SmsUrl, $param);
		$result = json_decode(json_encode(simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		if ($result['returnstatus']=='Success')
			return true;
		return false;
	}
}