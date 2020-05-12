<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProgramSearch represents the model behind the search form about `app\models\Program`.
 */
class ProgramSearch extends Program
{
    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['id',], 'integer'],
            [['start_date', 'end_date', 'remarks'], 'safe'],
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
    public function search($params): ActiveDataProvider
    {
        $query = Program::find()->joinWith(['programGroup']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        $pg = new ProgramGroup();
        $pg->load($params);

        // add conditions that should always apply here

        $query->andFilterWhere([
            'like',
            'program_group.location_id',
            $pg->location_id,
        ]);

        $query->andFilterWhere([
            'like',
            'program_group.name',
            $pg->name,
        ]);

        $query->andFilterWhere([
            'program_group.type_id' => $pg->type_id
        ]);

        $query->andFilterWhere([
            '>=',
            'start_date',
            $this->start_date
        ]);

        $query->andFilterWhere([
            '<=',
            'end_date',
            $this->end_date
        ]);

        return $dataProvider;
    }
}
