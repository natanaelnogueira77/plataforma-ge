<?php 
    $theme->title = sprintf(_('Saídas | %s'), $appData['app_name']);
    $this->layout("themes/architect-ui/_theme", ['theme' => $theme]);

    $this->insert('themes/architect-ui/_components/title', [
        'title' => _('Lista de Saídas'),
        'subtitle' => _('Segue abaixo a lista de saídas de produtos do sistema'),
        'icon' => 'pe-7s-next-2',
        'icon_color' => 'bg-malibu-beach'
    ]);
?>

<div class="card shadow mb-4 br-15">
    <div class="card-header-tab card-header-tab-animation card-header brt-15">    
        <div class="card-header-title">
            <i class="header-icon icofont-sign-out icon-gradient bg-info"> </i>
            <?= _('Saídas de Produtos') ?>
        </div>

        <div class="btn-actions-pane-right">
            <div role="group" class="btn-group-sm btn-group">
                <button type="button" id="create-product-output" class="btn btn-lg btn-primary" data-method="post" 
                    data-action="<?= $router->route('user.productOutputs.store') ?>">
                    <?= _('Dar Saída') ?>
                </button>

                <button type="button" id="export-excel" class="btn btn-outline-success btn-lg" 
                    data-action="<?= $router->route('user.productOutputs.export') ?>" data-method="get">
                    <i class="icofont-file-excel"></i>
                    <?= _('Exportar Excel') ?>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form id="filters">
            <?php $this->insert('_components/data-table-filters', ['formId' => 'filters']); ?>
            <div class="form-row"> 
                <div class="form-group col-md-4 col-sm-6">
                    <label><?= _('Produto') ?></label>
                    <select name="product_id" class="form-control">
                        <option value=""><?= _('Todos os Produtos') ?></option>
                        <?php 
                            if($dbProducts) {
                                foreach($dbProducts as $dbProduct) {
                                    echo "<option value=\"{$dbProduct->id}\">{$dbProduct->desc_short}</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
                
                <div class="form-group col-md-4 col-sm-6">
                    <label><?= _('Colaborador') ?></label>
                    <select name="collaborator_id" class="form-control">
                        <option value=""><?= _('Todos os Colaboradores') ?></option>
                        <?php 
                            if($dbCollaborators) {
                                foreach($dbCollaborators as $dbCollaborator) {
                                    echo "<option value=\"{$dbCollaborator->id}\">{$dbCollaborator->name}</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>
        </form>

        <div id="product-outputs" data-action="<?= $router->route('user.productOutputs.list') ?>">
            <div class="d-flex justify-content-around p-5">
                <div class="spinner-grow text-secondary" role="status">
                    <span class="visually-hidden"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    $this->start('scripts'); 
    $this->insert('user/products-outputs/_scripts/index.js');
    $this->end(); 

    $this->start('modals');
    $this->insert('user/product-outputs/_components/save-modal', [
        'v' => $this,
        'dbProducts' => $dbProducts, 
        'dbCollaborators' => $dbCollaborators
    ]);
    $this->insert('user/product-outputs/_components/export-modal', [
        'dbProducts' => $dbProducts, 
        'dbCollaborators' => $dbCollaborators
    ]);
    $this->end();
?>