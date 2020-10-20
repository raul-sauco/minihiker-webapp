<?php
/**
 * Created by PhpStorm.
 * User: xklxq
 * Date: 2020/9/11
 * Time: 17:00
 * Desc：
 */

namespace common\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends User {
	/**
	 * {@inheritdoc}
	 */


	/**
	 * {@inheritdoc}
	 */
	public function scenarios()
	{
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params)
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