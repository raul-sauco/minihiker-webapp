<?php

namespace api\controllers;

use api\models\Image;
use api\models\ProgramGroup;
use common\controllers\ActiveBaseController;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;

/**
 * Class ImageController
 * @package api\controllers
 */
class ImageController extends ActiveBaseController
{
    public $modelClass = Image::class;
    protected $_verbs = ['GET','OPTIONS'];

    /**
     * Unable actions that modify the data.
     *
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['delete'], $actions['create'], $actions['update']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    /**
     * Prepare the data that will be returned by the index action.
     * @return ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function prepareDataProvider(): ActiveDataProvider
    {
        $id = Yii::$app->request->get('program-group-id');
        if ($id !== null && ($pg = ProgramGroup::findOne($id)) !== null) {
            $query = $pg->getImages();
        } else {
            $query = Image::find();
        }
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
}
