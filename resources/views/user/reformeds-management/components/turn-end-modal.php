<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="turn-end-modal" 
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= _('Fim de Turno') ?></h5>
            </div>
            
            <div class="modal-body">
                <form id="turn-end">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="pro_id">
                                <?= _('Produto') ?>
                                <span data-toggle="tooltip" data-placement="top" title="<?= _('Escolha o produto.') ?>">
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
                            <label for="r_date">
                                <?= _('Data') ?>
                                <span data-toggle="tooltip" data-placement="top" title="<?= _('Determine a data de entrada.') ?>">
                                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                                </span>
                            </label>
                            <input type="date" name="r_date" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="turn">
                                <?= _('Turno') ?>
                                <span data-toggle="tooltip" data-placement="top" 
                                    title="<?= _('Digite o número do turno dessa inserção.') ?>">
                                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                                </span>
                            </label>
                            <input type="number" name="turn" class="form-control" min="0" placeholder="<?= _('Digite o número do turno...') ?>">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="amount_end">
                                <?= _('Quantidade de Fim de Turno') ?>
                                <span data-toggle="tooltip" data-placement="top" 
                                    title="<?= _('Determine o número de consertados no fim do turno.') ?>">
                                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                                </span>
                            </label>
                            <input type="number" name="amount_end" class="form-control" min="0" 
                                placeholder="<?= _('Digite o número de consertados...') ?>">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer d-block text-center">
                <input form="turn-end" type="submit" class="btn btn-success btn-lg" value="<?= _('Salvar') ?>">
                <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal">
                    <?= _('Voltar') ?>
                </button>
            </div>
        </div>
    </div>
</div>