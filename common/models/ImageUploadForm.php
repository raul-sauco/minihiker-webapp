<?php

namespace common\models;

use common\helpers\StringHelper;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\imagine\Image;

/**
 * Class ImageUploadForm
 * @package common\models
 */
class ImageUploadForm extends Model
{
    // Attributes
    private $max_size = 512000;
    public $file_name;
    
    /** @var UploadedFile */
    public $file;
    
    /**
     * 
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(): array
    {
        return [
            [['file_name'], 'string', 'max' => 64],
            [['file'], 'required'],
            [['file'], 'image', 'extensions' => 'png, gif, jpg, JPG, jpeg',
                'maxSize' => $this->max_size],
        ];
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \yii\base\Model::attributeLabels()
     */
    public function attributeLabels(): array
    {
        return [
            'file' => Yii::t('app', 'File'),
            'file_name' => Yii::t('app', 'File Name'),
        ];
    }

    /**
     * Saves the data on this model's attributes.
     *
     * @param null $dir The directory in which to save the file
     * @return boolean whether saving was successful
     * @throws Exception
     */
    public function save($dir = null): bool
    {
        if (!$this->validate('file')) {
            Yii::warning(
                "Uploaded file failed validation.",
                __METHOD__);
            return false;
        }

        // Create a pointer to the new image file
        $folder = $dir ?? Yii::getAlias('@imgPath');
        $thfolder = $folder . '/th';

        // Make the directory if it doesn't exist
        if (!is_dir($folder) && !mkdir($folder) && !is_dir($folder)) {
            Yii::error(Yii::t(
                'app', 'Error creating folder ' . $folder),
                __METHOD__);
        }

        if (!is_dir($thfolder) && !mkdir($thfolder) && !is_dir($thfolder)) {
            Yii::error(Yii::t(
                'app', 'Error creating folder ' . $thfolder),
                __METHOD__);
        }

        // The file name will be random_string + extension
        $name = StringHelper::randomStr(20);
        $this->file_name = $name . '.' . $this->file->extension;

        if (!$this->file->saveAs($folder . $this->file_name)) {
            Yii::error(
                "Error trying to save image",
                __METHOD__);
            return false;
        }

        // The file saves correctly
        Yii::info(
            "Saved file $this->file_name correctly.",
            __METHOD__);

        // Create thumbnails
        Image::thumbnail($folder . $this->file_name, 100 , null)
            ->save($thfolder . '/' . $this->file_name, ['quality' => 80]);

        return true;
    }
    
    /**
     * Deletes a file from the local filesystem.
     * 
     * @param string $fileURL the file to delete.
     * @return boolean wether the deletion was successful.
     */
    public function deleteFile($fileURL)
    {
        return unlink($fileURL);
    }
    
    
}