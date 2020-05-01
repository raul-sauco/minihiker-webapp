<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/**
 * This is the model class for table "program_group".
 *
 * @property int $id
 * @property string $name
 * @property int $type_id
 * @property string $location_id
 * @property int $accompanied
 * @property int $weapp_visible
 * @property int $weapp_in_banner
 * @property string $weapp_cover_image
 * @property string $weapp_display_name
 * @property string $theme
 * @property string $summary
 * @property string $keywords
 * @property string $weapp_description
 * @property string $price_description
 * @property string $refund_description
 * @property int $min_age
 * @property int $max_age
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Program[] $programs
 * @property User $createdBy
 * @property Location $location
 * @property ProgramType $type
 * @property User $updatedBy
 * @property ProgramGroupImage[] $programGroupImages
 * @property Image[] $images
 * @property Qa[] $qas
 */
class ProgramGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_id', 'accompanied', 'weapp_visible', 'weapp_in_banner', 'min_age', 'max_age', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['location_id', 'accompanied'], 'required'],
            [['theme', 'summary', 'keywords', 'weapp_description', 'price_description', 'refund_description'], 'string'],
            [[
                'theme',
                'summary',
                'keywords',
                'weapp_description',
                'price_description',
                'refund_description'
            ], 'filter', 'filter' => function ($value) {
                return HtmlPurifier::process($value);
            }],
            [['name'], 'string', 'max' => 128],
            [['location_id'], 'string', 'max' => 12],
            [['weapp_cover_image'], 'string', 'max' => 100],
            [['weapp_display_name'], 'string', 'max' => 50],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::class, 'targetAttribute' => ['location_id' => 'name_zh']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramType::class, 'targetAttribute' => ['type_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'type_id' => Yii::t('app', 'Type'),
            'location_id' => Yii::t('app', 'Location'),
            'accompanied' => Yii::t('app', 'Accompanied'),
            'weapp_visible' => Yii::t('app', 'Weapp Visible'),
            'weapp_in_banner' => Yii::t('app', 'Weapp In Banner'),
            'weapp_cover_image' => Yii::t('app', 'Weapp Cover Image'),
            'weapp_display_name' => Yii::t('app', 'Weapp Display Name'),
            'theme' => Yii::t('app', 'Theme'),
            'summary' => Yii::t('app', 'Summary'),
            'keywords' => Yii::t('app', 'Keywords'),
            'weapp_description' => Yii::t('app', 'Weapp Description'),
            'price_description' => Yii::t('app', 'Price Description'),
            'refund_description' => Yii::t('app', 'Refund Description'),
            'min_age' => Yii::t('app', 'Min Age'),
            'max_age' => Yii::t('app', 'Max Age'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * {@inheritDoc}
     * @see \yii\base\Component::behaviors()
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * Return the ProgramGroup's name in a human readable format.
     * The returned string has been internationalized and encoded to
     * escape HTML entities.
     *
     * @return string The ProgramGroup's name
     */
    public function getNamei18n()
    {

        $name = empty($this->type->name) ? '' :
            ' ' . $this->type->name;

        $name .= ' ' . $this->location_id;

        $name .= empty($this->name) ? '' :
            ' ' . $this->name;

        return Html::encode($name);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrograms()
    {
        return $this->hasMany(Program::class, ['program_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::class, ['name_zh' => 'location_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ProgramType::class, ['id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramGroupImages()
    {
        return $this->hasMany(ProgramGroupImage::class, ['program_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getImages()
    {
        return $this->hasMany(Image::class, ['id' => 'image_id'])->viaTable('program_group_image', ['program_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQas()
    {
        return $this->hasMany(Qa::class, ['program_group_id' => 'id']);
    }
}
