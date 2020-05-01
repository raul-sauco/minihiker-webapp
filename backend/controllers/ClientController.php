<?php

namespace backend\controllers;

use common\models\Client;
use common\models\ProgramClient;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ClientController implements the CRUD actions for Client model.
 */
class ClientController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['listClients'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view','check-unique-zh-name'],
                        'roles' => ['viewClient'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['createClient'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['updateClient'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteClient'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Client models.
     * todo move search data to ClientSearch
     *
     * @param string|null $queryString The string to search for.
     * @param bool|null $selectAll Whether to show all clients or only kids.
     *      Defaults to only kids.
     * @return mixed
     */
    public function actionIndex(
        $queryString = null, $selectAll = null, $birthdate_after = null, $birthdate_before = null)
    {
        $query = Client::find();
        
        // Implement the search using LIKE %query%
        if (!empty($queryString)) {
            
            $query->where(['like', 'name_zh', $queryString]);
            $query->orWhere(['like', 'nickname' , $queryString]);
            $query->orWhere(['like', 'name_pinyin' , $queryString]);
            $query->orWhere(['like', 'name_en' , $queryString]);
            
        }
        
        // Select only clients that are kids
        if (strcmp($selectAll, 'on') === 0) {
            
            $query->andWhere(['is_kid' => 1]);
            
        }

        if (!empty($birthdate_after)) {

            $query->andWhere(['>=', 'birthdate', $birthdate_after]);

        }

        if (!empty($birthdate_before)) {

            $query->andWhere(['<=', 'birthdate', $birthdate_before]);

        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 100]

        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'queryString' => $queryString,
            'selectAll' => $selectAll,
            'birthdate_before' => $birthdate_before,
            'birthdate_after' => $birthdate_after
        ]);
    }

    /**
     * Displays a single Client model.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    /**
     * Checks the name passed as a parameter against the database,
     * if the name already exists it returns a warning message,
     * otherwise it returns null.
     * 
     * @param string $name The name to check for.
     * @return string|integer a warning if the client is found or 0 otherwise
     */
    public function actionCheckUniqueZhName($name) {
        
        $client = Client::findOne(['name_zh' => $name]);
        
        if ($client === null) {
            return 0;
        }

        return $this->renderAjax('nameNotUniqueWarning', ['client' => $client]);
    }

    /**
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($family_id)
    {
        $model = new Client();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            return $this->redirect(['view', 'id' => $model->id]);
            
        }

        $model->family_id = $family_id;

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
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
     * Deletes an existing Client model.
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
        $client = $this->findModel($id);

        // Delete all the client's program_client records
        ProgramClient::deleteAll(['client_id' => $client->id]);

        $client->delete();

        return $this->redirect(['family/view', 'id' => $client->family_id]);
    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            Yii::t('app', 'The requested page does not exist.'));
    }
}
