<?php

namespace common\helpers;

use common\models\Image;
use common\models\ProgramGroup;
use common\models\ProgramGroupImage;
use DOMDocument;
use Exception;
use Imagine\Image\Box;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\web\ServerErrorHttpException;

/**
 * Class WxContentHelper
 * @package common\helpers
 */
class WxContentHelper
{
    /**
     * Examine the weapp-description property of a program group to see if it contains
     * any images hosted in remote servers. If any are found, try to download them
     * to the local server and change the links to point to it.
     * @param ProgramGroup $programGroup
     * @return bool true if the images could be downloaded and the links updated
     * @throws ServerErrorHttpException
     */
    public static function copyImagesToLocalServer(ProgramGroup $programGroup): bool
    {
        if (empty($programGroup->weapp_description)) {
            return true;
        }
        $html = $programGroup->weapp_description;
        $images = self::getImageTags($html);
        Yii::error($images, __METHOD__);
        foreach ($images as $image) {
            if (self::isImageRemote($image['src'])) {
                // Make a local copy of the image file
                $new = self::copyImage($image, $programGroup);
                // Replace the links in the text pointing to the image
                $remoteSrc = Html::encode($image['src']);
                $localSrc = Html::encode($new['src']);
                $count = 0;
                $html = str_replace($remoteSrc, $localSrc, $html, $count);
                Yii::debug(
                    "Replaced $count tag/s $remoteSrc with $localSrc",
                    __METHOD__
                );
            }
        }
        $programGroup->weapp_description = $html;
        if (!$programGroup->save()) {
            Yii::error(
                "Error updating pg $programGroup->id images",
                __METHOD__
            );
            return false;
        }
        return true;
    }

    /**
     * Check whether a program group has any remote images
     * @param ProgramGroup $programGroup
     * @return bool
     */
    public static function hasRemoteImages(ProgramGroup $programGroup): bool
    {
        foreach (self::getImageTags($programGroup->weapp_description) as $imageTag) {
            $src = $imageTag['src'];
            if (self::isImageRemote($src)) {
                Yii::debug("Detected remote image $src", __METHOD__);
                return true;
            }
        }
        return false;
    }

    /**
     * Check whether a url pointing to an image is remote or local
     * @param string $url
     * @return bool false if the image is local, true if it is remote
     */
    private static function isImageRemote(string $url): bool
    {
        return !empty($url) && strpos($url, Yii::getAlias('@imgUrl')) === false;
    }

    /**
     * Given an HTML string extract the src and alt attributes of all the
     * image tags found.
     * @param $html
     * @return array an array of arrays with the following information for
     * each image tag found
     * [
     *   'src' => 'https://my.domain.com/img/5aoe4u56aoe4u6'
     *   'alt' => 'This is an image'
     * ]
     */
    private static function getImageTags($html): array
    {
        if (empty($html)) {
            return [];
        }

        // https://www.php.net/manual/en/class.domdocument.php
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadHTML($html);

        $links = [];
        foreach ($doc->getElementsByTagName('img') as $node) {
            $links[] = [
                'src' => $node->attributes->getNamedItem('src')->nodeValue,
                'alt' => $node->attributes->getNamedItem('alt')->nodeValue
            ];
        }
        return $links;
    }

    /**
     * Copy a remote image to the local server and return the
     * new values for src and alt attributes of the image tag
     * @param $image
     * @param $programGroup
     * @return array
     * @throws ServerErrorHttpException
     * @throws \yii\base\Exception
     */
    private static function copyImage($image, $programGroup): array
    {
        $folder = Yii::getAlias('@imgPath/pg/') . $programGroup->id . '/';
        if (!file_exists($folder) && !mkdir($folder, 0777, true)
            && !is_dir($folder)) {
            throw new ServerErrorHttpException(
                Yii::t(
                    'app',
                    'Error creating directory {directory}',
                    ['directory' => $folder]
                )
            );
        }
        $name = null;
        do {
            try {
                $name = StringHelper::randomStr(20);
            } catch (Exception $e) {
                Yii::error('Error generating random string',
                    __METHOD__);
            }
        } while($name === null);
        $path = $folder . $name;

        $tempFileContents = self::getImage($image['src']);
        self::writeToFile($tempFileContents, $path);
        self::writeThumbnail($name, $programGroup);
        self::addImageModel($name, $programGroup);

        return [
            'src' => Yii::getAlias('@imgUrl/pg/') .
                $programGroup->id . '/' . $name,
            'alt' => $name
        ];
    }

    /**
     * Download the image from the server into a temporary file
     *
     * https://stackoverflow.com/a/13168447/2557030
     *
     * @param $url
     * @return bool|string
     */
    private static function getImage($url)
    {
        // Get the schema to send as referer
        $schema = parse_url($url, PHP_URL_SCHEME);
        $domain = parse_url($url, PHP_URL_HOST);
        $referer = $schema . '://' . $domain;

        // Cancel lazy load if found
        if (strpos($url, '&amp;wx_lazy=1') !== false) {
            $url = str_replace('&amp;wx_lazy=1', '', $url);
        }

        // Fetch the image
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, false);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        return($result);
    }

    /**
     * Save a temporary file to it's destination folder
     * @param string $tempName path to the temporary file
     * @param string $path permanent file destination
     */
    private static function writeToFile($tempName, $path): void
    {
        $fp = fopen($path, 'wb');
        fwrite($fp, $tempName);
        fclose($fp);
    }

    /**
     * Create a thumbnail file from a full-size file
     * @param string $name
     * @param ProgramGroup $programGroup
     * @param int $size
     * @throws \yii\base\Exception
     */
    private static function writeThumbnail(
        string $name, ProgramGroup $programGroup, int $size = 100): void
    {
        $thumbDirectory = Yii::getAlias('@imgPath/pg/') . $programGroup->id . '/th/';
        if (!file_exists($thumbDirectory)) {
            FileHelper::createDirectory($thumbDirectory);
        }
        $thumbPath = $thumbDirectory . $name;
        $imagePath = Yii::getAlias('@imgPath/pg/') . $programGroup->id . '/' . $name;
        $width = $height = $size;
        try {
            \yii\imagine\Image::getImagine()
                ->open($imagePath)
                ->thumbnail(new Box($width, $height))
                ->save($thumbPath, ['quality' => 90]);
        } catch (Exception $e) {
            Yii::warning($e->getMessage(), __METHOD__);
            // If generating the thumbnail fails copy the file to use as is
            copy($imagePath, $thumbPath);
        }
    }

    /**
     * Given an image name and a ProgramGroup model, add the Image and
     * ProgramGroupImage to link them.
     * @param string $name
     * @param ProgramGroup $programGroup
     * @return bool true if Image and ProgramGroupImage can be saved to database
     */
    private static function addImageModel(string $name, ProgramGroup $programGroup): bool
    {
        $image = new Image();
        $image->name = $name;
        $image->type = 'todo';
        if (!$image->save()) {
            Yii::error(
                "Error saving image $name for ProgramGroup $programGroup->id",
                __METHOD__
            );
            return false;
        }
        $programGroupImage = new ProgramGroupImage();
        $programGroupImage->program_group_id = $programGroup->id;
        $programGroupImage->image_id = $image->id;
        if (!$programGroupImage->save()) {
            Yii::error(
                "Error saving ProgramGroupImage for ProgramGroup " .
                "$programGroup->id and Image $image->id",
                __METHOD__
            );
            return false;
        }
        return true;
    }
}
