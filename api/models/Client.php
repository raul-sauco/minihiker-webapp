<?php

namespace api\models;

/**
 * Class Client
 * @package api\models
 */
class Client extends \common\models\Client
{
    /**
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();
        unset(
            $fields['created_at'],
            $fields['created_by'],
            $fields['updated_at'],
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
            'familyName' => static function ($data) {
                /** @var Client $data */
                return $data->family->name;
            }
        ];
    }
}
