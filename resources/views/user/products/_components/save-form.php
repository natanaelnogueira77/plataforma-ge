<form id="save-product">
    <div class="form-group">
        <label for="desc_short">
            <?= _('Descrição') ?>
            <span data-toggle="tooltip" data-placement="top" title="<?= _('Digite o nome ou uma descrição do produto.') ?>">
                <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
            </span>
        </label>
        <input type="text" name="desc_short" placeholder="<?= _('Informe uma descrição...') ?>"
            class="form-control" maxlength="300">
        <div class="invalid-feedback"></div>
    </div>
</form>