<?php

namespace apivp1\controllers;

use apivp1\models\User;
use common\controllers\BaseController;
use common\helpers\YunhtHelper;
use common\models\Sms;
use common\models\UserSigner;
use yii;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * Class AuthenticController
 * API end point for user real name verification.
 *
 * @package apivp1\controllers
 */
class AuthenticController extends BaseController {
	protected $_verbs = ['GET', 'OPTIONS', 'PUT', 'PATCH'];

    /**
     * Check if the current application user is real name verified.
     *
     * @param $id
     * @return array
     * @throws ForbiddenHttpException
     */
	public function actionView($id)
    {
        // Users can only check their own account.
        if ((int)$id !== (int)Yii::$app->user->id) {
            throw new ForbiddenHttpException(
                Yii::t('app', 'You are not allowed to access this resource')
            );
        }

        /** @var User $user */
        $user = Yii::$app->user->identity;
        if ($user === null || $user->user_type !== User::TYPE_CLIENT) {
            throw new ForbiddenHttpException(
                Yii::t('app', 'Verify real name only supported for "client" type users.')
            );
        }

        // Return only the wanted property.
        return ['is_verify' => $user->is_verify];
    }

	//更新实名认证信息
	public function actionUpdate($id): User {
		//接受认证参数
		$request = Yii::$app->request;
		$params  = $request->bodyParams;
		if (!$id){
			$id=Yii::$app->user->id;
		}
		if (Yii::$app->user->id != $id) {
			throw new UnauthorizedHttpException(Yii::t('app', 'Authentic Update User Error'));
		}
		$model = User::findOne($id);
		if(!$model){
			throw new yii\web\NotFoundHttpException(Yii::t('app','Authentic Update User Not Find'));
		}
		//验证实名数据
		if (Yii::$app->user->identity->is_verify!=1) {
			$transaction=Yii::$app->db->beginTransaction();
			try {
				//$this->checkUinfo($params);
				$params['is_verify'] = 1;
				$model->setScenario(User::SCENARIO_UPDATE_SELF_ACCOUNT);
				$model->load($params, '');
				if ($model->save() === false || $model->hasErrors()) {
					throw new ServerErrorHttpException(Yii::t('app', 'Failed to update the resource for an unknown reason'));
				}
				$this->setUser();
				$transaction->commit();
			}catch (\Exception $e){
				$transaction->rollBack();
				throw new ServerErrorHttpException($e->getMessage());
			}
		}
		return $model;
	}

	//验证实名信息
	private function checkUinfo($data){
		//短信验证码验证
		$count=Sms::find()->where(['mobile'=>$data['phone_number'],'code'=>$data['code']])->andWhere(['>','created_at',time()-60])->count();
		if (!$count){
			throw new ServerErrorHttpException(
				Yii::t('app', 'Failed to Check sms code date')
			);
		}
		$result=YunhtHelper::verify($data);

		if ($result['code']!='200'){
			throw new ServerErrorHttpException(Yii::t('app','Failed to Authentic'));
		}
		return true;
	}

	//创建个人用户
	private function setUser(){
		//$user=Yii::$app->user->identity;
		$user=User::find()->where(['id'=>Yii::$app->user->id])->one();
		$params['userName']=$user->name_zh;
		$params['identityRegion']='0';
		$params['certifyType']='a';
		$params['certifyNum']=$user->id_card_number;
		$params['phoneRegion']='0';
		$params['phoneNo']=$user->phone_number;
		$params['caType']='B2';
		$helper=new YunhtHelper();

		$res=$helper->addUser($params);
		if ($res['code']!='200'&&$res['code']!='20209'){
			throw new ServerErrorHttpException(Yii::t('app','Failed to Set User Person'));
		}
		if ($res['code']=='20209'){
			$info=$helper->getUserIgner([$user->id_card_number]);
			$res['data']['signerId']=$info['data'][0][$user->id_card_number];
		}
		$signer=new UserSigner();
		$signer->uid=$user->id;
		$signer->signerid=(string)$res['data']['signerId'];
		if($signer->save()===false){
			throw new ServerErrorHttpException(Yii::t('app','Failed to Set User Person'));
		}
		return true;
	}
}