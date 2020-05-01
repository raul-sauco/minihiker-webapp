<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "location".
 *
 * @property string $name_zh
 * @property string $name_en
 * @property boolean $international
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property Program[] $programs
 * @property ProgramGroup[] $programGroups
 */
class Location extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'location';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name_zh'], 'required'],
            [['name_en'], 'string'],
            [['international', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['name_zh'], 'string', 'max' => 12],
            [['name_zh'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name_zh' => Yii::t('app', 'Chinese name'),
            'name_en' => Yii::t('app', 'English name'),
            'international' => Yii::t('app', 'International'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
    	return [
    		BlameableBehavior::className(),
    		TimestampBehavior::className(),
    	];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrograms()
    {
        $sql = 'select p.* from program p, program_group pg ' .
            'where p.program_group_id=pg.id and pg.location_id=:name';

        return Program::findBySql($sql, [':name' => $this->name_zh]);
    }

    /**
     * Return the total number of clients participating on programs in this
     * location.
     *
     * @return integer
     * @throws \yii\db\Exception
     */
    public function getClientCount()
    {
        $sql = 'SELECT COUNT(*) FROM program p, ' .
            'program_client pc, program_group pg WHERE pg.location_id ' .
            'LIKE :location AND p.id=pc.program_id AND pg.id=p.program_group_id;';

        $count = Yii::$app->db->createCommand($sql,
            [':location' => $this->name_zh])->queryScalar();

        return $count;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramGroups()
    {
        return $this->hasMany(ProgramGroup::className(), ['location_id' => 'name_zh']);
    }
}
