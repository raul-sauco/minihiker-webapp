<?php

namespace backend\controllers;

use common\models\Family;
use common\models\FamilySearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;
use common\models\Wallet;
use yii\data\ActiveDataProvider;

/**
 * FamilyController implements the CRUD actions for Family model.
 */
class FinancialController extends Controller
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
                        'actions' => ['family-index'],
                        'roles' => ['listFamilies'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['family-view'],
                        'roles' => ['viewFamily'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    // 'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    /**
     * Renders financial information related to all families on the database.
     * @return string
     */
    function actionFamilyIndex()
    {
        $searchModel = new FamilySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('family-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Renders the financial information related to a Family model.
     *
     * @param integer $id The Family id
     * @return string The view displaying the financial information.
     * @throws NotFoundHttpException if the model cannot be found.
     */
    public function actionFamilyView($id)
    {
        $family = $this->findFamily($id);
        
        return $this->render('family-view', [
            'family' => $family,
        ]);
    }
    
    /**
     * Recovers a single Family model from the database and returns it.
     * If the model doesn't exists it will throw an exception and return 404.
     *
     * @param integer $id The Family id.
     * @return mixed The Family model or it will throw an NotFoundHttpException.
     * @throws NotFoundHttpException if the requested model does not exist.
     */
    protected function findFamily($id)
    {
        if (($model = Family::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(
                Yii::t('app', 'The requested page does not exist.'));
        }        
    }
}