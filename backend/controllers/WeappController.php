<?php

namespace backend\controllers;

use backend\helpers\BlueImpImageUploadHelper;
use common\models\ImageUploadForm;
use common\models\ProgramGroup;
use common\models\WeappProgramSearch;
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
                        'actions' => ['index'],
                        'roles' => ['listPrograms'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['upload-image', 'view'],
                        'roles' => ['viewProgram'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['updateProgram'],
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
     * @param string $q
     * @return string
     */
    public function actionIndex($q = ''): string
    {
        $searchModel = new WeappProgramSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'q' => $q
        ]);
    }

    /**
     * Displays the information related to this ProgramGroup visible
     * on the Weapp.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates the information related to this ProgramGroup that is
     * displayed on the Weapp. If update is successful, the browser
     * will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
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
