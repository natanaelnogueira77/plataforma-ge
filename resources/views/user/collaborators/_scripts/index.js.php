<script>
    $(function () {
        const app = new App();
        const table = $("#collaborators");
        const filters_form = $("#filters");

        const save_collaborator_form = $("#save-collaborator");
        const save_collaborator_modal = $("#save-collaborator-modal");
        const create_collaborator_btn = $("#create-collaborator");

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
                        save_collaborator_form.attr('action', response.save.action);
                        save_collaborator_form.attr('method', response.save.method);

                        app.cleanForm(save_collaborator_form);

                        if(response.content) {
                            app.populateForm(save_collaborator_form, response.content, 'name');
                        }

                        save_collaborator_modal.modal('show');
                    }
                });
            });
        }).load();

        create_collaborator_btn.click(function () {
            var data = $(this).data();

            save_collaborator_form.attr('action', data.action);
            save_collaborator_form.attr('method', data.method);

            app.cleanForm(save_collaborator_form);

            save_collaborator_modal.modal('show');
        });

        app.form(save_collaborator_form, function (response) {
            dataTable.load();
            save_collaborator_modal.modal('toggle');
        });
    });
</script>