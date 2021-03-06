<?php

use common\helpers\ClientHelper;
use common\models\Client;
use yii\bootstrap\Html;
use yii\web\View;

/* @var $this View */

$clientQuery = ClientHelper::getOrphanedClients();
$count = $clientQuery->count();
?>
<div id="orphaned-clients">
    <?php
    if ($count > 0) {
        $button = Html::a('Fix it',
            ['test/fix-orphaned-clients'],
            ['class' => 'btn btn-small btn-primary']
        );
        echo Html::tag('h4', "$count Orphaned clients " . $button);
        /** @var Client $client */
        foreach ($clientQuery->each() as $client) {
            $link = Html::a("Client $client->id (" . $client->getName() . ')',
                ['client/view', 'id' => $client->id]);
            echo Html::beginTag('div', ['class' => 'orphaned-client']);
            echo Html::tag('div', "Orphaned $link");
            if ($client->user_id !== null) {
                $userLink = Html::a("User $client->user_id (" . $client->user->username . ')',
                    ['user/view', 'id' => $client->user_id]);
                echo Html::tag('div', "User $userLink");
            }
            $possibleFamiliesQuery = ClientHelper::findOrphanedClientPossibleFamilies($client);
            foreach ($possibleFamiliesQuery->each() as $possibleFamilyMatch) {
                echo $this->render('_possible-family-match',
                    ['family' => $possibleFamilyMatch]);
            }
            echo $this->render('/layouts/_createInfo', ['model' => $client]);
            echo Html::endTag('div');
        }
    } else {
        echo Html::tag('h4', 'No orphaned Client records found');
    }
    ?>
</div>
