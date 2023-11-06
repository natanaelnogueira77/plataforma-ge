<?php 
    $theme->title = sprintf(_('Controle de Reformados | %s'), $appData['app_name']);
    $this->layout("themes/architect-ui/_theme", ['theme' => $theme]);

    $this->insert('themes/architect-ui/_components/title', [
        'title' => _('Controle de Reformados'),
        'subtitle' => _('Segue abaixo o controle de reformados'),
        'icon' => 'pe-7s-tools',
        'icon_color' => 'bg-malibu-beach'
    ]);
?>

<div class="card shadow mb-4 br-15">
    <div class="card-header-tab card-header-tab-animation card-header brt-15">    
        <div class="card-header-title">
            <i class="header-icon icofont-tools icon-gradient bg-info"> </i>
            <?= _('Controle de Reformados') ?>
        </div>

        <div class="btn-actions-pane-right">
            <div role="group" class="btn-group-sm btn-group">
                <button type="button" id="create-turn-start" class="btn btn-lg btn-danger" data-method="post" 
                    data-action="<?= $router->route('user.reformedsManagement.turnStart') ?>">
                    <?= _('Início de Turno') ?>
                </button>
                
                <button type="button" id="create-turn-end" class="btn btn-lg btn-success" data-method="post" 
                    data-action="<?= $router->route('user.reformedsManagement.turnEnd') ?>">
                    <?= _('Fim de Turno') ?>
                </button>

                <button type="button" id="export-excel" class="btn btn-outline-success btn-lg" 
                    data-action="<?= $router->route('user.reformedsManagement.export') ?>" data-method="get">
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
                <div class="form-group col-md-4">
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

                <div class="form-group col-md-4">
                    <label for="r_date"><?= _('Data') ?></label>
                    <input type="date" name="r_date" class="form-control">
                </div>
            </div>
        </form>

        <div id="reformeds-management" data-action="<?= $router->route('user.reformedsManagement.list') ?>">
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
    $this->insert('user/reformeds-management/_scripts/index.js');
    $this->end(); 

    $this->start('modals');
    $this->insert('user/reformeds-management/_components/turn-start-modal', ['dbProducts' => $dbProducts]);
    $this->insert('user/reformeds-management/_components/turn-end-modal', ['dbProducts' => $dbProducts]);
    $this->insert('user/reformeds-management/_components/export-modal', ['dbProducts' => $dbProducts]);
    $this->end();
?>