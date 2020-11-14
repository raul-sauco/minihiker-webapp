<?php

namespace backend\controllers;

use common\models\ProgramFamily;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ProgramFamilyController implements the CRUD actions for ProgramFamily model.
 */
class ProgramFamilyController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Updates an existing ProgramFamily model.
     * If update is successful, the browser will be redirected to the origin page.
     *
     * @param integer $program_id
     * @param integer $family_id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($program_id, $family_id)
    {
        $model = $this->findModel($program_id, $family_id);

        if ($model->load(Yii::$app->request->post())) {

            $model->cost = empty($model->cost) ? 0 : $model->cost;
            $model->discount = empty($model->discount) ? 0 : $model->discount;

            $model->final_cost = $model->cost - $model->discount;

            if ($model->save()) {

                /*
                 * If the user is viewing program-family details from the
                 * financial view redirect them there after an update,
                 * otherwise redirect them to the program view.
                 */
                if (Yii::$app->request->get('ref') === 'family') {

                    return $this->redirect(['financial/family-view', 'id' => $model->family_id]);

                }

                return $this->redirect(['program/view', 'id' => $model->program_id]);

            }

            return $this->render('update', [
                'model' => $model,
            ]);

        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the ProgramFamily model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $program_id
     * @param integer $family_id
     * @return ProgramFamily the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($program_id, $family_id): ProgramFamily
    {
        if (($model = ProgramFamily::findOne(['program_id' => $program_id, 'family_id' => $family_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            Yii::t('app', 'The requested page does not exist.'));
    }
}
