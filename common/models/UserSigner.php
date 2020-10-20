<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_signer".
 *
 * @property int $id
 * @property int $uid
 * @property string $signerid
 */
class UserSigner extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_signer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'signerid'], 'required'],
            [['uid'], 'integer'],
            [['signerid'], 'string', 'max' => 30],
            [['uid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'signerid' => 'Signerid',
        ];
    }
}
