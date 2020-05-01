<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "import_error".
 *
 * @property integer $id
 * @property string $message
 * @property string $field
 * @property string $field_value
 * @property string $validation_errors
 * @property integer $client_id
 * @property integer $family_id
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Client $client
 * @property Family $family
 */
class ImportError extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'import_error';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message', 'field_value', 'validation_errors'], 'string'],
            [['client_id', 'family_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['field'], 'string', 'max' => 64],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['family_id'], 'exist', 'skipOnError' => true, 'targetClass' => Family::className(), 'targetAttribute' => ['family_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'message' => Yii::t('app', 'Message'),
            'field' => Yii::t('app', 'Field'),
            'field_value' => Yii::t('app', 'Field Value'),
            'validation_errors' => Yii::t('app', 'Validation Errors'),
            'client_id' => Yii::t('app', 'Client ID'),
            'family_id' => Yii::t('app', 'Family ID'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFamily()
    {
        return $this->hasOne(Family::className(), ['id' => 'family_id']);
    }
}
