<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
        								'roles' => ['listUsers'],
        						],
        						[
        								'allow' => true,
        								'actions' => ['view'],
        								'roles' => ['viewUser'],
        						],
        						[
        								'allow' => true,
        								'actions' => ['create'],
        								'roles' => ['createUser'],
        						],
        						[
        								'allow' => true,
        								'actions' => ['update'],
        								'roles' => ['updateUser'],
        						],
        						[
        								'allow' => true,
        								'actions' => ['suspend','delete'],
        								'roles' => ['deleteUser'],
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
     * Lists all User models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->where(['not', ['user_type' => User::TYPE_SUSPENDED]]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
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
     * Suspend an existing user, similar to deleting but it will keep the
     * database entry to preserve create/update logs.
     *
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionSuspend($id)
    {
        $model = $this->findModel($id);

        $model->user_type = User::TYPE_SUSPENDED;

        if (!$model->save()) {
            Yii::error('Problem suspending user ' . $id, __METHOD__);
            Yii::error($model->getErrors(), __METHOD__);
        }

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing User model.
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            Yii::t('app', 'The requested page does not exist.'));
    }
}
