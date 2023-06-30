<?php 
    $this->layout("themes/architect-ui/_theme", [
        'title' => sprintf(_('Entradas | %s'), $appData['app_name'])
    ]);
?>

<?php 
    $this->insert('themes/architect-ui/components/title', [
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
            <?php $this->insert('components/data-table-filters', ['formId' => 'filters']); ?>
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

<?php $this->start('scripts'); ?>
<script>
    $(function () {
        const app = new App();
        const table = $("#product-inputs");
        const filters_form = $("#filters");

        const save_product_input_form = $("#save-product-input");
        const save_product_input_modal = $("#save-product-input-modal");

        const location_area = $("#location-area");
        const status_select = save_product_input_form.find("[name=c_status]");

        const create_product_input_btn = $("#create-product-input");
        
        const export_excel_btn = $("#export-excel");
        const export_product_inputs_form = $("#export-product-inputs");
        const export_product_inputs_modal = $("#export-product-inputs-modal");

        const dataTable = app.table(table, table.data('action'));
        dataTable.defaultParams(app.objectifyForm(filters_form)).filtersForm(filters_form)
        .setMsgFunc((msg) => app.showMessage(msg.message, msg.type)).loadOnChange().addAction((table) => {
            table.find("[data-act=delete]").click(function () {
                var data = $(this).data();

                if(confirm(<?php echo json_encode(_('Deseja realmente excluir esta entrada de produto?')) ?>)) {
                    app.callAjax({
                        url: data.action,
                        type: data.method,
                        success: function (response) {
                            dataTable.load();
                        }
                    });
                }
            });
        }).addAction((table) => {
            table.find("[data-act=edit]").click(function () {
                var data = $(this).data();

                app.callAjax({
                    url: data.action,
                    type: data.method,
                    success: function (response) {
                        save_product_input_form.attr('action', response.save.action);
                        save_product_input_form.attr('method', response.save.method);

                        save_product_input_form.find("[name=pro_id]").attr('readonly', true);
                        app.cleanForm(save_product_input_form);

                        location_area.hide('fast');
                        if(response.content) {
                            app.populateForm(save_product_input_form, response.content, 'name');
                            if(status_select.val() == 1) {
                                location_area.show('fast');
                            }
                        }

                        save_product_input_modal.modal('show');
                    }
                });
            });
        }).load();

        create_product_input_btn.click(function () {
            var data = $(this).data();

            save_product_input_form.attr('action', data.action);
            save_product_input_form.attr('method', data.method);

            save_product_input_form.find("[name=pro_id]").attr('readonly', false);

            location_area.hide('fast');
            app.cleanForm(save_product_input_form);

            save_product_input_modal.modal('show');
        });

        export_excel_btn.click(function () {
            var data = $(this).data();

            export_product_inputs_form.attr('action', data.action);
            export_product_inputs_form.attr('method', data.method);
            export_product_inputs_modal.modal('show');
        });

        status_select.change(function () {
            if($(this).val() == 1) {
                location_area.show('fast');
            } else {
                location_area.hide('fast');
            }
        });

        app.form(save_product_input_form, function (response) {
            dataTable.load();
            save_product_input_modal.modal('toggle');
        });
    });
</script>
<?php $this->end(); ?>

<?php 
    $this->start('modals');
    $this->insert('user/product-inputs/components/save-modal', [
        'dbProducts' => $dbProducts,
        'states' => $states
    ]);
    $this->insert('user/product-inputs/components/export-modal', [
        'dbProducts' => $dbProducts,
        'states' => $states
    ]);
    $this->end();
?>