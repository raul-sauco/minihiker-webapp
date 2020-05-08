<?php

namespace apivp1\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * Class Program
 * @package apivp1\models
 *
 * @property null|ActiveQuery $participants
 */
class Program extends \common\models\Program
{

    /**
     * @return ActiveQuery
     */
    public function getPayments(): ActiveQuery
    {
        return $this->hasMany(Payment::class, ['program_id' => 'id']);
    }

    /**
     * If the user is authenticated, get a list of it's family members participating
     * in the current Program.
     *
     * @return ActiveQuery|null
     * @throws InvalidConfigException
     */
    public function getParticipants(): ?ActiveQuery
    {
        if (($client = Client::findOne(['user_id' => Yii::$app->user->id])) === null) {
            Yii::warning(
                'Tried to get participants for not client user ' .
                Yii::$app->user->id, __METHOD__);
            return null;
        }

        if ($client->family === null) {
            Yii::warning(
                'Tried to get participants for client user ' .
                $client->id . '. Could not find a family.', __METHOD__);
            return null;
        }

        return $this->getClients()->where(['family_id' => $client->family_id]);
    }

    /**
     * @return ActiveQuery
     */
    public function getProgramGroup(): ActiveQuery
    {
        return $this->hasOne(ProgramGroup::class, ['id' => 'program_group_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProgramPeriod(): ActiveQuery
    {
        return $this->hasOne(ProgramPeriod::class, ['id' => 'program_period_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProgramPrices(): ActiveQuery
    {
        return $this->hasMany(ProgramPrice::class, ['program_id' => 'id']);
    }

    /**
     * Remove fields that are not relevant to API consumers.
     *
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
     * Add some extra fields, provided on expand
     *
     * @return array
     */
    public function extraFields(): array
    {
        return [
            'registrations' => 'registrations',
            'period' => 'programPeriod',
            'prices' => 'programPrices',
            'programGroup',
            'participants'
        ];
    }
}
