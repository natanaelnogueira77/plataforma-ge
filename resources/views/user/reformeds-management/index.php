<?php 
    $this->layout("themes/architect-ui/_theme", [
        'title' => sprintf(_('Controle de Reformados | %s'), $appData['app_name'])
    ]);
?>

<?php 
    $this->insert('themes/architect-ui/components/title', [
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
            <?php $this->insert('components/data-table-filters', ['formId' => 'filters']); ?>
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

<?php $this->start('scripts'); ?>
<script>
    $(function () {
        const app = new App();
        const table = $("#reformeds-management");
        const filters_form = $("#filters");

        const turn_start_form = $("#turn-start");
        const turn_start_modal = $("#turn-start-modal");
        
        const turn_end_form = $("#turn-end");
        const turn_end_modal = $("#turn-end-modal");

        const turn_start_btn = $("#create-turn-start");
        const turn_end_btn = $("#create-turn-end");

        const export_excel_btn = $("#export-excel");
        const export_reformations_form = $("#export-reformations");
        const export_reformations_modal = $("#export-reformations-modal");

        const dataTable = app.table(table, table.data('action'));
        dataTable.defaultParams(app.objectifyForm(filters_form)).filtersForm(filters_form)
        .setMsgFunc((msg) => app.showMessage(msg.message, msg.type)).loadOnChange().load();

        turn_start_btn.click(function () {
            var data = $(this).data();

            turn_start_form.attr('action', data.action);
            turn_start_form.attr('method', data.method);

            app.cleanForm(turn_start_form);

            turn_start_modal.modal('show');
        });

        turn_end_btn.click(function () {
            var data = $(this).data();

            turn_end_form.attr('action', data.action);
            turn_end_form.attr('method', data.method);

            app.cleanForm(turn_end_form);

            turn_end_modal.modal('show');
        });

        export_excel_btn.click(function () {
            var data = $(this).data();

            export_reformations_form.attr('action', data.action);
            export_reformations_form.attr('method', data.method);
            export_reformations_modal.modal('show');
        });

        app.form(turn_start_form, function (response) {
            dataTable.load();
            turn_start_modal.modal('toggle');
        });
        
        app.form(turn_end_form, function (response) {
            dataTable.load();
            turn_end_modal.modal('toggle');
        });
    });
</script>
<?php $this->end(); ?>

<?php 
    $this->start('modals');
    $this->insert('user/reformeds-management/components/turn-start-modal', ['dbProducts' => $dbProducts]);
    $this->insert('user/reformeds-management/components/turn-end-modal', ['dbProducts' => $dbProducts]);
    $this->insert('user/reformeds-management/components/export-modal', ['dbProducts' => $dbProducts]);
    $this->end();
?>