<?php
/* Inline navbar search form */
?>
<form role="search" method="get" class="navbar-search d-flex align-items-center gap-2" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label class="visually-hidden" for="header-search"><?php esc_html_e( 'Pesquisar', 'u_correio68' ); ?></label>
    <input id="header-search" type="search" name="s" class="form-control form-control-sm bg-light border-0" placeholder="" value="<?php echo esc_attr( get_search_query() ); ?>">
    <button class="btn btn-outline-light btn-sm" type="submit" aria-label="<?php esc_attr_e( 'Pesquisar', 'u_correio68' ); ?>">
        <i class="fa fa-search"></i>
    </button>
</form>
