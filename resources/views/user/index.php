<?php 
    $this->layout("themes/architect-ui/_theme", [
        'title' => sprintf(_('Painel do Usuário | %s'), $appData['app_name']),
        'noMainInner' => true
    ]);
?>

<?php $this->start('css'); ?>
<style>
    .hero-caption {
        background: rgba(0, 0, 0, 0.99);
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center center;
        height: 300px;
    }

    .services-area {
        margin-top: -100px;
        width: calc(100vw - 20px);
    }

    .scrolling-wrapper {
        overflow-x: auto;
    }

    .card-icon {
        font-size: 120px;
    }

    .left-arrow-scroll {
        display: fixed;
        position: absolute;
        top: 50%;
        left: 4rem;
        transform: translate(-50%, -50%);
        z-index: 1;
        opacity: 0.5;
        cursor: pointer;
    }
    
    .right-arrow-scroll {
        display: fixed;
        position: absolute;
        top: 50%;
        left: calc(100% - 4rem);
        transform: translate(-50%, -50%);
        z-index: 1;
        opacity: 0.5;
        cursor: pointer;
    }
</style>
<?php $this->end(); ?>

<div class="text-center bg-image rounded-3 hero-caption d-flex align-items-center justify-content-around">
    <h1 class="text-white" style="opacity: 0.99;"><?= _('Painel Principal') ?></h1>
</div>

<?php if($cards): ?>
<div class="container-fluid py-2 services-area">
    <div class="scrolling-wrapper row flex-row flex-nowrap pb-4 pt-2">
        <div class="left-arrow-scroll">
            <i class="icofont-circled-left" style="font-size: 4rem;"></i>
        </div>
        <div class="right-arrow-scroll">
            <i class="icofont-circled-right" style="font-size: 4rem;"></i>
        </div>

        <?php foreach($cards as $index => $card): ?>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xl-3 col-7 mb-4">
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
</div>
<?php endif; ?>

<?php $this->start('scripts'); ?>
<script>
    $(function () {
        const left_arrow = $(".left-arrow-scroll");
        const right_arrow = $(".right-arrow-scroll");
        const scrolling_wrapper = $(".scrolling-wrapper");

        var speed = 10;
        var scroll = 6;
        var scrolling;

        left_arrow.mouseenter(function() {
            scrolling = window.setInterval(function() {
                scrolling_wrapper.scrollLeft(scrolling_wrapper.scrollLeft() - scroll);
            }, speed);        
        }).mouseleave(function(){
            window.clearInterval(scrolling);
        });

        right_arrow.mouseenter(function() {
            scrolling = window.setInterval(function() {
                scrolling_wrapper.scrollLeft(scrolling_wrapper.scrollLeft() + scroll);
            }, speed);
        }).mouseleave(function(){
            window.clearInterval(scrolling);
        });
    });
</script>
<?php $this->end(); ?>