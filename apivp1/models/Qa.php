<?php

namespace apivp1\models;

use common\helpers\QaHelper;
use Yii;

/**
 * Class Qa
 * @package apivp1\models
 */
class Qa extends \common\models\Qa
{
    /**
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();
        unset(
            $fields['user_ip'],
            // $fields['created_at'],
            // $fields['updated_at'],
            $fields['created_by'],
            $fields['updated_by']
        );
        return $fields;
    }

    /**
     * @return array
     */
    public function extraFields(): array
    {
        return [
            'wxAccountNickname' => static function (Qa $model) {
                return QaHelper::getWxAccountNickname($model);
            },
            'wxAccountAvatar' => static function (Qa $model) {
                return QaHelper::getWxAccountAvatar($model);
            }
        ];
    }
}
