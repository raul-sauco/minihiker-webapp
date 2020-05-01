<?php

namespace backend\controllers;

use Yii;
use common\models\Payment;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Family;

/**
 * PaymentController implements the CRUD actions for Payment model.
 */
class PaymentController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Creates a new Payment model.
     * If creation is successful, the browser will redirect to:
     *      The program_family/update page if program_id is not null
     *      The financial/family-view page if program_id is null
     *
     * @param integer $family_id
     * @param integer|null $program_id
     * @param string|null $ref The page where the user came from
     * @return mixed
     */
    public function actionCreate($family_id, $program_id = null, $ref = null)
    {
        $model = new Payment();

        $model->family_id = $family_id;
        $model->program_id = $program_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if ($model->program_id === null) {

                return $this->redirect(['financial/family-view', 'id' => $model->family_id]);

            } else {

                return $this->redirect([
                    'program-family/update',
                    'program_id' => $model->program_id,
                    'family_id' => $model->family_id,
                    'ref' => $ref]);

            }

        } else {

            return $this->render('create', [
                'model' => $model,
            ]);

        }
    }

    /**
     * Updates an existing Payment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param string|null $ref The page where the user came from
     * @return mixed
     */
    public function actionUpdate($id, $ref = null)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if ($model->program_id === null || $ref === 'financial') {

                return $this->redirect(['financial/family-view', 'id' => $model->family_id]);

            } else {

                return $this->redirect([
                    'program-family/update',
                    'program_id' => $model->program_id,
                    'family_id' => $model->family_id,
                    'ref' => $ref]);

            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'family' => $this->findFamily($model->family_id),
            ]);
        }
    }

    /**
     * Deletes an existing Payment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        $familyId = $model->family_id;
        
        $model->delete();

        return $this->redirect(['financial/family-view', 'id' => $familyId]);
    }

    /**
     * Finds the Payment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Payment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(
                Yii::t('app', 'The requested page does not exist.'));
        }
    }
    
    /**
     * Finds a Family model based on it's primary key value.
     * If the model does not exist it will return 404.
     * @param integer $family_id
     * @throws NotFoundHttpException
     * @return Family
     */
    protected function findFamily($family_id)
    {
        if (($family = Family::findOne($family_id)) !== null) {
            return $family;
        } else {
            throw new NotFoundHttpException(
                Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
