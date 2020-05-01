<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\imagine\Image;

class ImageUploadForm extends Model
{
    // Attributes
    private $max_size = 512000;
    public $file_name = null;
    
    /** @var UploadedFile */
    public $file;
    
    /**
     * 
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules()
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
    public function attributeLabels()
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
     * @throws \yii\base\Exception
     */
    public function save($dir = null)
    {
        if ($this->validate('file')) {
            
                        
            // Create a pointer to the new image file
            $folder = $dir ?? Yii::$app->params['imageDirectory'];
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

            // Create a new name
            $name = str_replace('-', '_',
                Yii::$app->security->generateRandomString(20));

            // The file name will be random_string + extension
            $this->file_name = $name . '.' . $this->file->extension;
            
            // $this->file_name = \Yii::$app->security->generateRandomString(20);
            
            if ($this->file->saveAs($folder . $this->file_name)) {
                
                // The file saves correctly
                Yii::info(
                    "Saved file $this->file_name correctly.",
                    __METHOD__);

                // Create thumbnails
                Image::thumbnail($folder . $this->file_name, 100 , null)
                    ->save($thfolder . '/' . $this->file_name, ['quality' => 80]);
                
                return true;
                
            }

            // The file does not save correctly
            $user_id = Yii::$app->user->id;
            Yii::error(
                "Error trying to save image",
                __METHOD__);

            return false;
        }

        // The file does not validate
        Yii::info("Uploaded file failed validation.", __METHOD__);

        return false;
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