<?php

namespace backend\controllers;

use Yii;
use common\models\Location;
use common\models\LocationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * LocationController implements the CRUD actions for Location model.
 */
class LocationController extends Controller
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
        								'actions' => ['index', 'list'],
        								'roles' => ['listLocations'],
        						],
        						[
        								'allow' => true,
        								'actions' => ['view'],
        								'roles' => ['viewLocation'],
        						],
        						[
        								'allow' => true,
        								'actions' => ['create'],
        								'roles' => ['createLocation'],
        						],
        						[
        								'allow' => true,
        								'actions' => ['update'],
        								'roles' => ['updateLocation'],
        						],
        						[
        								'allow' => true,
        								'actions' => ['delete'],
        								'roles' => ['deleteLocation'],
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
     * Lists all Location models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LocationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Return all the location names like the query parameter.
     * 
     * @param string $q
     * @return string JSON representation of the results.
     */
    public function actionList ($q = '')
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $out = ['results' => ['id' => '', 'text' => '']];
        
        $locations = Location::find()
            ->where(['like', 'name_zh' , $q])
            ->select('name_zh AS id, name_zh AS text')
            ->limit(20)
            ->asArray(true)
            ->all();
                
        $out['results'] = $locations;
        
        return $out;
    }

    /**
     * Displays a single Location model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Location model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Location();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->name_zh]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Location model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->name_zh]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Location model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Location model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Location the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Location::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(
                Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
