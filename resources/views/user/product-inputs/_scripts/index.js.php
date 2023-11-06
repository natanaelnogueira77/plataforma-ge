<?php use Src\Models\ProductInput; ?>
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
                            if(status_select.val() == <?php echo json_encode(ProductInput::CS_RECEIVED) ?>) {
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
            if($(this).val() == <?php echo json_encode(ProductInput::CS_RECEIVED) ?>) {
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