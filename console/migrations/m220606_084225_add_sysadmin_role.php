<?php

use common\models\AuthItem;
use yii\db\Migration;

/**
 * Class m220606_084225_add_sysadmin_role
 */
class m220606_084225_add_sysadmin_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $sysadminRole = $auth->createRole(AuthItem::ROLE_SYSADMIN);
        $sysadminRole->description = 'System administrator role. Access and manages system data like logs and RBAC';
        try {
            $auth->add($sysadminRole);
            $auth->assign($sysadminRole, 1);
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->safeDown();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $sysadminRole = $auth->getRole(AuthItem::ROLE_SYSADMIN);
        if ($sysadminRole !== null) {
            $auth->revoke($sysadminRole, 1);
            $auth->remove($sysadminRole);
        }
    }
}
