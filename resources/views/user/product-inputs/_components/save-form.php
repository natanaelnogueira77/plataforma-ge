<form id="save-product-input">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="pro_id">
                <?= _('Produto') ?>
                <span data-toggle="tooltip" data-placement="top" title="<?= _('Escolha o produto de entrada.') ?>">
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
            <label for="c_status">
                <?= _('Observações') ?>
                <span data-toggle="tooltip" data-placement="top" title="<?= _('Escolha o status de entrada.') ?>">
                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                </span>
            </label>
            <select name="c_status" class="form-control">
                <option value=""><?= _('Selecionar...') ?></option>
                <?php 
                    if($states) {
                        foreach($states as $sId => $status) {
                            echo "<option value=\"{$sId}\">{$status}</option>";
                        }
                    }
                ?>
            </select>
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="boxes">
                <?= _('Quantidade de Caixas') ?>
                <span data-toggle="tooltip" data-placement="top" title="<?= _('Determine o número de caixas de entrada.') ?>">
                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                </span>
            </label>
            <input type="number" name="boxes" class="form-control" min="0" placeholder="<?= _('Digite o número de caixas...') ?>">
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group col-md-6">
            <label for="units">
                <?= _('Quantidade de Unidades') ?>
                <span data-toggle="tooltip" data-placement="top" title="<?= _('Determine o número de unidades de entrada.') ?>">
                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                </span>
            </label>
            <input type="number" name="units" class="form-control" min="0" placeholder="<?= _('Digite o número de unidades...') ?>" >
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div id="location-area" class="form-row" style="display: none;">
        <div class="form-group col-md-4">
            <label for="street">
                <?= _('Rua') ?>
                <span data-toggle="tooltip" data-placement="top" title="<?= _('Digite o número que representa a rua de entrada.') ?>">
                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                </span>
            </label>
            <input type="number" name="street" class="form-control" min="0" placeholder="<?= _('Digite a rua...') ?>">
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group col-md-4">
            <label for="position">
                <?= _('Posição') ?>
                <span data-toggle="tooltip" data-placement="top" title="<?= _('Digite o número que representa a posição de entrada.') ?>">
                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                </span>
            </label>
            <input type="number" name="position" class="form-control" min="0" placeholder="<?= _('Digite a posição...') ?>">
            <div class="invalid-feedback"></div>
        </div>
        
        <div class="form-group col-md-4">
            <label for="height">
                <?= _('Altura') ?>
                <span data-toggle="tooltip" data-placement="top" title="<?= _('Digite o número que representa a altura da entrada.') ?>">
                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                </span>
            </label>
            <input type="number" name="height" class="form-control" min="0" placeholder="<?= _('Digite a altura...') ?>">
            <div class="invalid-feedback"></div>
        </div>
    </div>
</form>