<?php 
    $theme->title = sprintf(_('Entradas | %s'), $appData['app_name']);
    $this->layout("themes/architect-ui/_theme", ['theme' => $theme]);

    $this->insert('themes/architect-ui/_components/title', [
        'title' => _('Lista de Entradas'),
        'subtitle' => _('Segue abaixo a lista de entradas de produtos do sistema'),
        'icon' => 'pe-7s-upload',
        'icon_color' => 'bg-malibu-beach'
    ]);
?>

<div class="card shadow mb-4 br-15">
    <div class="card-header-tab card-header-tab-animation card-header brt-15">    
        <div class="card-header-title">
            <i class="header-icon icofont-upload icon-gradient bg-info"> </i>
            <?= _('Entradas de Produtos') ?>
        </div>

        <div class="btn-actions-pane-right">
            <div role="group" class="btn-group-sm btn-group">
                <button type="button" id="create-product-input" class="btn btn-lg btn-primary" data-method="post" 
                    data-action="<?= $router->route('user.productInputs.store') ?>">
                    <?= _('Dar Entrada') ?>
                </button>

                <button type="button" id="export-excel" class="btn btn-outline-success btn-lg" 
                    data-action="<?= $router->route('user.productInputs.export') ?>" data-method="get">
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
                    <label><?= _('Status de Entrada') ?></label>
                    <select name="status" class="form-control">
                        <option value=""><?= _('Não recebido') ?></option>
                        <?php 
                            if($states) {
                                foreach($states as $sId => $status) {
                                    echo "<option value=\"{$sId}\">{$status}</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>
        </form>

        <div id="product-inputs" data-action="<?= $router->route('user.productInputs.list') ?>">
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
    $this->insert('user/product-inputs/_scripts/index.js');
    $this->end(); 

    $this->start('modals');
    $this->insert('user/product-inputs/_components/save-modal', [
        'v' => $this,
        'dbProducts' => $dbProducts,
        'states' => $states
    ]);
    $this->insert('user/product-inputs/_components/export-modal', [
        'dbProducts' => $dbProducts,
        'states' => $states
    ]);
    $this->end();
?>