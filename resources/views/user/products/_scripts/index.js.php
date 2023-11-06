<script>
    $(function () {
        const app = new App();
        const table = $("#products");
        const filters_form = $("#filters");

        const save_product_form = $("#save-product");
        const save_product_modal = $("#save-product-modal");
        const create_product_btn = $("#create-product");

        const dataTable = app.table(table, table.data('action'));
        dataTable.defaultParams(app.objectifyForm(filters_form)).filtersForm(filters_form)
        .setMsgFunc((msg) => app.showMessage(msg.message, msg.type)).loadOnChange().addAction((table) => {
            table.find("[data-act=delete]").click(function () {
                var data = $(this).data();

                if(confirm(<?php echo json_encode(_('Deseja realmente excluir este produto?')) ?>)) {
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
                        save_product_form.attr('action', response.save.action);
                        save_product_form.attr('method', response.save.method);

                        app.cleanForm(save_product_form);

                        if(response.content) {
                            app.populateForm(save_product_form, response.content, 'name');
                        }

                        save_product_modal.modal('show');
                    }
                });
            });
        }).load();

        create_product_btn.click(function () {
            var data = $(this).data();

            save_product_form.attr('action', data.action);
            save_product_form.attr('method', data.method);

            app.cleanForm(save_product_form);

            save_product_modal.modal('show');
        });

        app.form(save_product_form, function (response) {
            dataTable.load();
            save_product_modal.modal('toggle');
        });
    });
</script>