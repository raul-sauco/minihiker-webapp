<?php

namespace api\controllers;

use common\controllers\BaseController;
use common\helpers\BlueimpHelper;
use common\models\Image;
use common\models\ProgramGroup;
use common\models\ProgramGroupImage;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class BlueImpProgramGroupImageUploadController
 * @package api\controllers
 */
class BlueImpProgramGroupImageUploadController extends BaseController
{
    protected $_verbs = ['GET','OPTIONS','DELETE'];

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['options','index','delete'];
        return $behaviors;
    }

    /**
     * Get ProgramImages or a list of all ProgramImages linked to a
     * ProgramGroup. The response will be formatted as expected by
     * the Blueimp file upload widget.
     *
     * https://github.com/blueimp/jQuery-File-Upload
     *
     * @return array
     */
    public function actionIndex(): array
    {
        $get = Yii::$app->request->get();

        if (!empty($get['program-group-id']) &&
            (($pg = ProgramGroup::findOne($get['program-group-id'])) !== null)) {

            $query = $pg->getProgramGroupImages();

        } else {

            $query = ProgramGroupImage::find();

        }

        $images = [];

        /* @var $file ProgramGroupImage */
        foreach ($query->all() as $file) {

            $image = [];
            $image['name'] = $file->image->name;
            $image['size'] = BlueimpHelper::getFileSize($file);
            $image['type'] = $file->image->type;
            $image['url'] = Url::to('@imgUrl/pg/', true) .
                $file->program_group_id . '/' . $file->image->name;
            $image['deleteUrl'] = Url::to('@web/bu/' .
                $file->image->id, true) ;
            $image['deleteType'] = 'DELETE';

            $images[] = $image;

        }

        return $images;

    }

    /**
     * Delete a Program Group Image from the system.
     * It will delete the following related information:
     *      - Image record
     *      - ProgramGroupImage junction record
     *      - Image file
     *      - Image thumbnail file
     *
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws \Throwable
     */
    public function actionDelete($id): array
    {
        $image = Image::findOne($id);

        if ($image === null) {
            throw new NotFoundHttpException(
                Yii::t('app',
                    'The resource requested does not exist on this server.'));
        }

        // Delete all related ProgramGroupImage records
        foreach ($image->programGroupImages as $programGroupImage) {

            // Delete related files
            $folder = Yii::getAlias('@imgPath/pg/') .
                $programGroupImage->program_group_id . '/';
            $fullSize = $folder .  $programGroupImage->image->name;
            if (file_exists($fullSize)) {
                if (!unlink($fullSize)) {
                    Yii::error(Yii::t(
                        'app',
                        'Error unlinking file ' . $fullSize),
                        __METHOD__
                    );
                }
            } else {
                Yii::warning(Yii::t(
                    'app',
                    'Missing image file ' . $fullSize),
                    __METHOD__
                );
            }

            $thumbnail = $folder . 'th/' . $programGroupImage->image->name;
            if (file_exists($thumbnail)) {
                if (!unlink($thumbnail)) {
                    Yii::error(Yii::t(
                        'app',
                        'Error unlinking file ' . $thumbnail),
                        __METHOD__
                    );
                }
            } else {
                Yii::warning(Yii::t(
                    'app',
                    'Missing thumbnail file ' . $thumbnail),
                    __METHOD__
                );
            }


            if (!$programGroupImage->delete()) {
                $errorMsg = Yii::t('app',
                    'Could not delete ProgramGroupImage {img_id}/{pgi_id}.',
                    ['img_id' => $programGroupImage->image_id,
                        ['pgi_id' => $programGroupImage->program_group_id]
                    ]);
                Yii::error($errorMsg, __METHOD__);
                throw new ServerErrorHttpException($errorMsg);
            }

        }

        if (!$image->delete()) {
            $errorMsg = Yii::t(
                'app',
                'Could not delete Image {id}',
                ['id' => $image->id]
            );
            Yii::error($errorMsg, __METHOD__);
            throw new ServerErrorHttpException($errorMsg);
        }

        // Deletion was successful, continue
        return [$image->name => true];
    }
}
