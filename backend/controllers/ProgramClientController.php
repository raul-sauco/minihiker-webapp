<?php

namespace backend\controllers;

use Yii;
use common\models\Client;
use common\models\Program;
use common\models\ProgramClient;
use common\models\ProgramClientSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ClientSearch;
use yii\web\BadRequestHttpException;
use yii\helpers\Json;

/**
 * ProgramClientController implements the CRUD actions for ProgramClient model.
 */
class ProgramClientController extends Controller
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
                    'create' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Renders a page that allows the user to manage the clients currently
     * participating in a program.
     *
     * @param integer $program_id
     * @return string
     */
    public function actionUpdateProgramClients($program_id)
    {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('update-program-clients', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'program' => $this->findProgram($program_id),
        ]);
    }

    /**
     * Lists all ProgramClient models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProgramClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProgramClient model.
     * @param integer $program_id
     * @param integer $client_id
     * @return mixed
     */
    public function actionView($program_id, $client_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($program_id, $client_id),
        ]);
    }

    /**
     * Deletes an existing ProgramClient model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $program_id
     * @param integer $client_id
     * @return mixed
     */
    public function actionDelete()
    {
        if (Yii::$app->request->isAjax) {

            $program_id = Yii::$app->request->post('program_id');
            $client_id = Yii::$app->request->post('client_id');

            if ($this->safeUnLink($program_id, $client_id)) {

                // The record was added successfully
                $message = Yii::t('app',
                    'Removed {client} from program {program} participants.' ,
                    [
                        'client' => Client::findOne($client_id)->getName() ,
                        'program' => Program::findOne($program_id)->getNamei18n(),
                    ]);
                $json = [
                    'message' => $message,
                    'program_id' => $program_id,
                    'client_id' => $client_id,
                    'link_text' => Yii::t('app', 'Add'),
                ];
                return Json::encode($json);

            } else {

                // There was an error unlinking the records
                Yii::$app->response->statusCode = 500;

                return Yii::t('app',
                    'An error ocurred while trying to remove {client} from program {program}.' ,
                    [
                        'client' => $client_id,
                        'program' => $program_id,
                    ]);

            }

        } else {

            Yii::error('Non AJAX request received at ProgramClientController->actionCreate' , __METHOD__);

            throw new BadRequestHttpException('The requested address cannot be accessed in that manner.');
        }
    }

    /**
     * Creates a new ProgramClient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        if (Yii::$app->request->isAjax) {

            $program_id = Yii::$app->request->post('program_id');
            $client_id = Yii::$app->request->post('client_id');

            if ($this->safeLink($program_id, $client_id)) {

                // The record was added successfully
                $message = Yii::t('app',
                    'Added {client} to program {program} participants.' ,
                    [
                        'client' => Client::findOne($client_id)->getName() ,
                        'program' => Program::findOne($program_id)->getNamei18n(),
                    ]);
                $json = [
                    'message' => $message,
                    'program_id' => $program_id,
                    'client_id' => $client_id,
                    'link_text' => Yii::t('app', 'Remove'),
                ];
                return Json::encode($json);

            } else {

                // There was an error inserting
                Yii::$app->response->statusCode = 500;

                return Yii::t('app',
                    'An error ocurred while trying to add {client} to program {program}.' ,
                    [
                        'client' => $client_id,
                        'program' => $program_id,
                    ]);
            }

        } else {

            Yii::error('Non AJAX request received at ProgramClientController->actionCreate' , __METHOD__);

            throw new BadRequestHttpException('The requested address cannot be accessed in that manner.');
        }
    }

    /**
     * Updates an existing ProgramClient model. If the update is successful,
     * the browser will be redirected to the 'program/view' page.
     *
     * @param integer $program_id
     * @param integer $client_id
     * @return mixed
     */
    public function actionUpdate($program_id, $client_id)
    {
        $model = $this->findModel($program_id, $client_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->redirect(
                ['program/view', 'id' => $model->program_id]);

        } else {

            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the ProgramClient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $program_id
     * @param integer $client_id
     * @return ProgramClient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($program_id, $client_id)
    {
        if (($model = ProgramClient::findOne(['program_id' => $program_id,
            'client_id' => $client_id])) !== null) {

            return $model;

        } else {

            throw new NotFoundHttpException(
                Yii::t('app', 'The requested page does not exist.'));

        }
    }

    protected function findProgram($program_id)
    {
        if (($program = Program::findOne($program_id)) !== null) {
            return $program;
        } else {
            throw new NotFoundHttpException(
                Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
    * Links a Program model with a Client model by creating the corresponding record on
    * the program_client table. Performs the necessary checks and updates accordingly the
    * program_family table.
    * @param integer $program_id The id of the Program to link
    * @param integer $client_id The id of the Client to link
    */
    public static function safeLink($program_id, $client_id)
    {
        \Yii::trace("Linking Program $program_id and client $client_id", __METHOD__);

        if (($program = Program::findOne($program_id)) === null) {

            \Yii::error('Trying to create a ProgramClient entry with unexisting program id', __METHOD__);
            return false;

        } else if (($client = Client::findOne($client_id)) === null) {

            \Yii::error('Trying to create a ProgramClient entry with unexisting client_id', __METHOD__);
            return false;

        } else if (ProgramClient::findOne(['program_id' => $program_id, 'client_id' => $client_id]) !== null) {

            \Yii::warning('Trying to create an existing ProgramClient record', __METHOD__);
            return false;

        } else {

            // There were no errors, both models exists and the link does not

            // $model->link() fails to update timestamp and blameable
            // $program->link('clients', $client);

            $programClient = new ProgramClient();
            $programClient->program_id = $program_id;
            $programClient->client_id = $client_id;
            $programClient->save();

            if ($client->family_id !== null) {

                // The client is assigned to a family, link the family and program
                return ProgramFamilyController::safeLink($program->id, $client->family_id);

            } else {

                // The client is not assigned to a family
                return true;
            }
        }
    }

    /**
     * Unlinks a Program record and a Client record by deleting the corresponding
     * entry on the program_client table.
     * @param integer $program_id
     * @param integer $client_id
     * @return boolean wheter the deletion was successful.
     */
    public static function safeUnLink($program_id, $client_id)
    {
        \Yii::trace("Unlinking Program $program_id and client $client_id", __METHOD__);

        if (($program = Program::findOne($program_id)) === null) {

            \Yii::error('Trying to delete a ProgramClient entry with unexisting program id', __METHOD__);
            return false;

        } else if (($client = Client::findOne($client_id)) === null) {

            \Yii::error('Trying to delete a ProgramClient entry with unexisting client_id', __METHOD__);
            return false;

        } else if (ProgramClient::findOne(['program_id' => $program_id, 'client_id' => $client_id]) === null) {

            \Yii::warning('Trying to delete an non-existing ProgramClient record, ' .
                "Program id=$program_id, Client id=$client_id." , __METHOD__);
            return false;

        } else {

            // There were no errors, both models exists and the link does not
            \Yii::info('There were no errors, unlinking models', __METHOD__);
            $program->unlink('clients', $client, true);

            if ($client->family_id !== null) {
                return ProgramFamilyController::safeUnLink($program->id, $client->family_id);
            } else {
                return true;
            }
        }
    }
}
