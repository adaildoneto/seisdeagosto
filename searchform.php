<?php
/* Inline navbar search form */
?>
<form role="search" method="get" class="navbar-search form-inline" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label class="sr-only" for="header-search"><?php esc_html_e( 'Buscar', 'u_correio68' ); ?></label>
    <input id="header-search" type="search" name="s" class="form-control form-control-sm bg-light border-0" placeholder="<?php esc_attr_e( 'Buscar…', 'u_correio68' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
    <button class="btn btn-outline btn-sm ml-2" type="submit" aria-label="<?php esc_attr_e( 'Pesquisar', 'u_correio68' ); ?>">
        <i class="fa fa-search"></i>
    </button>
</form>
