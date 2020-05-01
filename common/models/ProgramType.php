<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "program_type".
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 */
class ProgramType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'program_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'name_en'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'name_en' => Yii::t('app', 'Name En'),
        ];
    }
}
