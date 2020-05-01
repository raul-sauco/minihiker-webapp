<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ClientSearch represents the model behind the search form of `app\models\Client`.
 */
class ClientSearch extends Client
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'family_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['name_zh', 'nickname', 'name_pinyin', 'name_en', 'birthdate', 'remarks', 'phone_number', 'phone_number_2', 'email', 'wechat_id', 'id_card_number', 'passport_number', 'passport_issue_date', 'passport_expire_date', 'passport_place_of_issue', 'passport_issuing_authority', 'place_of_birth'], 'safe'],
            [['is_male', 'is_kid'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Client::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'birthdate' => $this->birthdate,
            'is_male' => $this->is_male,
            'is_kid' => $this->is_kid,
            'passport_issue_date' => $this->passport_issue_date,
            'passport_expire_date' => $this->passport_expire_date,
            'family_id' => $this->family_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name_zh', $this->name_zh])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like', 'name_pinyin', $this->name_pinyin])
            ->andFilterWhere(['like', 'name_en', $this->name_en])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'phone_number', $this->phone_number])
            ->andFilterWhere(['like', 'phone_number_2', $this->phone_number_2])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'wechat_id', $this->wechat_id])
            ->andFilterWhere(['like', 'id_card_number', $this->id_card_number])
            ->andFilterWhere(['like', 'passport_number', $this->passport_number])
            ->andFilterWhere(['like', 'passport_place_of_issue', $this->passport_place_of_issue])
            ->andFilterWhere(['like', 'passport_issuing_authority', $this->passport_issuing_authority])
            ->andFilterWhere(['like', 'place_of_birth', $this->place_of_birth]);

        return $dataProvider;
    }
}
