<?php 
    $this->layout("themes/architect-ui/_theme", [
        'title' => sprintf(_('Produtividade do Dia | %s'), $appData['app_name'])
    ]);
?>

<?php 
    $this->insert('themes/architect-ui/components/title', [
        'title' => _('Produtividade do Dia'),
        'subtitle' => _('Segue abaixo a produtividade do dia'),
        'icon' => 'pe-7s-check',
        'icon_color' => 'bg-malibu-beach'
    ]);
?>

<div class="card shadow mb-4 br-15">
    <div class="card-header-tab card-header-tab-animation card-header brt-15">    
        <div class="card-header-title">
            <i class="header-icon icofont-check icon-gradient bg-info"> </i>
            <?= _('Produtividade do Dia') ?>
        </div>
    </div>

    <div class="card-body">
        <div class="alert alert-info"><?= _('Estamos preparando...') ?></div>
    </div>
</div>

<?php $this->start('scripts'); ?>
<script>
    $(function () {
        const app = new App();
    });
</script>
<?php $this->end(); ?>