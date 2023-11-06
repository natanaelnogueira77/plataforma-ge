<?php 
    $theme->title = sprintf(_('Controle de Estoque de Insumos | %s'), $appData['app_name']);
    $this->layout("themes/architect-ui/_theme", ['theme' => $theme]);

    $this->insert('themes/architect-ui/_components/title', [
        'title' => _('Controle de Estoque de Insumos'),
        'subtitle' => _('Segue abaixo a lista de estoques de produtos do sistema'),
        'icon' => 'pe-7s-server',
        'icon_color' => 'bg-malibu-beach'
    ]);
?>

<div class="card shadow mb-4 br-15">
    <div class="card-header-tab card-header-tab-animation card-header brt-15">    
        <div class="card-header-title">
            <i class="header-icon icofont-database icon-gradient bg-info"> </i>
            <?= _('Controle de Estoque de Insumos') ?>
        </div>

        <div class="btn-actions-pane-right">
            <div role="group" class="btn-group-sm btn-group">
                <button type="button" id="export-excel" class="btn btn-outline-success btn-lg" 
                    data-action="<?= $router->route('user.stocks.export') ?>" data-method="get">
                    <i class="icofont-file-excel"></i>
                    <?= _('Exportar Excel') ?>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form id="filters">
            <?php $this->insert('_components/data-table-filters', ['formId' => 'filters']); ?>
        </form>

        <div id="stocks" data-action="<?= $router->route('user.stocks.list') ?>">
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
    $this->insert('user/stocks/_sripts/index.js');
    $this->end(); 

    $this->start('modals');
    $this->insert('user/stocks/_components/export-modal', [
        'dbProducts' => $dbProducts, 
        'dbCollaborators' => $dbCollaborators
    ]);
    $this->end();
?>