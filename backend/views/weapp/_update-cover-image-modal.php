<?php
/* @var $this yii\web\View */
?>
<div class="modal fade" id="cover-image-selection-modal"
     tabindex="-1" role="dialog" aria-labelledby="modalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalLabel">
                    <?= Yii::t('app', 'Select Cover Image') ?>
                </h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <?= Yii::t('app', 'Close') ?>
                </button>
            </div>
        </div>
    </div>
</div>
