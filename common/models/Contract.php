<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\web\UploadedFile;

/**
 * This is the model class for table "contract".
 *
 * @property int $id
 * @property string|null $contractno 合同编号
 * @property string $contractfile 合同文件路径
 * @property string $contracttitle 合同标题
 * @property int $touid 推送用户id
 * @property int $status 合同状态
 * @property int|null $cratetime
 * @property int|null $updatetime
 */
class Contract extends \yii\db\ActiveRecord {
	public $File;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName() {
		return 'contract';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			[['contracttitle','contractno'], 'required'],
			[['contractfile'], 'file', 'extensions' => 'pdf,docx'],
			[['touid', 'status', 'updated_at', 'created_at','user_status','admin_status'], 'integer'],
			[['contractfile', 'contracttitle'], 'string', 'max' => 150],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels() {
		return [
			'id'            => Yii::t('app', 'ID'),
			'contractno'    => Yii::t('app', 'Contract No'),
			'contractfile'  => Yii::t('app', 'Contract File'),
			'contracttitle' => Yii::t('app', 'Contract Title'),
			'touid'         => Yii::t('app', 'Contract Touid'),
			'status'        => Yii::t('app', 'Contract Status'),
			'created_at'    => Yii::t('app', 'Created At'),
			'updated_at'    => Yii::t('app', 'Updated At'),
		];
	}

	public function upload() {
		if ($this->validate('contractfile')) {
			$save_path = Yii::$app->basePath . '/web/contract/';
			if ( ! file_exists($save_path)) {
				mkdir($save_path, 0777, true);
			}
			$file_name = $this->File->baseName . date('YmdHis') . '.' . $this->File->extension;
			$this->File->saveAs($save_path . $file_name);

			return '/contract/' . $file_name;
		} else {
			return false;
		}
	}

	/**
	 * {@inheritDoc}
	 * @see \yii\base\Component::behaviors()
	 */
	public function behaviors() {
		return [
			BlameableBehavior::class,
			TimestampBehavior::class,
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function getAttrStatus($name) {
		$attr[0] = Yii::t('app', 'Contract Status 0');
		$attr[1] = Yii::t('app', 'Contract Status 1');
		$attr[2] = Yii::t('app', 'Contract Status 2');
		$attr[3] = Yii::t('app', 'Contract Status 3');

		return $attr[ $name ];
	}
	/**
	 * @return ActiveQuery
	 */
	public function getUname()
	{
		return $this->hasOne(User::class, ['id' => 'touid']);
	}
}
