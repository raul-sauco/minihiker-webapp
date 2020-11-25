<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class UserSearch.
 * @package common\models
 */
class UserSearch extends User {

    /**
     * Creates data provider instance with search query applied.
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

	/**
	 * {@inheritdoc}
	 */
	public function scenarios()
	{
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

    /**
     * Created by PhpStorm.
     * User: xklxq
     * Date: 2020/9/11
     * Time: 17:00
     * Desc：
     */
	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function searchYHTUser($params)
	{
		$query = User::find();
		// add conditions that should always apply here
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		$this->load($params);
		// grid filtering conditions
		$query->andFilterWhere([
			'is_verify' => 1,
		]);
		$query->andFilterWhere(['like', 'name_zh', $this->name_zh]);
		return $dataProvider;
	}
}