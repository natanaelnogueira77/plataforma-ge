<?php 
    $this->layout("themes/architect-ui/_theme", [
        'title' => sprintf(_('Saídas | %s'), $appData['app_name'])
    ]);
?>

<?php 
    $this->insert('themes/architect-ui/components/title', [
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

<?php $this->start('scripts'); ?>
<script>
    $(function () {
        const app = new App();
        const table = $("#product-outputs");
        const filters_form = $("#filters");

        const save_product_output_form = $("#save-product-output");
        const save_product_output_area = $("#save-product-output-area");
        const save_product_output_modal = $("#save-product-output-modal");
        const create_product_output_btn = $("#create-product-output");

        const save_collaborator_form = $("#save-collaborator");
        const save_collaborator_area = $("#save-collaborator-area");
        const save_collaborator_return_btn = $("#save-collaborator-return");
        const create_collaborator_btn = $("#create-collaborator");

        const export_excel_btn = $("#export-excel");
        const export_product_outputs_form = $("#export-product-outputs");
        const export_product_outputs_modal = $("#export-product-outputs-modal");

        const dataTable = app.table(table, table.data('action'));
        dataTable.defaultParams(app.objectifyForm(filters_form)).filtersForm(filters_form)
        .setMsgFunc((msg) => app.showMessage(msg.message, msg.type)).loadOnChange().addAction((table) => {
            table.find("[data-act=delete]").click(function () {
                var data = $(this).data();

                if(confirm(<?php echo json_encode(_('Deseja realmente excluir esta saída de produto?')) ?>)) {
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
                        save_product_output_form.attr('action', response.save.action);
                        save_product_output_form.attr('method', response.save.method);

                        save_product_output_form.find("[name=pro_id]").attr('readonly', true);
                        app.cleanForm(save_product_output_form);

                        if(response.content) {
                            app.populateForm(save_product_output_form, response.content, 'name');
                        }

                        save_product_output_modal.modal('show');
                    }
                });
            });
        }).load();

        create_product_output_btn.click(function () {
            var data = $(this).data();

            save_product_output_form.attr('action', data.action);
            save_product_output_form.attr('method', data.method);

            save_product_output_form.find("[name=pro_id]").attr('readonly', false);
            app.cleanForm(save_product_output_form);

            save_collaborator_area.hide();
            save_product_output_area.show();
            save_product_output_modal.modal('show');
        });

        create_collaborator_btn.click(function () {
            var data = $(this).data();

            save_collaborator_form.attr('action', data.action);
            save_collaborator_form.attr('method', data.method);

            app.cleanForm(save_collaborator_form);

            save_product_output_area.hide();
            save_collaborator_area.show();
        });

        save_collaborator_return_btn.click(function () {
            save_collaborator_area.hide();
            save_product_output_area.show();
        });

        export_excel_btn.click(function () {
            var data = $(this).data();

            export_product_outputs_form.attr('action', data.action);
            export_product_outputs_form.attr('method', data.method);
            export_product_outputs_modal.modal('show');
        });

        app.form(save_collaborator_form, function (response) {
            if(response.content) {
                save_product_output_form.find("[name=col_id]").append(`
                    <option value="${response.content.id}">${response.content.name}</option>
                `);
                save_product_output_form.find("[name=col_id]").val(response.content.id);
            }
            save_collaborator_area.hide();
            save_product_output_area.show();
        });

        app.form(save_product_output_form, function (response) {
            dataTable.load();
            save_product_output_modal.modal('toggle');
        });
    });
</script>
<?php $this->end(); ?>

<?php 
    $this->start('modals');
    $this->insert('user/product-outputs/components/save-modal', [
        'dbProducts' => $dbProducts, 
        'dbCollaborators' => $dbCollaborators
    ]);
    $this->insert('user/product-outputs/components/export-modal', [
        'dbProducts' => $dbProducts, 
        'dbCollaborators' => $dbCollaborators
    ]);
    $this->end();
?>