<?php

namespace api\controllers;

use api\models\ProgramFamily;
use common\controllers\ActiveBaseController;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class ProgramFamilyController
 * @package api\controllers
 */
class ProgramFamilyController extends ActiveBaseController
{
    public $modelClass = ProgramFamily::class;

    /**
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();
        unset(
            $actions['delete'],
            $actions['update'],
            $actions['view']
        );
        return $actions;
    }

    /**
     * @param string $action
     * @param null $model
     * @param array $params
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = []): bool
    {
        if ($action === 'create') {
            return Yii::$app->user->can('user');
        }
        return parent::checkAccess($action, $model, $params);
    }

    /**
     * @param $program_id
     * @param $family_id
     * @return ProgramFamily|null
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionView($program_id,$family_id): ?ProgramFamily
    {
        if (!Yii::$app->user->can('updateProgram')) {
            throw new ForbiddenHttpException(
                Yii::t('app',
                    'You are not allowed to access this resource')
            );
        }
        $programFamily = ProgramFamily::findOne([
            'program_id' => $program_id, 'family_id' => $family_id
        ]);
        if ($programFamily === null) {
                throw new NotFoundHttpException(
                    Yii::t('app',
                        'The resource requested does not exist on this server.')
            );
        }
        return $programFamily;
    }
}
