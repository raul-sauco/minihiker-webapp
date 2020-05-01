<?php

namespace backend\controllers;

use Yii;
use common\models\Expense;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ExpenseController implements the CRUD actions for Expense model.
 */
class ExpenseController extends Controller
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
     * Creates a new Expense model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($family_id)
    {
        $model = new Expense();
        
        $model->family_id = $family_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([
                '/financial/family-view', 
                'id' => $model->family_id,                
            ]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Expense model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([
                '/financial/family-view',
                'id' => $model->family_id,
            ]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Expense model.
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
     * Finds the Expense model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Expense the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Expense::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            Yii::t('app', 'The requested page does not exist.'));
    }
}
