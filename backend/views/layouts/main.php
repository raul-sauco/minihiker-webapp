<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\models\User;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use backend\assets\BackendAsset;

BackendAsset::register($this);
$user = Yii::$app->user;
$this->beginPage()
?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::t('app' , 'Mini Hiker'),
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
        'innerContainerOptions' => [
            'class' => 'container-fluid'
        ]
    ]);
    echo Nav::widget([
        'options' => [
            'class' => 'navbar-nav navbar-right',
        ],
        'encodeLabels' => false,
        'items' => [
            [
                'label' => Yii::t('app', 'Programs'),
                'url' => ['/program'],
                'visible' => $user->can('listPrograms')
            ],
            [
                'label' => Yii::t('app', 'Clients'),
                'url' => ['/client'],
                'visible' => $user->can('listClients')
            ],
            [
                'label' => Yii::t('app', 'Families'), 
                'url' => ['/family'],
                'visible' => $user->can('listFamilies')
            ],
            [
                'label' => Yii::t('app', 'Locations'), 
                'url' => ['/location'],
                'visible' => $user->can('listLocations')
            ],
            [
                'label' => Yii::t('app', 'Mini Program'),
                'url' => ['/weapp/index'],
                'visible' => $user->can('listPrograms')
            ],
            [
                'label' => Yii::t('app', 'Users'), 
                'url' => ['/user'],
                'visible' => $user->can('listUsers')
            ],
	        [
		        'label' => Yii::t('app', 'Contract'),
		        'url' => ['/contracts'],
		        //'visible' => $user->can('listContract')
	        ],
            Yii::$app->user->isGuest ? (
                ['label' => Yii::t('app', 'Login'), 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                		Yii::t('app', 'Logout ({username})', 
                				['username' => Yii::$app->user->identity->username]),
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            ),
            [
                'label' => Html::icon('question-sign'), 
                'url' => ['/site/man'],
                'visible' => $user->can('viewMan')
            ],
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container-fluid">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<?= $this->render('_footer') ?>

<div id="notification-window">
</div>

<?php

Modal::begin([
    'headerOptions' => ['id' => 'modalHeader'],
    'id' => 'appModal',
    'size' => 'modal-lg'
]);
echo Html::tag('div', '', ['id' => 'modalContent']);
Modal::end();

$this->registerJsVar(
    'apiurl',
    strpos(Url::base(true), 'localhost') ? 'http://localhost/mhapi/' :
        'https://apiv2.minihiker.com/'
);

$this->registerJsVar(
    'baseurl',
    Url::to('@web') . '/'
);

$this->registerJsVar(
        'loading20',
    Html::img('@web/img/loading_20.gif', [
        'alt' => 'loading...',
        'class' => 'loading-spinner loading-spinner-20'
    ])
);

$this->registerJsVar(
    'loadingOverlaySmall',
    Html::tag('div',
        Html::img('@web/img/loading_20.gif', [
            'alt' => 'loading...',
            'class' => 'loading-spinner loading-spinner-20'
        ]), [
            'class' => 'loading-overlay loading-overlay-small'
        ])
);

// Add the global Mh JS object
$this->render('jsvars');
?>

<?php

if (isset(Yii::$app->user)) {
    $userModel = User::findOne($user->id);
    if ($userModel !== null) {
        $this->registerJsVar('userAccessToken', $userModel->access_token);
    }
}

$this->endBody()
?>
</body>
</html>
<?php $this->endPage() ?>
