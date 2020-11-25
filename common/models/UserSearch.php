<?php

namespace common\models;

use yii\data\ActiveDataProvider;

/**
 * Class UserSearch
 * @package common\models
 */
class UserSearch extends User {

    /**
     * Creates data provider instance with search query applied
     * @param string $q
     * @return ActiveDataProvider
     */
	public function search(string $q): ActiveDataProvider
    {
		$query = User::find();
		if ($q !== '') {
		    $query->where(['like', 'username' , $q])
                ->orWhere(['like', 'name_zh', $q])
                ->orWhere(['like', 'name_en', $q])
                ->orWhere(['like', 'name_pinyin', $q])
                ->orWhere(['like', 'id_card_number', $q])
                ->orWhere(['like', 'passport_number', $q])
                ->orWhere(['like', 'phone_number', $q])
                ->orWhere(['like', 'id_card_number', $q])
                ->orWhere(['like', 'id_card_number', $q]);
        }
		// Only not suspended users.
        $query->andWhere(['not', ['user_type' => User::TYPE_SUSPENDED]]);
		// add conditions that should always apply here
        return new ActiveDataProvider([
            'query' => $query,
        ]);
	}
}
