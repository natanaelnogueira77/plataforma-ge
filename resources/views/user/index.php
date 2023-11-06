<?php 
    $theme->title = sprintf(_('Painel do Usuário | %s'), $appData['app_name']);
    $this->layout("themes/architect-ui/_theme", ['theme' => $theme]);
?>

<?php $this->start('css'); ?>
<style>
    .card-icon {
        font-size: 120px;
    }
</style>
<?php $this->end(); ?>

<?php 
    $this->insert('themes/architect-ui/_components/title', [
        'title' => _('Painel Principal'),
        'subtitle' => sprintf(_('Seja bem-vindo(a), %s!'), $user->name),
        'icon' => 'pe-7s-home',
        'icon_color' => 'bg-malibu-beach'
    ]);
?>

<?php if($cards): ?>
<div class="row">
    <?php foreach($cards as $index => $card): ?>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xl-4 mb-4 align-items-stretch">
        <div class="card card-block <?= $card['shadow_color'] ?> br-15">
            <div class="card-header d-flex justify-content-around brt-15">
                <h5 class="mb-0 text-center"><strong><?= $card['title'] ?></strong></h5>
            </div>
            
            <a href="<?= $card['items'][0]['url'] ?>" style="text-decoration: none;">
                <div class="d-flex justify-content-around <?= $card['bg_color'] ?>">
                    <div class="text-center">
                        <i class="<?= $card['icon'] ?> <?= $card['color'] ?> text-center py-4 card-icon"></i>
                    </div>
                </div>
            </a>

            <div class="card-body">
                <p class="card-text"><?= $card['text'] ?></p>
            </div>

            <?php if($card['items']): ?>
            <ul class="nav flex-column">
                <?php foreach($card['items'] as $item): ?>
                <li class="nav-item-divider nav-item my-0"></li>
                <li class="nav-item">
                    <a href="<?= $item['url'] ?>" class="nav-link">
                        <i class="nav-link-icon <?= $item['icon'] ?>"> </i><span><?= $item['text'] ?></span>
                        <div class="ml-auto">></div>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php 
    $this->start('scripts'); 
    $this->insert('user/_scripts/index.js');
    $this->end(); 
?>