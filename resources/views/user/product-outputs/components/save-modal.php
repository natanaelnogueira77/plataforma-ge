<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="save-product-output-modal" 
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div id="save-product-output-area" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= _('Dar Saída') ?></h5>
            </div>
            
            <div class="modal-body">
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
                <button type="button" id="save-collaborator-return" class="btn btn-danger btn-lg"><?= _('Voltar') ?></button>
                <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal"><?= _('Fechar') ?></button>
            </div>
        </div>
    </div>
</div>