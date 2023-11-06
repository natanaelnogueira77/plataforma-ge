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