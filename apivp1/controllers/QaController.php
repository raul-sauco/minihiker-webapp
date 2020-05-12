<?php

namespace apivp1\controllers;

use apivp1\models\ProgramGroup;
use apivp1\models\Qa;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class QaController
 * @package apivp1\controllers
 */
class QaController extends ActiveBaseController
{
    public $modelClass = Qa::class;

    protected $_verbs = ['GET','POST','OPTIONS'];

    /**
     * TODO maybe add a 'client-create' scenario to prevent clients
     * sending the 'answer' field at the same time they send the
     * 'question'
     *
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['delete'], $actions['update']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    /**
     * Prepare the data that will be returned by the index action.
     *
     * @return ActiveDataProvider
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function prepareDataProvider()
    {
        $pg = Yii::$app->request->get('program_group');

        if (empty($pg)) {
            throw new BadRequestHttpException(
                Yii::t('app', 'Missing required parameter Program Group')
            );
        }

        if (($programGroup = ProgramGroup::findOne($pg)) === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'The resource requested does not exist on this server.')
            );
        }

        return new ActiveDataProvider([
            'query' => $programGroup->getQas()
        ]);
    }
}
