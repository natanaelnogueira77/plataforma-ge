<form id="save-product-output">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="pro_id">
                <?= _('Produto') ?>
                <span data-toggle="tooltip" data-placement="top" title="<?= _('Escolha o produto de saída.') ?>">
                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                </span>
            </label>
            <select name="pro_id" class="form-control">
                <option value=""><?= _('Selecionar...') ?></option>
                <?php 
                    if($dbProducts) {
                        foreach($dbProducts as $dbProduct) {
                            echo "<option value=\"{$dbProduct->id}\">{$dbProduct->desc_short}</option>";
                        }
                    }
                ?>
            </select>
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group col-md-6">
            <label for="col_id">
                <?= _('Colaborador') ?>
                <span data-toggle="tooltip" data-placement="top" 
                    title='<?= _('Selecione o colaborador, ou cadastre um novo, clicando em "Cadastrar".') ?>'>
                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                </span>
            </label>
            <div class="input-group">
                <select name="col_id" class="form-control">
                    <option value=""><?= _('Selecionar...') ?></option>
                    <?php 
                        if($dbCollaborators) {
                            foreach($dbCollaborators as $dbCollaborator) {
                                echo "<option value=\"{$dbCollaborator->id}\">{$dbCollaborator->name}</option>";
                            }
                        }
                    ?>
                </select>
                <div class="input-group-append">
                    <button id="create-collaborator" type="button" class="btn btn-primary" 
                        data-action="<?= $router->route('user.collaborators.store') ?>" 
                        data-method="post"><?= _('Cadastrar') ?></button>
                </div>
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="boxes">
                <?= _('Quantidade de Caixas') ?>
                <span data-toggle="tooltip" data-placement="top" title="<?= _('Determine o número de caixas de saída.') ?>">
                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                </span>
            </label>
            <input type="number" name="boxes" class="form-control" min="0" placeholder="<?= _('Digite o número de caixas...') ?>">
            <div class="invalid-feedback"></div>
        </div>
        
        <div class="form-group col-md-6">
            <label for="units">
                <?= _('Quantidade de Unidades') ?>
                <span data-toggle="tooltip" data-placement="top" title="<?= _('Determine o número de unidades de saída.') ?>">
                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                </span>
            </label>
            <input type="number" name="units" class="form-control" min="0" placeholder="<?= _('Digite o número de unidades...') ?>">
            <div class="invalid-feedback"></div>
        </div>
    </div>
</form>