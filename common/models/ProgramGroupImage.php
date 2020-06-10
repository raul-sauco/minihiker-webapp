<?php

namespace common\models;

use common\helpers\ImageHelper;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "program_group_image".
 *
 * @property int $program_group_id
 * @property int $image_id
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property Image $image
 * @property ProgramGroup $programGroup
 */
class ProgramGroupImage extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'program_group_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['program_group_id', 'image_id'], 'required'],
            [['program_group_id', 'image_id', 'created_by', 'updated_by',
                'created_at', 'updated_at'], 'integer'],
            [['program_group_id', 'image_id'], 'unique', 'targetAttribute' =>
                ['program_group_id', 'image_id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' =>
                User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' =>
                User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['image_id'], 'exist', 'skipOnError' => true, 'targetClass' =>
                Image::class, 'targetAttribute' => ['image_id' => 'id']],
            [['program_group_id'], 'exist', 'skipOnError' => true, 'targetClass' =>
                ProgramGroup::class, 'targetAttribute' =>
                ['program_group_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'program_group_id' => Yii::t('app', 'Program Group ID'),
            'image_id' => Yii::t('app', 'Image ID'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function afterDelete(): void
    {
        $image = $this->image;
        if ($image !== null && (int)$image->getProgramGroupImages()->count() === 0) {
            ImageHelper::removeProgramGroupImage($image, $this);
            $image->delete();
        }
        parent::afterDelete();
    }

    /**
     *
     * {@inheritDoc}
     * @see \yii\base\Component::behaviors()
     */
    public function behaviors(): array
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCreatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @return ActiveQuery
     */
    public function getImage(): ActiveQuery
    {
        return $this->hasOne(Image::class, ['id' => 'image_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProgramGroup(): ActiveQuery
    {
        return $this->hasOne(ProgramGroup::class, ['id' => 'program_group_id']);
    }
}
