<?php 
    $theme->title = sprintf(_('Controle de Peças | %s'), $appData['app_name']);
    $this->layout("themes/architect-ui/_theme", ['theme' => $theme]);
    
    $this->insert('themes/architect-ui/_components/title', [
        'title' => _('Controle de Peças'),
        'subtitle' => _('Segue abaixo o controle de peças'),
        'icon' => 'pe-7s-tools',
        'icon_color' => 'bg-malibu-beach'
    ]);
?>

<div class="card shadow mb-4 br-15">
    <div class="card-header-tab card-header-tab-animation card-header brt-15">    
        <div class="card-header-title">
            <i class="header-icon icofont-tools icon-gradient bg-info"> </i>
            <?= _('Controle de Peças') ?>
        </div>
    </div>

    <div class="card-body">
        <div class="alert alert-info"><?= _('Estamos preparando...') ?></div>
    </div>
</div>