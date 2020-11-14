<?php

namespace backend\controllers;

use common\helpers\ProgramClientHelper;
use common\models\ClientSearch;
use common\models\Program;
use common\models\ProgramClient;
use common\models\ProgramClientSearch;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * ProgramClientController implements the CRUD actions for ProgramClient model.
 */
class ProgramClientController extends Controller
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
                        'actions' => [
                            'index',
                            'update-program-clients',
                            'create',
                            'update',
                            'delete',
                        ],
                        'roles' => ['user'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
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
     * @throws NotFoundHttpException
     */
    public function actionUpdateProgramClients(int $program_id): string
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
     * Deletes an existing ProgramClient model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     * @throws ServerErrorHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete()
    {
        if (Yii::$app->request->isAjax) {
            $program_id = Yii::$app->request->post('program_id');
            $client_id = Yii::$app->request->post('client_id');
            // If the ProgramClient does not exist, don't go any further.
            if (($programClient = ProgramClient::findOne([
                    'program_id' => $program_id, 'client_id' => $client_id
                ])) === null) {
                $msg = "Trying to unlink null ProgramClient p $program_id c $client_id";
                Yii::error($msg, __METHOD__);
                Yii::$app->response->statusCode = 500;
                return $msg;
            }
            // The ProgramClient exists
            // ProgramClientHelper does a double check. Not considered a performance issue.
            if (ProgramClientHelper::safeUnLink($program_id, $client_id)) {

                // The record was added successfully
                $message = Yii::t('app',
                    'Removed {client} from program {program} participants.' ,
                    [
                        'client' => $programClient->client->getName() ,
                        'program' => $programClient->program->getNamei18n(),
                    ]);
                $json = [
                    'message' => $message,
                    'program_id' => $program_id,
                    'client_id' => $client_id,
                    'link_text' => Yii::t('app', 'Add'),
                ];
                return Json::encode($json);

            }
            // There was an error unlinking the records
            $msg = Yii::t('app',
                'An error occurred while trying to remove {client} from program {program}.' ,
                [
                    'client' => $client_id,
                    'program' => $program_id,
                ]);
            Yii::error($msg, __METHOD__);
            Yii::$app->response->statusCode = 500;
            return $msg;
        }
        Yii::error(
            'Non AJAX request received at ProgramClientController->actionCreate' ,
            __METHOD__);
        throw new BadRequestHttpException(
            'The requested address cannot be accessed in that manner.'
        );
    }

    /**
     * Creates a new ProgramClient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws BadRequestHttpException|InvalidConfigException
     * @throws ServerErrorHttpException
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isAjax) {

            $program_id = Yii::$app->request->post('program_id');
            $client_id = Yii::$app->request->post('client_id');

            if (($programClient = ProgramClientHelper::safeLink($program_id, $client_id)) !== null) {

                // The record was added successfully
                // Or it existed previously and a warning was thrown
                $message = Yii::t('app',
                    'Added {client} to program {program} participants.' ,
                    [
                        'client' => $programClient->client->getName() ,
                        'program' => $programClient->program->getNamei18n(),
                    ]);
                $json = [
                    'message' => $message,
                    'program_id' => $program_id,
                    'client_id' => $client_id,
                    'link_text' => Yii::t('app', 'Remove'),
                ];
                return Json::encode($json);

            }

            Yii::$app->response->statusCode = 500;
            return Yii::t('app',
                'An error occurred while trying to add {client} to program {program}.' ,
                [
                    'client' => $client_id,
                    'program' => $program_id,
                ]);
        }
        Yii::error(
            'Non AJAX request received at ProgramClientController->actionCreate' ,
            __METHOD__
        );
        throw new BadRequestHttpException(
            'The requested address cannot be accessed in that manner.'
        );
    }

    /**
     * Updates an existing ProgramClient model. If the update is successful,
     * the browser will be redirected to the 'program/view' page.
     *
     * @param int $program_id
     * @param int $client_id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $program_id, int $client_id)
    {
        if (($model = $this->findModel($program_id, $client_id)) !== null) {
            // Mute the null model warnings.
            throw new NotFoundHttpException(
                Yii::t('app', 'The requested page does not exist.'));
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(
                ['program/view', 'id' => $model->program_id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the ProgramClient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $program_id
     * @param int $client_id
     * @return ProgramClient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $program_id, int $client_id): ?ProgramClient
    {
        if (($model = ProgramClient::findOne(['program_id' => $program_id,
            'client_id' => $client_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * @param $program_id
     * @return Program|null
     * @throws NotFoundHttpException
     */
    protected function findProgram($program_id): ?Program
    {
        if (($program = Program::findOne($program_id)) !== null) {
            return $program;
        }

        throw new NotFoundHttpException(
            Yii::t('app', 'The requested page does not exist.'));
    }
}
