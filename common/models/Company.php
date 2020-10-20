<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property string $companyname 企业名称
 * @property int $certifytype 企业证件类型
 * @property string $certifynum 证件号码
 * @property string $phoneno 手机号
 */
class Company extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['companyname', 'certifytype', 'certifynum', 'phoneno','signerid','moulageid'], 'required'],
            [['certifytype'], 'integer'],
	        [['signerid','moulageid'], 'string'],
            [['companyname'], 'string', 'max' => 50],
            [['certifynum'], 'string', 'max' => 20],
            [['phoneno'], 'string', 'max' => 18],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'companyname' => Yii::t('app', '企业名称'),
            'certifytype' => Yii::t('app', '企业证件类型'),
            'certifynum' => Yii::t('app', '证件号码'),
            'phoneno' => Yii::t('app', '手机号'),
        ];
    }
}
