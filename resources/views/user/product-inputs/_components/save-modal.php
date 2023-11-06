<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="save-product-input-modal" 
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= _('Dar Entrada') ?></h5>
            </div>
            
            <div class="modal-body">
                <?php 
                    $v->insert('user/product-inputs/_components/save-form', [
                        'dbProducts' => $dbProducts,
                        'states' => $states
                    ]);
                ?>
            </div>
            
            <div class="modal-footer d-block text-center">
                <input form="save-product-input" type="submit" class="btn btn-success btn-lg" value="<?= _('Dar Entrada') ?>">
                <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal">
                    <?= _('Voltar') ?>
                </button>
            </div>
        </div>
    </div>
</div>