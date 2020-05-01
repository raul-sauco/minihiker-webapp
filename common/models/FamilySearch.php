<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FamilySearch represents the model behind the search form of `app\models\Family`.
 */
class FamilySearch extends Family
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['name', 'serial_number', 'category', 'membership_date', 'address', 'place_of_residence', 'remarks'], 'safe'],
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
        $query = Family::find();

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
        /*$query->andFilterWhere([
            'id' => $this->id,
            'membership_date' => $this->membership_date,
            'mother_id' => $this->mother_id,
            'father_id' => $this->father_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);*/

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'serial_number', $this->serial_number])
            // ->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'address', $this->address])
            // ->andFilterWhere(['like', 'place_of_residence', $this->place_of_residence])
            ->andFilterWhere(['like', 'remarks', $this->remarks]);

        return $dataProvider;
    }

    /**
     * Find Family records that match a query string.
     *
     * @param $q
     * @return ActiveDataProvider
     */
    public function searchByQuery($q, $exclude)
    {
        // Allow searching using client's attributes
        $query = Family::find()->joinWith('clients')->distinct();

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        if ($q !== null) {

            // Search family attributes
            $query->where(['like', 'family.id', $q]);
            $query->where(['like', 'family.name', $q]);
            $query->orWhere(['like', 'family.serial_number', $q]);
            $query->orWhere(['like', 'family.address', $q]);
            $query->orWhere(['like', 'family.remarks', $q]);

            // Search client attributes
            $query->orWhere(['like', 'client.name_zh', $q]);
            $query->orWhere(['like', 'client.name_en', $q]);
            $query->orWhere(['like', 'client.name_pinyin', $q]);
            $query->orWhere(['like', 'client.nickname', $q]);
            $query->orWhere(['like', 'client.wechat_id', $q]);
            $query->orWhere(['like', 'client.id_card_number', $q]);
            $query->orWhere(['like', 'client.passport_number', $q]);

            // Exclude the family that we are searching from
            $query->andWhere(['<>', 'family.id', $exclude]);

        }

        return $dataProvider;
    }
}
