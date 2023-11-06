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