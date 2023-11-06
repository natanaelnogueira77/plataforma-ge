<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="save-product-output-modal" 
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div id="save-product-output-area" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= _('Dar Saída') ?></h5>
            </div>
            
            <div class="modal-body">
                <?php 
                    $v->insert('user/product-outputs/_components/save-form', [
                        'dbProducts' => $dbProducts,
                        'dbCollaborators' => $dbCollaborators
                    ]);
                ?>
            </div>
            
            <div class="modal-footer d-block text-center">
                <input form="save-product-output" type="submit" class="btn btn-success btn-lg" value="<?= _('Dar Saída') ?>">
                <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal">
                    <?= _('Voltar') ?>
                </button>
            </div>
        </div>

        <div id="save-collaborator-area" class="modal-content" style="display: none;">
            <div class="modal-header">
                <h5 class="modal-title"><?= _('Salvar Colaborador') ?></h5>
            </div>
            
            <div class="modal-body">
                <?php $v->insert('user/collaborators/_components/save-form'); ?>
            </div>
            
            <div class="modal-footer d-block text-center">
                <input form="save-collaborator" type="submit" class="btn btn-success btn-lg" value="<?= _('Salvar') ?>">
                <button type="button" id="save-collaborator-return" class="btn btn-danger btn-lg"><?= _('Voltar') ?></button>
                <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal"><?= _('Fechar') ?></button>
            </div>
        </div>
    </div>
</div>