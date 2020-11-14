<?php

namespace backend\controllers;

use Yii;
use common\models\ProgramFamily;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Program;
use common\models\Family;
use common\models\ProgramClient;

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

    /**
     * Unlink a Program record and a Family record by deleting the corresponding
     * entry on the program_family table.
     *
     * @param integer $program_id
     * @param integer $family_id
     * @return boolean whether the deletion was successful.
     */
    public static function safeUnLink($program_id, $family_id): bool
    {
        Yii::debug("Unlinking Program $program_id and family $family_id", __METHOD__);

        if (($program = Program::findOne($program_id)) === null) {

            Yii::error('Trying to delete a ProgramFamily entry with unexisting program id=' . $program_id, __METHOD__);
            return false;

        }

        if (($family = Family::findOne($family_id)) === null) {

            Yii::error('Trying to delete a ProgramFamily entry with unexisting Family id=' . $family_id, __METHOD__);
            return false;

        }

        if (ProgramFamily::findOne(['program_id' => $program_id, 'family_id' => $family_id]) === null) {

            Yii::error('Trying to delete an non-existing ProgramFamily record, ' .
                "Program id=$program_id, Family id=$family_id.", __METHOD__);
            return false;

        }

        // There were no errors, both models and the link exists
        // Check to see if there are any other clients from that family on the program
        if (!self::hasClientsInProgram($family, $program->id)) {

            Yii::debug('There were no errors, unlinking models', __METHOD__);
            $program->unlink('families', $family, true);

        } else {

            Yii::error('There were no errors but the family still has members on the trip, not unlinking', __METHOD__);
        }

        return true;
    }

    /**
     * Returns whether this Family has any member participating in a given program.
     *
     * @param Family $family
     * @param integer $program_id
     * @return boolean whether this Family still has any members in the program
     */
    protected static function hasClientsInProgram($family, $program_id): bool
    {
        $hasClientOnProgram = false;
        $clients = $family->clients;

        foreach ($clients as $client) {

            if (ProgramClient::findOne(['program_id' => $program_id, 'client_id' => $client->id]) !== null) {

                // There is an entry for this family and program, don't delete the entry
                $hasClientOnProgram = true;
            }
        }

        return $hasClientOnProgram;
    }
}
