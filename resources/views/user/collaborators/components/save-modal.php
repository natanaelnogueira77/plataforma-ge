<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="save-collaborator-modal" 
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= _('Salvar Colaborador') ?></h5>
            </div>
            
            <div class="modal-body">
                <form id="save-collaborator">
                    <div class="form-group">
                        <label for="name">
                            <?= _('Nome') ?>
                            <span data-toggle="tooltip" data-placement="top" title="<?= _('Digite o nome do colaborador.') ?>">
                                <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                            </span>
                        </label>
                        <input type="text" name="name" placeholder="<?= _('Informe um nome...') ?>" class="form-control" maxlength="100">
                        <div class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer d-block text-center">
                <input form="save-collaborator" type="submit" class="btn btn-success btn-lg" value="<?= _('Salvar') ?>">
                <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal">
                    <?= _('Voltar') ?>
                </button>
            </div>
        </div>
    </div>
</div>