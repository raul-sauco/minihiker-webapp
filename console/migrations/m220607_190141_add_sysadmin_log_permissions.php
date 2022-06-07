<?php

use common\models\AuthItem;
use yii\db\Migration;

/**
 * Class m220607_190141_add_sysadmin_log_permissions
 */
class m220607_190141_add_sysadmin_log_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $sysadminRole = $auth->getRole(AuthItem::ROLE_SYSADMIN);
        $adminRole = $auth->getRole(AuthItem::ROLE_ADMIN);
        $userRole = $auth->getRole(AuthItem::ROLE_USER);
        $clientRole = $auth->getRole(AuthItem::ROLE_CLIENT);
        $createLog = $auth->createPermission('createLog');
        $createLog->description = 'Create a log';
        $auth->add($createLog);
        $auth->addChild($sysadminRole, $createLog);
        $auth->addChild($adminRole, $createLog);
        $auth->addChild($userRole, $createLog);
        $auth->addChild($clientRole, $createLog);

        $updateLog = $auth->createPermission('updateLog');
        $updateLog->description = 'Update a log';
        $auth->add($updateLog);
        $auth->addChild($sysadminRole, $updateLog);

        $viewLog = $auth->createPermission('viewLog');
        $viewLog->description = 'View a single log details';
        $auth->add($viewLog);
        $auth->addChild($sysadminRole, $viewLog);

        $listLogs = $auth->createPermission('listLogs');
        $listLogs->description = 'View a list of logs';
        $auth->add($listLogs);
        $auth->addChild($sysadminRole, $listLogs);

        $deleteLog = $auth->createPermission('deleteLog');
        $deleteLog->description = 'Delete a log';
        $auth->add($deleteLog);
        $auth->addChild($sysadminRole, $deleteLog);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $sysadminRole = $auth->getRole(AuthItem::ROLE_SYSADMIN);
        $adminRole = $auth->getRole(AuthItem::ROLE_ADMIN);
        $userRole = $auth->getRole(AuthItem::ROLE_USER);
        $clientRole = $auth->getRole(AuthItem::ROLE_CLIENT);
        $createLog = $auth->getPermission('createLog');
        $auth->removeChild($sysadminRole, $createLog);
        $auth->removeChild($adminRole, $createLog);
        $auth->removeChild($userRole, $createLog);
        $auth->removeChild($clientRole, $createLog);
        $updateLog = $auth->getPermission('updateLog');
        $auth->removeChild($sysadminRole, $updateLog);
        $viewLog = $auth->getPermission('viewLog');
        $auth->removeChild($sysadminRole, $viewLog);
        $listLogs = $auth->getPermission('listLogs');
        $auth->removeChild($sysadminRole, $listLogs);
        $deleteLog = $auth->getPermission('deleteLog');
        $auth->removeChild($sysadminRole, $deleteLog);
        $auth->remove($createLog);
        $auth->remove($updateLog);
        $auth->remove($viewLog);
        $auth->remove($listLogs);
        $auth->remove($deleteLog);
    }
}
