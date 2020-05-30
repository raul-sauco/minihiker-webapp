<?php

namespace backend\controllers;

use backend\helpers\BlueImpImageUploadHelper;
use common\models\ImageUploadForm;
use common\models\ProgramGroup;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Class WeappController
 * @package backend\controllers
 */
class WeappController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['upload-image'],
                        'roles' => ['viewProgram'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
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
     * @throws Exception
     */
    public function actionUploadImage($id)
    {
        $pg = $this->findModel($id);

        Yii::$app->response->getHeaders()->set('Vary', 'Accept');
        Yii::$app->response->format = Response::FORMAT_JSON;

        $iuf = new ImageUploadForm();
        $iuf->file = UploadedFile::getInstance($iuf, 'file');

        return BlueImpImageUploadHelper::uploadImage($iuf, $pg->id);
    }

    /**
     * Finds the Program model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return ProgramGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): ProgramGroup
    {
        if (($model = ProgramGroup::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(
            Yii::t('app', 'The requested page does not exist.'));
    }
}
