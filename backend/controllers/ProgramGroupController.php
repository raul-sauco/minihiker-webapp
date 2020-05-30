<?php

namespace backend\controllers;

use common\models\ProgramGroup;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class ProgramGroupController
 * @package backend\controllers
 */
class ProgramGroupController extends Controller
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
                        'actions' => ['qas'],
                        'roles' => ['updateProgram'],
                    ],
                ],
            ]
        ];
    }

    /**
     * Provide a list of QAs related to this Program Group
     *
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionQas($id)
    {
        $model = $this->findModel($id);
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getQas()->orderBy('created_at DESC'),
            'pagination' => false
        ]);

        return $this->render('qa', [
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }

    /**
     * Finds the Program model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return ProgramGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): ProgramGroup
    {
        if (($model = ProgramGroup::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(
            Yii::t('app', 'The requested page does not exist.'));
    }
}
