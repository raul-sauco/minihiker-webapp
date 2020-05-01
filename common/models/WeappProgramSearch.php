<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * WeappProgramSearch represents the model behind the search form
 * of `app\models\ProgramGroup`. but focused on ProgramGroups that
 * are visible in the webapp
 */
class WeappProgramSearch extends ProgramGroup
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type_id', 'accompanied', 'weapp_visible', 'min_age', 'max_age',
                'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['name', 'location_id', 'weapp_cover_image', 'weapp_display_name', 'theme',
                'summary', 'keywords', 'weapp_description', 'price_description', 'refund_description'], 'safe'],
        ];
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
     * Creates data provider instance with search query applied.
     * Simulate the search results that clients get on the Mini program
     *
     * @param array $get
     *
     * @return ActiveDataProvider
     */
    public function search($get)
    {
        // Find all the weapp visible programs starting today of after.
        $query = ProgramGroup::find()->joinWith(['location', 'programs'])
            ->where(['weapp_visible' => true])->distinct();

        if (!empty($get['q'])) {

            $q = $get['q'];

            if (strpos($q, '单飞') !== false) {

                $query->andWhere(['accompanied' => false]);

            }

            if (strpos($q, '亲子') !== false) {

                $query->andWhere(['accompanied' => true]);

            }

            if (strpos($q, '国际') !== false) {

                $query->andWhere(['location.international' => true]);

            }

            if (strpos($q, '国内') !== false) {

                $query->andWhere(['location.international' => false]);

            }

            // $tokens = explode(',', $get['q']);
            $tokens = preg_split('/[\s\n,.，。]+/u', $q, -1, PREG_SPLIT_NO_EMPTY);

            $attrs = [
                'theme','summary','keywords','location_id','location.name_en'
            ];

            $searchQuery = ['or'];

            foreach ($tokens as $token) {

                $token = trim($token);

                if (!empty($token) && $token !== '单飞' && $token !== '亲子' && $token !== '国际' && $token !== '国内') {

                    foreach ($attrs as $attr) {

                        $searchQuery[] = ['like', $attr, $token];

                    }

                }

            }

            $query->andWhere($searchQuery);

        }


        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}
