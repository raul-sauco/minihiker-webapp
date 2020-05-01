<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property string $code
 * @property string $name_en
 * @property string $name_fr
 * @property string $name_zh
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['name_en', 'name_fr', 'name_zh'], 'string'],
            [['code'], 'string', 'max' => 2],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => Yii::t('app', 'Code'),
            'name_en' => Yii::t('app', 'Name En'),
            'name_fr' => Yii::t('app', 'Name Fr'),
            'name_zh' => Yii::t('app', 'Name Zh'),
        ];
    }
}
