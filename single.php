<?php get_header(); ?>

<div class="wrapper" id="single-wrapper">
    <div class="container" id="content" tabindex="-1">
        <div class="container">
            <div class="row">
                <div class="col-md-7 col-sm-12 content-area offset-md-1" id="primary">
                    <main class="site-main" id="main">
                        <?php get_template_part( 'loop-templates/content-single' ); ?>
                    </main>
                   
                </div>
                <div class="col-md-3 col-sm-12 widget-area" role="complementary" id="right-sidebar">
                    <div class="container">
                       <?php if ( is_active_sidebar( 'right-sidebar' ) ) : ?>
                        <?php dynamic_sidebar( 'right-sidebar' ); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ( ! ( function_exists( 'u_seisbarra8_is_amp' ) && u_seisbarra8_is_amp() ) ) : ?>
<script>
(function(){
    if (!document.body.classList.contains('single-post')) return;
    var root = document.getElementById('single-wrapper');
    if (!root) return;
    var decrease = document.getElementById('font-decrease');
    var increase = document.getElementById('font-increase');
    var resetBtn = document.getElementById('font-reset');
    var STORAGE_KEY = 'u68_post_scale';
    var scale = 1;
    try {
        var saved = localStorage.getItem(STORAGE_KEY);
        if (saved) { scale = Math.min(1.5, Math.max(0.85, parseFloat(saved))); }
    } catch(e) {}
    function applyScale(){
        root.style.setProperty('--u68-post-scale', String(scale));
    }
    applyScale();
    function change(delta){
        scale = Math.round((scale + delta) * 10) / 10;
        if (scale < 0.85) scale = 0.85;
        if (scale > 1.5) scale = 1.5;
        applyScale();
        try { localStorage.setItem(STORAGE_KEY, String(scale)); } catch(e) {}
    }
    if (decrease) decrease.addEventListener('click', function(){ change(-0.1); });
    if (increase) increase.addEventListener('click', function(){ change(0.1); });
    if (resetBtn) resetBtn.addEventListener('click', function(){ scale = 1; change(0); });
})();
</script>
<?php endif; ?>

<?php get_footer(); ?>