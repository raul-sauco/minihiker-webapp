<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

?>
<footer class="footer">
    <nav class="footer-nav">
        <div class="footer-column">
            <ul class="list-unstyled">
                <li><?= Yii::t('app', 'Programs') ?></li>
                <li><?= Html::a(
                        Yii::t('app', 'List Programs'),
                        ['/program/index']) ?>
                </li>
                <li><?= Html::a(Yii::t('app', 'Create Program'),
                        ['/program/create']) ?>
                </li>
                <li><?= Html::a(Yii::t('app', 'Spreadsheet upload'),
                        ['/excel-import']) ?>
                </li>
                <li>
                    <a href="<?= Url::to(
                        '@staticUrl/templates/program-data-upload-template.xlsx',
                        true) ?>" download="上传数据模板.xlsx">
                        <?= Yii::t('app', 'Spreadsheet upload template') ?>
                    </a>
                </li>
            </ul>
        </div>
        <div class="footer-column">
            <ul class="list-unstyled">
                <li><?= Yii::t('app', 'Families') ?></li>
                <li><?= Html::a(
                        Yii::t('app', 'List Families'),
                        ['/family/index']) ?>
                </li>
                <li><?= Html::a(Yii::t('app', 'Create Family'),
                        ['/family/create']) ?>
                </li>
            </ul>
        </div>
        <div class="footer-column">
            <ul class="list-unstyled">
                <li><?= Yii::t('app', 'Locations') ?></li>
                <li><?= Html::a(
                        Yii::t('app', 'List Locations'),
                        ['/location/index']) ?>
                </li>
                <li><?= Html::a(Yii::t('app', 'Create Location'),
                        ['/location/create']) ?>
                </li>
            </ul>
        </div>
        <div class="footer-column">
            <ul class="list-unstyled">
                <li><?= Yii::t('app', 'Weapp Data') ?></li>
                <li><?= Html::a(
                        Yii::t('app', 'List Weapp Programs'),
                        ['/weapp/index']) ?>
                </li>
            </ul>
        </div>
        <div class="footer-column">
            <ul class="list-unstyled">
                <li><?= Yii::t('app', 'Weapp Pay') ?></li>
                <li><?= Html::a(
                        Yii::t('app', 'List Unified payment orders'),
                        ['/wx-unified-payment-order/index']) ?>
                </li>
            </ul>
        </div>
    </nav>
    <div class="copyright-notice">&copy;
        <?= Yii::t('app', 'Mini Hiker') ?>
        <?= date('Y') ?>
    </div>
</footer>
