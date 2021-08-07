<?php

namespace backend\controllers;

use common\helpers\FamilyHelper;
use common\models\Family;
use common\models\FamilySearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * FamilyController implements the CRUD actions for Family model.
 */
class FamilyController extends Controller
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
                        'actions' => ['index'],
                        'roles' => ['listFamilies'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view', 'merge-search'],
                        'roles' => ['viewFamily'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['createFamily'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'merge-confirm'],
                        'roles' => ['updateFamily'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteFamily'],
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
     * Lists all Family models.
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new FamilySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     *  a single Family model.
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
            'programFamilyDataProvider' => new ActiveDataProvider([
                'query' => $model->getProgramFamilies(),
            ]),
        ]);
    }

    /**
     * Display family information and allow to search for other families to see
     * if records are duplicates.
     *
     * @param $id
     * @param string|null $q
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionMergeSearch($id, $q = null): string
    {
        $model = $this->findModel($id);
        // Set default query if null
        if ($q === null) {
            $q = $model->name;
        }
        $searchModel = new FamilySearch();
        $searchDataProvider = $searchModel->searchByQuery($q, $id);
        return $this->render('merge/search', [
            'model' => $model,
            'searchDataProvider' => $searchDataProvider,
            'query' => $q
        ]);
    }

    /**
     * Show two family record details to confirm merge action.
     *
     * @param $id
     * @param $dup
     * @return Response|string
     * @throws NotFoundHttpException
     * @throws yii\web\ServerErrorHttpException
     */
    public function actionMergeConfirm($id, $dup)
    {
        $original = $this->findModel($id);
        $duplicate = $this->findModel($dup);
        if (Yii::$app->request->isPost && FamilyHelper::mergeFamilies($original, $duplicate)) {
            return $this->redirect(['view', 'id' => $original->id]);
        }
        return $this->render('merge/confirm', [
            'original' => $original,
            'duplicate' => $duplicate
        ]);
    }

    /**
     * Creates a new Family model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return Response|string
     */
    public function actionCreate()
    {
        $model = new Family();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Family model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return Response|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
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
     * Deletes an existing Family model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Family model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Family the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Family
    {
        if (($model = Family::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(
            Yii::t('app', 'The requested page does not exist.'));
    }
}
