<?php

namespace backend\helpers;

use common\models\User;
use Yii;

/**
 * Class RbacPermissionCheckerHelper
 * @package common\helpers
 */
class RbacPermissionCheckerHelper
{
    /**
     * Check if the user's permissions are consistent with the expected
     * logic. If errors are found, return a message with error information.
     * @param User $user
     * @return string
     */
    public static function checkUserPermissions(User $user): ?string
    {
        switch ($user->user_type) {
            case User::TYPE_ADMIN:
                return self::checkAdminPermissions($user);
            case User::TYPE_USER:
                return self::checkRegularUserPermissions($user);
            case User::TYPE_SUSPENDED:
                return self::checkSuspendedPermissions($user);
            case User::TYPE_CLIENT:
                return self::checkClientPermissions($user);
            default:
                return "Unknown type $user->user_type for user $user->id";
        }
    }

    /**
     * Check permissions for an admin user.
     * @param User $admin
     * @return string|null
     */
    protected static function checkAdminPermissions(User $admin): ?string
    {
        if (!self::checkAccess($admin, 'admin')) {
            return "User $admin->id is admin without admin permission";
        }
        return null;
    }

    /**
     * Check permissions for a regular user user type.
     * @param User $user
     * @return string|null
     */
    protected static function checkRegularUserPermissions(User $user): ?string
    {
        if (!self::checkAccess($user, 'user')) {
            return "User $user->id is regular user without 'user' permission";
        }
        return null;
    }

    /**
     * Check permissions for a client user.
     * @param User $user
     * @return string|null
     */
    protected static function checkClientPermissions(User $user): ?string
    {
        if (!self::checkAccess($user, 'client')) {
            return "User $user->id is client without 'client' permission";
        }

        if (self::checkAccess($user, 'admin')) {
            return "User $user->id is client with Admin permission";
        }

        if (self::checkAccess($user, 'user')) {
            return "User $user->id is client with regular user permission";
        }

        return null;
    }

    /**
     * Check permissions for suspended users.
     * @param User $user
     * @return string|null
     */
    protected static function checkSuspendedPermissions(User $user): ?string
    {
        if (self::checkAccess($user, 'client')) {
            return "Suspended user $user->id has client permission";
        }

        if (self::checkAccess($user, 'admin')) {
            return "Suspended user $user->id has admin permission";
        }

        if (self::checkAccess($user, 'user')) {
            return "Suspended user $user->id has user permission";
        }

        return null;
    }

    /**
     * Shortcut to the authManager checkRole method.
     * @param User $user
     * @param string $role
     * @return bool
     */
    protected static function checkAccess(User $user, string $role): bool
    {
        return Yii::$app->authManager->checkAccess($user->id, $role);
    }
}
