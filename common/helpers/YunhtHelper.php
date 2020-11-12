<?php
/**
 * Created by PhpStorm.
 * User: xklxq
 * Date: 2020/9/9
 * Time: 11:22
 * Desc：
 */

namespace common\helpers;

use GuzzleHttp\Client;
use Yii;

class YunhtHelper {
	const AUTH_PERSONAL_URL = 'https://authentic.yunhetong.com/authentic/personal/simple';//实名认证简项
	const BASE_URL = 'https://api.yunhetong.com/api/';
	const AUTH_TOKEN = 'auth/login';
	const FILE_CONTRACT = 'contract/fileContract';
	const USER_PERSON = 'user/person';
	const CERTIFY_NUMS='user/signerId/certifyNums';
	const ADD_SIGNER='contract/signer';
	const ADD_COMPANY='user/company';
	const COMPANY_MOULAGE='user/companyMoulage';

	protected $client;
	protected $session;

	public function __construct() {
		$this->client  = new Client([
			'base_uri' => self::BASE_URL,
			'curl'     => [CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_SSL_VERIFYHOST => 0]
		]);
		$this->session = Yii::$app->session;
	}

	/**
	 * 云合同令牌获取
	 *
	 * @param string $singerId 可选参数。如不传该参数，那么此时获取的是平台
	 * 自身的长效令牌，反之获取的是指定用户的长效令牌
	 *
	 * @return bool|mixed
	 */
	public function getToken($singerId = '') {
		$session         = Yii::$app->session;
		$param['appId']  = Yii::$app->params['YHT_APPID'];
		$param['appKey'] = Yii::$app->params['YHT_APPKEY'];
		$token           = '';
		if ($singerId) {
			$param['signerId'] = $singerId;
		}
			$options['headers']['Content-Type']='application/json';
			$options['headers']['Charset']='UTF-8';
			$options['json']=$param;
			//var_dump($options);exit();
			$result = $this->parseJSON('POST', self::AUTH_TOKEN, $options);
			if ($result['code'] != 200) {
				$result = false;
			}
		return $result;
	}

	public function Token($singerId = '') {
		$token_name = !$singerId ? 'token_admin' : 'token_user_' . $singerId;
		$token      = $this->session->get($token_name);
		if ( ! $token||!isset($token['time'])||$token['time']<time()) {
			$this->getToken($singerId);
		}

		$res=$this->session->get($token_name);
		return $res['token'];
	}

	public function fileContract($params) {
		$token                 = $this->Token();
		$f_params['headers']   = ['token' => $token];
		$f_params['multipart'] = [
			['name' => 'contractFile', 'contents' => fopen($params['contractFile'],'r'), 'filename' => $params['contractFile']],
			['name' => 'contractTitle', 'contents' => $params['contractTitle']]
		];
		return $this->parseJSON('POST', self::FILE_CONTRACT, $f_params);
	}

	private function parseJSON($method, $url, $options = []) {
		$response = $this->client->request($method, $url, $options);
		if ($response->hasHeader('token')) {
			$session = Yii::$app->session;
			$data['token']   = $response->getHeader('token');
			$data['time']=time()+12*60;
			if (isset($options['json']['signerId'])) {
				$signerid = $options['json']['signerId'];
				$session->set('token_user_' . $signerid, $data);
			} else {
				$session->set('token_admin', $data);
			}
		}

		$res= json_decode($response->getBody()->getContents(), true);
		return $res;
	}

	//实名认证
	public static function verify($data){
		$param['appId']  = Yii::$app->params['YHT_APPID'];
		$param['appKey'] = Yii::$app->params['YHT_APPKEY'];
		//$param['guId']   = $data['id_card_number'];
		$param['idNo']   = $data['id_card_number'];
		$param['idName'] = $data['name_zh'];
		$res= HttpHelper::post(self::AUTH_PERSONAL_URL,$param);
		return json_decode($res,true);
	}

	//创建个人用户
	public  function addUser(Array $data){
		$token=$this->Token();
		$params['headers']=['token'=>$token];
		$params['json']=$data;
		return $this->parseJSON('POST',self::USER_PERSON,$params);
	}
	public function getUserIgner($cardid){
		$param['certifyNumList']=$cardid;
		$token=$this->Token();
		$params['headers']=['token'=>$token];
		$params['json']=$param;
		return $this->parseJSON('POST',self::CERTIFY_NUMS,$params);
	}

	//添加签署者
	public function addSigner($contractId,$signers){
		$token=$this->Token();
		//var_dump($token);exit();
		$params['headers']=['token'=>$token];

		$data['idType']='0';
		$data['idContent']=$contractId;
		$data['signers']=$signers;

		$params['json']=$data;
		return $this->parseJSON('POST',self::ADD_SIGNER,$params);
	}
	//创建企业用户
	public function addCompany($data) {
		$token             = $this->Token();
		$params['headers'] = ['token' => $token];
		$data['caType']    = 'B2';
		$params['json']    = $data;

		return $this->parseJSON('POST', self::ADD_COMPANY, $params);
	}

	//创建企业印章
	public function addCompanyMoulage($signerId){
		$token             = $this->Token();
		$data['signerId']=$signerId;
		$data['styleType']='1';
		$params['headers'] = ['token' => $token];
		$params['json']    = $data;
		return $this->parseJSON('POST',self::COMPANY_MOULAGE,$params);
	}
}