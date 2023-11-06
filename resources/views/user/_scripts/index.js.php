<script>
    $(function () {
        $(".card").mouseover(function () {
            $(this).addClass("border border-primary");
        });

        $(".card").mouseleave(function () {
            $(this).removeClass("border border-primary");
        });
    });
</script>