<?php

namespace backend\controllers;

use common\models\ProgramGroup;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ProgramController implements the CRUD actions for Program model.
 */
class ProgramGroupController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['weapp-view'],
                        'roles' => ['viewProgram'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['weapp-update','qas'],
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
     * Displays the information related to this ProgramGroup visible
     * on the Weapp.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionWeappView($id)
    {
        return $this->render('weapp-view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates the information related to this ProgramGroup that is displayed on the
     * Weapp. If update is successful, the browser will be redirected to the 'weapp-view' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionWeappUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->redirect(['weapp-view', 'id' => $model->id]);

        }

        return $this->render('weapp-update', [
            'model' => $model,
        ]);
    }

    /**
     * Provide a list of QAs related to this Program Group
     *
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionQas($id)
    {
        $model = $this->findModel($id);
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getQas()->orderBy('created_at DESC'),
            'pagination' => false
        ]);

        return $this->render('qa', [
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
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