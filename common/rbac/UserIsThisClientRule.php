<?php

namespace common\rbac;

use common\models\Client;
use Yii;
use yii\rbac\Rule;

/**
 * Class UserIsThisClientRule
 * @package app\rbac
 */
class UserIsThisClientRule extends Rule
{
    public $name = 'userIsThisClient';

    /**
     * This rule checks if the client referenced by the parameter client_id corresponds
     * to the current application user.
     *
     * {@inheritDoc}
     * @see \yii\rbac\Rule::execute()
     */
    public function execute($user, $item, $params) : bool
    {
        Yii::debug('Checking if the client id points to the current application user.' .
            "user id: $user Client id: " . $params['client_id'], __METHOD__);

        if (empty($params['client_id'])) {

            Yii::error('client_id parameter cannot be null', __METHOD__);
            return false;

        }

        /** @var Client $client */
        $client = Client::findOne($params['client_id']);

        if ($client === null) {

            Yii::error('Client referenced by id: ' . $params['client_id'] .
                ' is null.',__METHOD__);
            return false;

        }

        // We have a valid client model
        if ( ( (int) $client->user_id) !== $user ) {

            Yii::info('application user\' client and client_id point to ' .
                'different clients, refusing request',
                __METHOD__
            );
            return false;

        }

        Yii::info(
            'application user owns the client\'s account, allowing request.',
            __METHOD__
        );
        return true;
    }
}
