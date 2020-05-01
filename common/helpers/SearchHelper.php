<?php
namespace common\helpers;

use common\models\Program;
use common\models\ProgramGroup;
use common\models\SearchResult;
use Yii;
use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\helpers\BaseInflector;
use yii\helpers\ArrayHelper;

/**
 * Search helper class.
 * 
 * @author i
 *
 */
class SearchHelper
{
    /** @var $results array Stores all the results. */
    private $results = [];
    private $query;
    private $wrapClass = 'search-query-string';
    
    public function search ($query = null)
    {
        if ($query == null) {
            
            return null;
            
        } else {
            
            $this->query = $query;
            
            $this->searchFamilies();
            
            $this->searchClients();
            
            $this->searchPrograms();
            
            $this->searchLocations();
            
            $this->orderResults();
            
            return $this->results;
            
        }
    }
    
    /**
     * Find all the families that match the query 
     * and add them to the result set.
     */
    protected function searchFamilies()
    {
        $table = 'Family';
        
        $attributes = [
            'name',
            'serial_number',
            'category',
            'address',
            'place_of_residence',
            'remarks'
        ];
        
        $this->getResults($table, $attributes);
    }
    
    /**
     * Find all the clients that match the query
     * and add them to the result set.
     */
    protected function searchClients()
    {
        $table = 'Client';
        
        $attributes = [
            'name_zh',
            'nickname',
            'name_pinyin',
            'name_en',
            'phone_number',
            'phone_number_2',
            'email',
            'wechat_id',
            'id_card_number',
            'remarks'
        ];
        
        $this->getResults($table, $attributes);
    }
    
    /**
     * Find all the programs that match the query
     * and add them to the result set.
     */
    protected function searchPrograms()
    {
        $models = [];

        // Find programs by name and location
        $programGroups = ProgramGroup::find()
            ->where(['like', 'name', $this->query])
            ->orWhere(['like', 'location_id', $this->query])
            ->all();

        /* @var $programGroup ProgramGroup */
        if (!empty($programGroups)) {

            foreach ($programGroups as $programGroup) {

                $programs = $programGroup->getPrograms()->all();

                if (!empty($programs)) {

                    foreach ($programs as $program) {

                        $models[$program->id] = $program;

                    }

                }

            }

        }

        // Find programs by remarks
        $programs = Program::find()
            ->where(['like', 'remarks', $this->query])->all();

        if (!empty($programs)) {

            foreach ($programs as $program) {

                $models[$program->id] = $program;

            }

        }

        if (!empty($models)) {

            foreach ($models as $model) {

                // Add programs to result set
                $this->addProgramToResults($model);

            }

        }
    }
    
    /** 
     * Find all the locations that match the query
     * and add them to the result set.
     */
    protected function searchLocations()
    {
        $table = 'Location';
        
        $attributes = [
            'name_zh',
            'name_en',
        ];
        
        $this->getResults($table, $attributes);
    }
    
    /**
     * Query the database to get results for the current user query.
     * 
     * @param string $table The name of the class to query for.
     * @param array[string] $attributes The attributes to search in.
     */
    protected function getResults ($table, $attributes) 
    {
        Yii::trace("Searching table $table with query $this->query", 
            __METHOD__);
        
        $tableName = 'common\\models\\' . $table;
        
        $activeQuery = $tableName::find();
        
        foreach ($attributes as $attr) {
            
            $activeQuery->orWhere(['like', $attr, $this->query]);
            
        }
        
        $models = $activeQuery->orderBy('updated_at DESC')->all();
        
        if (!empty($models)) {
            
            foreach ($models as $model) {
                
                $this->addToResults($table, $model, $attributes);
                
            }
            
        }
    }
    
    /**
     * Adds a model's data to the current result set.
     * 
     * @param string $table The name of the Class.
     * @param yii\db\ActiveRecord $model the ActiveRecord instance.
     * @param array $attributes The attributes to display.
     */
    protected function addToResults ($table, $model, $attributes)
    {
        $result = new SearchResult();
        
        $result->query = $this->query;
    
        $result->model = Yii::t('app', $table);
        
        $result->header = $this->getHeader($model, $table, $model->getAttribute($attributes[0]));
        
        $result->subHeader = $this->getSubHeader($model);
        
        $result->link = $this->getLink($model, $table);
        
        $result->body = $this->getBody($model, $attributes);
        
        $result->searchRank = $this->getRank($result, $table);
        
        $this->results[] = $result;        
    }

    /**
     * Add a program's data to the current result set.
     *
     * @param Program $program The model to get the data from.
     */
    protected function addProgramToResults ($program)
    {
        $result = new SearchResult();

        $result->query = $this->query;

        $result->model = Yii::t('app', 'Program');

        $result->header = $this->wrapQuery($program->getNamei18n());

        $result->subHeader = $this->getSubHeader($program);

        $result->link = $this->getLink($program, 'Program');

        $result->body = $this->getProgramBody($program);

        $result->searchRank = $this->getRank($result, 'Program');

        $this->results[] = $result;
    }
    
    /**
     * Return the search rank of the result.
     * 
     * @param SearchResult $result
     * @param string $table The name of the class.
     * @return number The result's rank.
     */
    protected function getRank($result, $table)
    {
        $rank = 0;
        
        // Assign points for type
        if ($table == 'Location') {
            $rank += 5;
        } elseif ($table == 'Program') {
            $rank += 5;
        } elseif ($table == 'Client') {
            $rank += 5;
        } elseif ($table == 'Family') {
            $rank += 5;
        }
        
        $pattern = '~' . $this->query . '~i';
        
        $headerMatches = preg_match_all($pattern, $result->header);
        
        $rank += $headerMatches * 3;    // Three points for header matches
        
        $bodyMatches = preg_match_all($pattern, $result->body);
        
        $rank += $bodyMatches;
        
        return $rank;
    }
    
    /**
     * Order the search results based on their rank.
     */
    protected function orderResults()
    {
        return ArrayHelper::multisort($this->results, 'searchRank', SORT_DESC);
    }
    
    /**
     * Generate the result header field.
     * 
     * @param yii\db\ActiveRecord $model The ActiveRecord instance.
     * @param string $table The name of the class.
     * @param array/string $attribute The attribute to look into.
     * @return string The value of the result's header field.
     */
    protected function getHeader ($model, $table, $attribute)
    {
        if ($table == 'Program') {
            
            // Return getName() instead of the attribute
            return $this->wrapQuery($model->getNamei18n());
            
        } else {
            
            if (!empty($attribute)) {
                
                return $this->wrapQuery($attribute);
                
            } else {
                
                return Yii::t('app', $table) . ' ' .
                    ($table == 'Location' ? $model->name_zh : $model->id);
                    
            }
            
        }
    }
    
    /**
     * Generate the sub-header field.
     * 
     * @param yii\db\ActiveRecord $model The ActiveRecord instance.
     * @return string result->subHeader field.
     */
    protected function getSubHeader ($model)
    {        
        $createdAt = Yii::$app->formatter->asDatetime($model->created_at);
        
        $updatedAt = Yii::$app->formatter->asDatetime($model->updated_at);
        
        $subHeader = Yii::t('app', 'Created At') . ' ' . $createdAt . '. ';
        
        if ($createdAt != $updatedAt) {
            
            $subHeader .= Yii::t('app', 'Updated At')  . ' ' . $updatedAt . '. ';
            
        }
        
        return $subHeader;
        
    }
    
    /**
     * Generate the result link field.
     * 
     * @param yii\db\ActiveRecord $model The ActiveRecord instance.
     * @param string $table The name of the class.
     * @return string result->link field.
     */
    protected function getLink ($model, $table)
    {
        
        $route = BaseInflector::camel2id($table) . '/view';
        
        $link = Url::to([$route , 'id' => $table == 'Location' ? $model->name_zh : $model->id]);
        
        return $link;
        
    }
    
    /**
     * Generate the body field.
     * 
     * @param yii\db\ActiveRecord $model The ActiveRecord instance.
     * @param array $attributes The attributes to look into.
     * @return string result->body field.
     */
    protected function getBody ($model, $attributes)
    {
        $body = '';        
        
        foreach ($attributes as $attr) {
            
            if (!empty($model->getAttribute($attr))) {
                
                $body .= $this->addAttributeTags($model, $attr);
                
            }
        }
        
        return $body;
        
    }

    /**
     * Generate the SearchResult body tag.
     *
     * @param $program The model to get data from
     * @return string The HTML to be displayed on the SearchResult body
     */
    protected function getProgramBody ($program)
    {
        $body = '';

        if (!empty($program->programGroup->name)) {

            $label = Html::tag('span',
                Yii::t('app', 'Program Name') . ': ',
                ['class' => 'attribute-label']);

            $value = Html::tag('span',
                $this->wrapQuery($program->programGroup->name) . '. ',
                ['class' => 'attribute-value']);

            $body .= Html::tag('span',
                $label . $value,
                ['class' => 'search-result-name']);

        }

        if (!empty($program->programGroup->location_id)) {

            $label = Html::tag('span',
                Yii::t('app', 'Location') . ': ',
                ['class' => 'attribute-label']);

            $value = Html::tag('span',
                $this->wrapQuery($program->programGroup->location_id) . '. ',
                ['class' => 'attribute-value']);

            $body .= Html::tag('span',
                $label . $value,
                ['class' => 'search-result-location']);

        }

        if (!empty($program->programGroup->type_id)) {

            $label = Html::tag('span',
                Yii::t('app', 'Type') . ': ',
                ['class' => 'attribute-label']);

            $value = Html::tag('span',
                $this->wrapQuery($program->programGroup->type->name) . '. ',
                ['class' => 'attribute-value']);

            $body .= Html::tag('span',
                $label . $value,
                ['class' => 'search-result-type']);

        }

        if (!empty($program->start_date)) {

            $label = Html::tag('span',
                Yii::t('app', 'Start Date') . ': ',
                ['class' => 'attribute-label']);

            $value = Html::tag('span',
                $this->wrapQuery(
                    Yii::$app->formatter->asDate($program->start_date)
                ) . '. ',
                ['class' => 'attribute-value']);

            $body .= Html::tag('span',
                $label . $value,
                ['class' => 'search-result-start-date']);

        }

        if (!empty($program->end_date)) {

            $label = Html::tag('span',
                Yii::t('app', 'End Date') . ': ',
                ['class' => 'attribute-label']);

            $value = Html::tag('span',
                $this->wrapQuery(
                    Yii::$app->formatter->asDate($program->end_date)
                ) . '. ',
                ['class' => 'attribute-value']);

            $body .= Html::tag('span',
                $label . $value,
                ['class' => 'search-result-end-date']);

        }

        return $body;
    }
    
    /**
     * Adds the html tags to an attribute.
     * 
     * @param yii\db\ActiveRecord $model The ActiveRecord instance.
     * @param array/string $attr The attribute to look into.
     * @return string
     */
    protected function addAttributeTags ($model, $attr) 
    {            
        $html = Html::beginTag('span', ['class' => "search-result-$attr"]);
        
        $html .= Html::tag('span',
            $model->getAttributeLabel($attr) . ': ',
            ['class' => 'attribute-label']);
        
        $html .= Html::tag('span',
            $this->wrapQuery($model->getAttribute($attr)) . '. ',
            ['class' => 'attribute-value']);
        
        $html .= Html::endTag('span');
        
        return $html;        
    }
    
    /**
     * Wraps the query string on <span class="class">...</span> tags.
     */
    protected function wrapQuery ($subject, $class = null)
    {
        $class = $class == null ? $this->wrapClass : $class;
        
        $pattern = '~' . preg_quote($this->query, '~') . '~i';
        
        $replacement = '<span class="' . $class . '">\\0</span>';
        
        return preg_replace($pattern, $replacement, $subject);
    }
}

