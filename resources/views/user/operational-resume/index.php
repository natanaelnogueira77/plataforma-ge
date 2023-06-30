<?php 
    $this->layout("themes/architect-ui/_theme", [
        'title' => sprintf(_('Resumo Operacional | %s'), $appData['app_name'])
    ]);
?>

<?php 
    $this->insert('themes/architect-ui/components/title', [
        'title' => _('Resumo Operacional'),
        'subtitle' => _('Segue abaixo o resumo operacional'),
        'icon' => 'pe-7s-note2',
        'icon_color' => 'bg-malibu-beach'
    ]);
?>

<div class="card shadow mb-4 br-15">
    <div class="card-header-tab card-header-tab-animation card-header brt-15">    
        <div class="card-header-title">
            <i class="header-icon icofont-list icon-gradient bg-info"> </i>
            <?= _('Resumo Operacional') ?>
        </div>
    </div>

    <div class="card-body">
        
    </div>
</div>

<?php $this->start('scripts'); ?>
<script>
    $(function () {
        const app = new App();
    });
</script>
<?php $this->end(); ?>