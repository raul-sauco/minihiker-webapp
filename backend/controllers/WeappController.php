<?php

namespace backend\controllers;

use common\models\Image;
use common\models\ImageUploadForm;
use common\models\ProgramGroup;
use common\models\ProgramGroupImage;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class WeappController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['upload-image'],
                        'roles' => ['viewProgram'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionUploadImage($id)
    {
        $pg = $this->findModel($id);

        $iuf = new ImageUploadForm();
        $iuf->file = UploadedFile::getInstance($iuf, 'file');

        if ($iuf->file !== null && $iuf->validate(['file'])) {

            Yii::$app->response->getHeaders()->set('Vary', 'Accept');
            Yii::$app->response->format = Response::FORMAT_JSON;

            $dir = Yii::$app->params['imageDirectory'] . 'pg/' . $id . '/';

            $response = [];

            if ($iuf->save($dir)) {

                $url = Url::to("@web/img/pg/$id/" . $iuf->file_name, true);

                // Link the models with the image
                $imageModel = new Image();
                $imageModel->name = $iuf->file_name;
                $imageModel->type = $iuf->file->type;

                if (!$imageModel->save()) {
                    Yii::error('Error saving image model ' . $iuf->file_name);
                    Yii::error($imageModel->getErrors());
                } else {

                    $pgi = new ProgramGroupImage();
                    $pgi->program_group_id = $pg->id;
                    $pgi->image_id = $imageModel->id;

                    if (!$pgi->save()) {
                        Yii::error('Error saving program_group_image model');
                    }

                    // $thurl = Url::to("img/pg/$id/th/" . $iuf->file_name);

                    // THIS IS THE RESPONSE UPLOADER REQUIRES!
                    $response['files'][] = [
                        'name' => $iuf->file_name,
                        'type' => $iuf->file->type,
                        'size' => $iuf->file->size,
                        'url' => $url,
                        'thumbnailUrl' => $url,
                        'deleteUrl' => (strpos(Url::base(true), 'localhost') ? 'http://localhost/mhapi/' :
                                'https://api.minihiker.com/') . 'bu/' . $imageModel->id,
                        'deleteType' => 'DELETE'
                    ];

                }
            } else {
                $response[] = ['error' => Yii::t('app',
                    'Unable to save picture')];
            }
            @unlink($iuf->file->tempName);
        } else {
            if ($iuf->hasErrors(['picture'])) {
                $response[] = ['errors' => $iuf->getErrors()];
            } else {
                throw new HttpException(500,
                    Yii::t('app',
                        'Could not upload file'));
            }
        }
        return $response;
    }

    /**
     * Finds the Program model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return ProgramGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProgramGroup::findOne($id)) !== null) {

            return $model;

        }

        throw new NotFoundHttpException(
            Yii::t('app', 'The requested page does not exist.'));
    }

}