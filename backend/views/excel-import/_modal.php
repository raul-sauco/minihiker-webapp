<?php ?>

<div id="v-modal" v-if="modal.visible">
    <div class="v-modal-mask"
         @click="dismissModal"></div>
    <div class="v-modal-dialog">
        <div class="v-modal-title" v-html="modal.title"></div>
        <div class="v-modal-content">
            <template v-if="modal.content === 'client-info'">
                <?= $this->render('_client-info-template') ?>
            </template>
            <template v-if="modal.content === 'client-not-found'">
                给定Excel工作表中的数据，我们找不到任何合适的客户
            </template>
            <template v-if="modal.content === 'help'">
                <?= $this->render('_modal-help-content') ?>
            </template>
            <template v-if="modal.content === 'program-info'">
                <div class="program-display-container"
                     v-for="program in modal.programs">
                    <?= $this->render('_program-info-template') ?>
                </div>
            </template>
            <template v-if="modal.content === 'program-not-found'">
                给定Excel工作表中的数据，我们找不到任何合适的项目
            </template>
            <template v-if="modal.content === 'row-not-ready-to-upload'">
                所选行{{ modal.row.index }}尚未准备好上载
            </template>
            <template v-if="modal.content === 'select-program'">
                <div class="program-select-container"
                     v-for="program in modal.programs">
                    <?= $this->render('_program-info-template') ?>
                    <button class="btn btn-success"
                            @click="selectProgram(modal.row, program)">
                        使用这个项目
                    </button>
                </div>
            </template>
            <template v-if="modal.content === 'upload-errors'">
                <div class="upload-error" v-for="error in modal.row.errors">
                    <div>{{ error }}</div>
                </div>
            </template>
            <template v-if="modal.content === 'work-in-progress'">
                工作正在进行中
            </template>
        </div>
    </div>
</div>
