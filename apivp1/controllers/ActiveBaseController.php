<?php

namespace apivp1\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;

/**
 * Class BaseController
 *
 * @package apivp1\controllers
 * @author Raul Sauco
 */
abstract class ActiveBaseController extends ActiveController
{
    // Allow all methods by default
    protected $_verbs = ['GET','POST','PATCH','PUT','OPTIONS','DELETE'];

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        // remove auth filter
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => $this->_verbs,
                'Access-Control-Allow-Headers' => ['content-type','authorization'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Expose-Headers' => ['Link']
            ],
        ];

        // re-add authentication filter
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];

        return $behaviors;
    }

    /**
     * @param string $action
     * @param null $model
     * @param array $params
     * @throws ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = []): bool
    {
        $className = str_replace('Controller', '',
            substr(strrchr(get_class($this), '\\'), 1));

        $permissionName = $action === 'index' ? "list$className" . 's' : $action . $className;
        if (!Yii::$app->user->can($permissionName)) {
            throw new ForbiddenHttpException(
                Yii::t('app',
                    'You are not allowed to {action} {resource}.',
                    ['action' => $action, 'resource' => $className])
            );
        }
    }

    /**
     * Send the HTTP options available to this route
     */
    public function actionOptions()
    {
        if (Yii::$app->getRequest()->getMethod() !== 'OPTIONS') {
            Yii::$app->getResponse()->setStatusCode(405);
        }

        Yii::$app->getResponse()->getHeaders()->set('Allow', implode(', ', $this->_verbs));
    }
}
