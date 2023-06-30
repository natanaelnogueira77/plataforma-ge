<?php 
    $this->layout("themes/architect-ui/_theme", [
        'title' => sprintf(_('Controle de Estoque de Insumos | %s'), $appData['app_name'])
    ]);
?>

<?php 
    $this->insert('themes/architect-ui/components/title', [
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
            <?php $this->insert('components/data-table-filters', ['formId' => 'filters']); ?>
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

<?php $this->start('scripts'); ?>
<script>
    $(function () {
        const app = new App();
        const table = $("#stocks");
        const filters_form = $("#filters");

        const export_excel_btn = $("#export-excel");
        const export_stocks_form = $("#export-stocks");
        const export_stocks_modal = $("#export-stocks-modal");

        const dataTable = app.table(table, table.data('action'));
        dataTable.defaultParams(app.objectifyForm(filters_form)).filtersForm(filters_form)
        .setMsgFunc((msg) => app.showMessage(msg.message, msg.type)).loadOnChange().load();

        export_excel_btn.click(function () {
            var data = $(this).data();

            export_stocks_form.attr('action', data.action);
            export_stocks_form.attr('method', data.method);
            export_stocks_modal.modal('show');
        });
    });
</script>
<?php $this->end(); ?>

<?php 
    $this->start('modals');
    $this->insert('user/stocks/components/export-modal', [
        'dbProducts' => $dbProducts, 
        'dbCollaborators' => $dbCollaborators
    ]);
    $this->end();
?>