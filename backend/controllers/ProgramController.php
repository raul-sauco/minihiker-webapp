<?php

namespace backend\controllers;

use common\helpers\ProgramHelper;
use common\models\Client;
use common\models\Expense;
use common\models\Payment;
use common\models\Program;
use common\models\ProgramClient;
use common\models\ProgramFamily;
use common\models\ProgramGroup;
use common\models\ProgramGuide;
use common\models\ProgramPrice;
use common\models\ProgramSearch;
use common\models\ProgramUser;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ProgramController implements the CRUD actions for Program model.
 */
class ProgramController extends Controller
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
                                    'actions' => ['index'],
                                    'roles' => ['listPrograms'],
                            ],
                            [
                                    'allow' => true,
                                    'actions' => ['view','export'],
                                    'roles' => ['viewProgram'],
                            ],
                            [
                                    'allow' => true,
                                    'actions' => ['create'],
                                    'roles' => ['createProgram'],
                            ],
                            [
                                    'allow' => true,
                                    'actions' => ['update','update-guides'],
                                    'roles' => ['updateProgram'],
                            ],
                            [
                                    'allow' => true,
                                    'actions' => ['delete'],
                                    'roles' => ['deleteProgram'],
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
     * Lists all Program models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProgramSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Program model.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        ProgramHelper::markUserViewedProgram($model);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Program model.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionExport($id)
    {
        return $this->render('export/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Program model. If creation is successful, the browser
     * will be redirected to the 'view' page.
     *
     * @param integer|null $group_id The id of the corresponding ProgramGroup
     *      if the model has one.
     * @return mixed The form to create a new Program.
     * @throws NotFoundHttpException
     */
    public function actionCreate($group_id = null)
    {
        $model = new Program();

        if ($group_id === null) {

            $pg = new ProgramGroup();

        } else {

            $pg = $this->findProgramGroup($group_id);

        }

        if ($model->load(Yii::$app->request->post()) &&
            $pg->load(Yii::$app->request->post())) {

            if ($pg->save()) {

                // ProgramGroup has been saved
                $model->program_group_id = $pg->id;

                if ($model->save()) {

                    return $this->redirect(['view', 'id' => $model->id]);

                }

                // Saved ProgramGroup but failed to save Program
                return $this->redirect(['create', 'group_id' => $pg->id]);

            }

            Yii::warning($pg->errors);

            // If the ProgramGroup fails to save return the errors
            return $this->render('create', [
                'model' => $model,
                'pg' => $pg,
            ]);

        }

        // There is no data to load
        return $this->render('create', [
            'model' => $model,
            'pg' => $pg,
        ]);
    }

    /**
     * Updates an existing Program model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @param null $ref
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id, $ref = null)
    {
        $model = $this->findModel($id);

        $pg = $model->programGroup;

        if ($pg->load(Yii::$app->request->post()) &&
            $model->load(Yii::$app->request->post()) &&
            $pg->save() && $model->save()) {

            if ($ref === 'weapp') {
                return $this->redirect(['program-group/weapp-view', 'id' => $model->program_group_id]);
            }

            // If it is a regular update redirect to the view page
            return $this->redirect(['view', 'id' => $model->id]);

        }

        return $this->render('update', [
            'model' => $model,
            'pg' => $pg,
        ]);
    }

    /**
     * Edits the guides of a given program.
     *
     * @param integer $id The program to edit
     * @return string If values have been POSTed and they can be updated
     * correctly it will display the updated program view, otherwise
     * an error message or the update view will be sent to the client
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdateGuides($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {

            Yii::debug("Editing guides for Program $model->id", __METHOD__);

            // The request is a POST, the user has selected clients, try to update
            // Get the old guides list
            $oldGuidesId = $model->getProgramGuides()->select('user_id')->asArray()->column();

            // Get the new client's list
            $postValues = Yii::$app->request->post('Program')['guides'];
            $newGuidesId = empty($postValues)?[]:$postValues;

            // Find out clients that have been added or removed
            $removedGuidesId = array_diff($oldGuidesId, $newGuidesId);
            $addedGuidesId = array_diff($newGuidesId, $oldGuidesId);

            // Use the arrays to update the database
            if(!empty($removedGuidesId)) {
                foreach ($model->getGuides()->where(['id' => $removedGuidesId])->all() as $guide) {
                    if($model->getProgramGuides()->where(['user_id' => $guide->id])->count() > 0) {
                        Yii::debug("Unlinking Program $model->id and user $guide->id", __METHOD__);
                        $model->unlink('guides', $guide, true);
                    }
                }
            }

            if (!empty($addedGuidesId)) {
                foreach (Client::find()->where(['id' => $addedGuidesId])->all() as $guide) {
                    if ($model->getGuides()->where(['id' => $guide->id])->count() < 1) {
                        Yii::trace("Linking Program $model->id and user $guide->id", __METHOD__);
                        // $model->link('users', $guide);
                        $pg = new ProgramGuide();
                        $pg->program_id = $model->id;
                        $pg->user_id = $guide->id;
                        $pg->save();
                    }
                }
            }

            // Show the view page for the program, it will show newly added clients
            return $this->redirect(['view', 'id' => $model->id]);

        }

        return $this->render('updateGuides', [
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing Program model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        Expense::deleteAll(['program_id' => $model->id]);

        Payment::deleteAll(['program_id' => $model->id]);

        ProgramClient::deleteAll(['program_id' => $model->id]);

        ProgramFamily::deleteAll(['program_id' => $model->id]);

        ProgramGuide::deleteAll(['program_id' => $model->id]);

        ProgramPrice::deleteAll(['program_id' => $model->id]);

        ProgramUser::deleteAll(['program_id' => $model->id]);

        $pg = $model->programGroup;

        $model->delete();

        if ((int)$pg->getPrograms()->count() === 0) {

            return $this->redirect(['index']);

        }

        // There are some other programs on the group show the group page
        $id = $pg->getPrograms()->one()->id;
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Finds the Program model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Program the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Program::findOne($id)) !== null) {

            return $model;

        }

        throw new NotFoundHttpException(
            Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Finds the Program model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $group_id the model's id.
     * @return ProgramGroup if the model is found.
     * @throws NotFoundHttpException if the model cannot be found.
     */
    protected function findProgramGroup($group_id): ProgramGroup
    {
        if (($pg = ProgramGroup::findOne($group_id)) !== null) {

            return $pg;

        }

        Yii::warning("Warning! Failed to find ProgramGroup $group_id",__METHOD__);

        throw new NotFoundHttpException(
            Yii::t('app', 'The requested program group does not exist.'));
    }
}
