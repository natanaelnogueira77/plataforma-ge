<?php 
    $theme->title = sprintf(_('Colaboradores | %s'), $appData['app_name']);
    $this->layout("themes/architect-ui/_theme", ['theme' => $theme]);

    $this->insert('themes/architect-ui/_components/title', [
        'title' => _('Lista de Colaboradores'),
        'subtitle' => _('Segue abaixo a lista de colaboradores do sistema'),
        'icon' => 'pe-7s-user',
        'icon_color' => 'bg-malibu-beach'
    ]);
?>

<div class="card shadow mb-4 br-15">
    <div class="card-header-tab card-header-tab-animation card-header brt-15">    
        <div class="card-header-title">
            <i class="header-icon icofont-user icon-gradient bg-info"> </i>
            <?= _('Colaboradores') ?>
        </div>

        <div class="btn-actions-pane-right">
            <div role="group" class="btn-group-sm btn-group">
                <button type="button" id="create-collaborator" class="btn btn-lg btn-primary" data-method="post" 
                    data-action="<?= $router->route('user.collaborators.store') ?>">
                    <?= _('Cadastrar Colaborador') ?>
                </button>

                <a href="<?= $router->route('user.collaborators.export') ?>" class="btn btn-outline-success btn-lg">
                    <i class="icofont-file-excel"></i>
                    <?= _('Exportar Excel') ?>
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form id="filters">
            <?php $this->insert('_components/data-table-filters', ['formId' => 'filters']); ?>
        </form>

        <div id="collaborators" data-action="<?= $router->route('user.collaborators.list') ?>">
            <div class="d-flex justify-content-around p-5">
                <div class="spinner-grow text-secondary" role="status">
                    <span class="visually-hidden"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    $this->start('scripts'); 
    $this->insert('user/collaborators/_components/index.js');
    $this->end(); 

    $this->start('modals');
    $this->insert('user/collaborators/_components/save-modal', ['v' => $this]);
    $this->end();
?>