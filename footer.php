
                    </div>
                    <div class="wrapper bg-orange" id="wrapper-footer">
                        <div class="container-fluid">
                            <div class="row">
                                <?php if ( is_active_sidebar( 'bannerfooter' ) ) : ?>
                                    <div class="col-md-12 spaces">
                                        <?php dynamic_sidebar( 'bannerfooter' ); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <footer class="site-footer" id="colophon">
                                        <div class="site-info">
                                            <p class="text-ce text-center text-white"><?php echo get_theme_mod( 'footer_text', 'Orgulhosamente feito com <i class="fa fa-heart"></i> no Acre | <b>6barra8.com</b>' ); ?></p>
                                        </div>
                                        <!-- .site-info -->
                                    </footer>
                                    <!-- #colophon -->
                                </div>
                                <!--col end -->
                            </div>
                        </div>
                        <!-- container end -->
                    </div>
                </div>
            </div>
        </div>
        <?php wp_footer(); ?>
    </body>
</html>
