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