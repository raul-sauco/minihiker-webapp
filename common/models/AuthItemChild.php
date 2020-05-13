<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "auth_item_child".
 *
 * @property string $parent
 * @property string $child
 *
 * @property AuthItem $parent0
 * @property AuthItem $child0
 */
class AuthItemChild extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'auth_item_child';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['parent', 'child'], 'required'],
            [['parent', 'child'], 'string', 'max' => 64],
            [['parent', 'child'], 'unique', 'targetAttribute' => ['parent', 'child']],
            [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::class, 'targetAttribute' => ['parent' => 'name']],
            [['child'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::class, 'targetAttribute' => ['child' => 'name']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'parent' => Yii::t('app', 'Parent'),
            'child' => Yii::t('app', 'Child'),
        ];
    }

    /**
     * Gets query for [[Parent0]].
     *
     * @return ActiveQuery
     */
    public function getParent0(): ActiveQuery
    {
        return $this->hasOne(AuthItem::class, ['name' => 'parent']);
    }

    /**
     * Gets query for [[Child0]].
     *
     * @return ActiveQuery
     */
    public function getChild0(): ActiveQuery
    {
        return $this->hasOne(AuthItem::class, ['name' => 'child']);
    }
}
