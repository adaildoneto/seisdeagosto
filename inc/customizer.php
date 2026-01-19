<?php
/**
 * Theme Customizer Settings
 * 
 * Permite customizar cores, fontes e estilos através do Customizer do WordPress
 */

/**
 * Register Extended Customizer Settings
 */
function u_correio68_get_google_font_presets() {
    return array(
        'custom' => array(
            'label'    => __( 'Personalizado (manual)', 'u_correio68' ),
            'families' => '',
            'body'     => '',
            'heading'  => '',
        ),
        'roboto' => array(
            'label'    => __( 'Roboto + Roboto Slab', 'u_correio68' ),
            'families' => 'Roboto:wght@300;400;500;700|Roboto+Slab:wght@400;600;700',
            'body'     => 'Roboto',
            'heading'  => 'Roboto Slab',
        ),
        'montserrat' => array(
            'label'    => __( 'Montserrat + Montserrat', 'u_correio68' ),
            'families' => 'Montserrat:wght@300;400;500;600;700',
            'body'     => 'Montserrat',
            'heading'  => 'Montserrat',
        ),
        'inter' => array(
            'label'    => __( 'Inter + Inter', 'u_correio68' ),
            'families' => 'Inter:wght@300;400;500;600;700',
            'body'     => 'Inter',
            'heading'  => 'Inter',
        ),
        'poppins' => array(
            'label'    => __( 'Poppins + Poppins', 'u_correio68' ),
            'families' => 'Poppins:wght@300;400;500;600;700',
            'body'     => 'Poppins',
            'heading'  => 'Poppins',
        ),
        'opensans_lato' => array(
            'label'    => __( 'Open Sans + Lato', 'u_correio68' ),
            'families' => 'Open+Sans:wght@300;400;600;700|Lato:wght@300;400;700',
            'body'     => 'Open Sans',
            'heading'  => 'Lato',
        ),
        'sourcesans_playfair' => array(
            'label'    => __( 'Source Sans 3 + Playfair Display', 'u_correio68' ),
            'families' => 'Source+Sans+3:wght@300;400;600;700|Playfair+Display:wght@400;600;700',
            'body'     => 'Source Sans 3',
            'heading'  => 'Playfair Display',
        ),
        'nunito_merriweather' => array(
            'label'    => __( 'Nunito + Merriweather', 'u_correio68' ),
            'families' => 'Nunito:wght@300;400;600;700|Merriweather:wght@400;700',
            'body'     => 'Nunito',
            'heading'  => 'Merriweather',
        ),
        'raleway_montserrat' => array(
            'label'    => __( 'Raleway + Montserrat', 'u_correio68' ),
            'families' => 'Raleway:wght@300;400;600;700|Montserrat:wght@300;400;600;700',
            'body'     => 'Raleway',
            'heading'  => 'Montserrat',
        ),
        'dm_sans_dm_serif' => array(
            'label'    => __( 'DM Sans + DM Serif Display', 'u_correio68' ),
            'families' => 'DM+Sans:wght@300;400;500;700|DM+Serif+Display',
            'body'     => 'DM Sans',
            'heading'  => 'DM Serif Display',
        ),
    );
}

/**
 * Default sidebar intro text using site name.
 */
function u_correio68_get_sidebar_intro_default_text() {
    $site_name = get_bloginfo( 'name' );
    if ( empty( $site_name ) ) {
        $site_name = wp_parse_url( home_url(), PHP_URL_HOST );
    }
    $site_name = $site_name ? $site_name : __( 'o site', 'u_correio68' );
    return sprintf(
        'O %s é um jornal em homenagem a seis de agosto, data da revolução acreana. Temos orgulho de ser acreano e a revolução virá através da informação.',
        $site_name
    );
}

function u_correio68_customize_register_extended( $wp_customize ) {
    
    // ====================
    // PAINEL: Aparência Visual
    // ====================
    $wp_customize->add_panel( 'u_correio68_appearance_panel', array(
        'title'       => __( '🎨 Aparência Visual', 'u_correio68' ),
        'description' => __( 'Personalize cores, fontes e estilos visuais do tema', 'u_correio68' ),
        'priority'    => 28,
    ) );

    // ====================
    // SEÇÃO: Cores Principais
    // ====================
    $wp_customize->add_section( 'u_correio68_colors', array(
        'title'       => __( 'Paleta de Cores', 'u_correio68' ),
        'description' => __( 'Defina as cores principais do site (header, footer, badges)', 'u_correio68' ),
        'panel'       => 'u_correio68_appearance_panel',
        'priority'    => 10,
    ) );

    // Cor primária
    $wp_customize->add_setting( 'u_correio68_primary_color', array(
        'default'           => '#0a4579',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'u_correio68_primary_color', array(
        'label'       => __( 'Cor Primária (Header/Menu)', 'u_correio68' ),
        'description' => __( 'Usada no cabeçalho, menu e elementos principais', 'u_correio68' ),
        'section'     => 'u_correio68_colors',
        'settings'    => 'u_correio68_primary_color',
    ) ) );

    // Cor do badge/destaque
    $wp_customize->add_setting( 'u_correio68_badge_color', array(
        'default'           => '#ec940d',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'u_correio68_badge_color', array(
        'label'       => __( 'Cor de Destaque (Badges/Footer)', 'u_correio68' ),
        'description' => __( 'Cor dos badges de categoria e fundo do rodapé', 'u_correio68' ),
        'section'     => 'u_correio68_colors',
        'settings'    => 'u_correio68_badge_color',
    ) ) );

    // Cor do cabeçalho (Header)
    $wp_customize->add_setting( 'u_correio68_header_bg', array(
        'default'           => '#0a4579',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'u_correio68_header_bg', array(
        'label'       => __( 'Cor do Cabeçalho (Header)', 'u_correio68' ),
        'description' => __( 'Fundo do topo e barra de categorias', 'u_correio68' ),
        'section'     => 'u_correio68_colors',
        'settings'    => 'u_correio68_header_bg',
    ) ) );

    // Cor do rodapé (Footer)
    $wp_customize->add_setting( 'u_correio68_footer_bg', array(
        'default'           => '#ec940d',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'u_correio68_footer_bg', array(
        'label'       => __( 'Cor do Rodapé (Footer)', 'u_correio68' ),
        'description' => __( 'Fundo do rodapé do site', 'u_correio68' ),
        'section'     => 'u_correio68_colors',
        'settings'    => 'u_correio68_footer_bg',
    ) ) );

    // Cor de fundo dos destaques
    $wp_customize->add_setting( 'u_correio68_highlight_bg', array(
        'default'           => '#efefef',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'u_correio68_highlight_bg', array(
        'label'       => __( 'Fundo da Seção de Destaques', 'u_correio68' ),
        'description' => __( 'Cor de fundo da área de notícias em destaque', 'u_correio68' ),
        'section'     => 'u_correio68_colors',
        'settings'    => 'u_correio68_highlight_bg',
    ) ) );

    // Cor de fundo do time/colunistas
    $wp_customize->add_setting( 'u_correio68_team_bg', array(
        'default'           => '#f6f6f6',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'u_correio68_team_bg', array(
        'label'       => __( 'Fundo dos Cards de Colunistas', 'u_correio68' ),
        'description' => __( 'Cor de fundo dos cards da equipe/colunistas', 'u_correio68' ),
        'section'     => 'u_correio68_colors',
        'settings'    => 'u_correio68_team_bg',
    ) ) );

    // ====================
    // SEÇÃO: Tipografia
    // ====================
    $wp_customize->add_section( 'u_correio68_typography', array(
        'title'       => __( 'Fontes e Tamanhos', 'u_correio68' ),
        'description' => __( 'Ajuste tamanhos de títulos e peso das fontes', 'u_correio68' ),
        'panel'       => 'u_correio68_appearance_panel',
        'priority'    => 20,
    ) );

    // Tamanho título grande
    $wp_customize->add_setting( 'u_correio68_title_large_size', array(
        'default'           => '35',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'u_correio68_title_large_size', array(
        'label'    => __( 'Tamanho Título Grande (px)', 'u_correio68' ),
        'section'  => 'u_correio68_typography',
        'type'     => 'number',
        'input_attrs' => array(
            'min'  => 16,
            'max'  => 72,
            'step' => 1,
        ),
    ) );

    // Tamanho título médio
    $wp_customize->add_setting( 'u_correio68_title_medium_size', array(
        'default'           => '19',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'u_correio68_title_medium_size', array(
        'label'    => __( 'Tamanho Título Médio (px)', 'u_correio68' ),
        'section'  => 'u_correio68_typography',
        'type'     => 'number',
        'input_attrs' => array(
            'min'  => 12,
            'max'  => 48,
            'step' => 1,
        ),
    ) );

    // Tamanho título do post
    $wp_customize->add_setting( 'u_correio68_post_title_size', array(
        'default'           => '2.5',
        'sanitize_callback' => 'u_correio68_sanitize_float',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'u_correio68_post_title_size', array(
        'label'    => __( 'Tamanho Título do Post (rem)', 'u_correio68' ),
        'section'  => 'u_correio68_typography',
        'type'     => 'number',
        'input_attrs' => array(
            'min'  => 1,
            'max'  => 5,
            'step' => 0.1,
        ),
    ) );

    // Peso da fonte do título grande
    $wp_customize->add_setting( 'u_correio68_title_weight', array(
        'default'           => '600',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'u_correio68_title_weight', array(
        'label'   => __( 'Peso Título Grande', 'u_correio68' ),
        'section' => 'u_correio68_typography',
        'type'    => 'select',
        'choices' => array(
            '300' => __( 'Light (300)', 'u_correio68' ),
            '400' => __( 'Normal (400)', 'u_correio68' ),
            '600' => __( 'Semi-Bold (600)', 'u_correio68' ),
            '700' => __( 'Bold (700)', 'u_correio68' ),
            '900' => __( 'Black (900)', 'u_correio68' ),
        ),
    ) );

    // Google Fonts (opcional)
    $presets = u_correio68_get_google_font_presets();
    $preset_choices = array();
    foreach ( $presets as $key => $preset ) {
        $preset_choices[ $key ] = $preset['label'];
    }

    $wp_customize->add_setting( 'u_correio68_google_fonts_preset', array(
        'default'           => 'custom',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'u_correio68_google_fonts_preset', array(
        'label'       => __( 'Preset de fontes (2025)', 'u_correio68' ),
        'description' => __( 'Selecione um conjunto pronto para facilitar o uso.', 'u_correio68' ),
        'section'     => 'u_correio68_typography',
        'type'        => 'select',
        'choices'     => $preset_choices,
    ) );

    $wp_customize->add_setting( 'u_correio68_enable_google_fonts', array(
        'default'           => 0,
        'sanitize_callback' => 'u_correio68_sanitize_checkbox',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'u_correio68_enable_google_fonts', array(
        'label'       => __( 'Carregar Google Fonts', 'u_correio68' ),
        'description' => __( 'Ative para padronizar fontes via Google Fonts.', 'u_correio68' ),
        'section'     => 'u_correio68_typography',
        'type'        => 'checkbox',
    ) );

    $wp_customize->add_setting( 'u_correio68_google_fonts_family', array(
        'default'           => 'Open+Sans:wght@300;400;600;700|Lato:wght@300;400;700',
        'sanitize_callback' => 'u_correio68_sanitize_google_fonts_family',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'u_correio68_google_fonts_family', array(
        'label'       => __( 'Famílias Google Fonts (URL)', 'u_correio68' ),
        'description' => __( 'Ex.: Open+Sans:wght@300;400;600;700|Lato:wght@300;400;700', 'u_correio68' ),
        'section'     => 'u_correio68_typography',
        'type'        => 'text',
    ) );

    $wp_customize->add_setting( 'u_correio68_font_body', array(
        'default'           => 'Open Sans',
        'sanitize_callback' => 'u_correio68_sanitize_font_name',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'u_correio68_font_body', array(
        'label'       => __( 'Fonte do corpo (nome)', 'u_correio68' ),
        'description' => __( 'Ex.: Open Sans', 'u_correio68' ),
        'section'     => 'u_correio68_typography',
        'type'        => 'text',
    ) );

    $wp_customize->add_setting( 'u_correio68_font_heading', array(
        'default'           => 'Lato',
        'sanitize_callback' => 'u_correio68_sanitize_font_name',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'u_correio68_font_heading', array(
        'label'       => __( 'Fonte de títulos (nome)', 'u_correio68' ),
        'description' => __( 'Ex.: Lato', 'u_correio68' ),
        'section'     => 'u_correio68_typography',
        'type'        => 'text',
    ) );

    // Hifenização automática
    $wp_customize->add_setting( 'u_correio68_enable_hyphenation', array(
        'default'           => 0,
        'sanitize_callback' => 'u_correio68_sanitize_checkbox',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'u_correio68_enable_hyphenation', array(
        'label'       => __( 'Hifenizar textos automaticamente', 'u_correio68' ),
        'description' => __( 'Quando ativo, o tema permite hifenização automática em textos longos.', 'u_correio68' ),
        'section'     => 'u_correio68_typography',
        'type'        => 'checkbox',
    ) );

    // ====================
    // SEÇÃO: Espaçamentos e Dimensões
    // ====================
    $wp_customize->add_section( 'u_correio68_layout', array(
        'title'       => __( 'Espaçamentos e Tamanhos', 'u_correio68' ),
        'description' => __( 'Configure altura de imagens e espaçamentos entre elementos', 'u_correio68' ),
        'panel'       => 'u_correio68_appearance_panel',
        'priority'    => 30,
    ) );

    // Altura da imagem de destaque
    $wp_customize->add_setting( 'u_correio68_featured_height', array(
        'default'           => '425',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'u_correio68_featured_height', array(
        'label'    => __( 'Altura Imagem Destaque (px)', 'u_correio68' ),
        'section'  => 'u_correio68_layout',
        'type'     => 'number',
        'input_attrs' => array(
            'min'  => 200,
            'max'  => 800,
            'step' => 5,
        ),
    ) );

    // Espaçamento entre cards
    $wp_customize->add_setting( 'u_correio68_card_spacing', array(
        'default'           => '8',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'u_correio68_card_spacing', array(
        'label'    => __( 'Espaçamento entre Cards (px)', 'u_correio68' ),
        'section'  => 'u_correio68_layout',
        'type'     => 'number',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 40,
            'step' => 2,
        ),
    ) );

        // ====================
        // SEÇÃO: Menu Lateral
        // ====================
        $wp_customize->add_section( 'u_correio68_sidebar', array(
            'title'       => __( 'Menu Lateral', 'u_correio68' ),
            'description' => __( 'Configurações do menu que abre (sidebar)', 'u_correio68' ),
            'panel'       => 'u_correio68_appearance_panel',
            'priority'    => 40,
        ) );

        // Texto introdutório do menu lateral
        $wp_customize->add_setting( 'u_correio68_sidebar_intro_text', array(
            'default'           => u_correio68_get_sidebar_intro_default_text(),
            'sanitize_callback' => 'sanitize_textarea_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'u_correio68_sidebar_intro_text', array(
            'label'       => __( 'Texto introdutório do menu lateral', 'u_correio68' ),
            'description' => __( 'Este texto aparece no topo do menu lateral, junto à logo.', 'u_correio68' ),
            'section'     => 'u_correio68_sidebar',
            'type'        => 'textarea',
        ) );

        // ====================
        // SEÇÃO: Modelos de Arquivo (Categoria, Tag, Busca)
        // ====================
        $content_panel_id = 'u_correio68_content_panel';
        $content_panel    = method_exists( $wp_customize, 'get_panel' ) && $wp_customize->get_panel( $content_panel_id ) ? $content_panel_id : null;

        $wp_customize->add_section( 'u_correio68_templates', array(
            'title'       => __( 'Modelos de Arquivo', 'u_correio68' ),
            'description' => __( 'Escolha páginas criadas com blocos para desenhar Categorias, Tags e Busca. Dica: use o bloco “News Grid” sem categoria para seguir o contexto atual.', 'u_correio68' ),
            'panel'       => $content_panel,
            'priority'    => 45,
        ) );

        // Página modelo para Categorias
        $wp_customize->add_setting( 'template_page_category', array(
            'default'           => 0,
            'type'              => 'theme_mod',
            'sanitize_callback' => 'absint',
        ) );
        $wp_customize->add_control( 'template_page_category', array(
            'label'       => __( 'Página modelo: Categorias', 'u_correio68' ),
            'description' => __( 'Selecione uma Página com blocos para renderizar arquivos de categoria.', 'u_correio68' ),
            'type'        => 'dropdown-pages',
            'section'     => 'u_correio68_templates',
        ) );

        // Página modelo para Tags
        $wp_customize->add_setting( 'template_page_tag', array(
            'default'           => 0,
            'type'              => 'theme_mod',
            'sanitize_callback' => 'absint',
        ) );
        $wp_customize->add_control( 'template_page_tag', array(
            'label'       => __( 'Página modelo: Tags', 'u_correio68' ),
            'description' => __( 'Selecione uma Página com blocos para renderizar arquivos de tags.', 'u_correio68' ),
            'type'        => 'dropdown-pages',
            'section'     => 'u_correio68_templates',
        ) );

        // Página modelo para Busca
        $wp_customize->add_setting( 'template_page_search', array(
            'default'           => 0,
            'type'              => 'theme_mod',
            'sanitize_callback' => 'absint',
        ) );
        $wp_customize->add_control( 'template_page_search', array(
            'label'       => __( 'Página modelo: Busca', 'u_correio68' ),
            'description' => __( 'Selecione uma Página com blocos para renderizar resultados de busca.', 'u_correio68' ),
            'type'        => 'dropdown-pages',
            'section'     => 'u_correio68_templates',
        ) );

        // ====================
        // SEÇÃO: Posts e Badges
        // ====================
        $wp_customize->add_section( 'u_correio68_posts_badges', array(
            'title'       => __( 'Posts e Badges', 'u_correio68' ),
            'description' => __( 'Opções para a chamada dos posts e exibição da badge.', 'u_correio68' ),
            'panel'       => $content_panel,
            'priority'    => 50,
        ) );

        // Fonte da chamada (tema ou nativa)
        $wp_customize->add_setting( 'u_correio68_chamada_source', array(
            'default'           => 'theme',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'u_correio68_chamada_source', array(
            'label'       => __( 'Fonte da chamada', 'u_correio68' ),
            'description' => __( 'Escolha entre a chamada definida no tema ou o resumo nativo do post.', 'u_correio68' ),
            'section'     => 'u_correio68_posts_badges',
            'type'        => 'select',
            'choices'     => array(
                'theme'  => __( 'Chamada do tema', 'u_correio68' ),
                'native' => __( 'Resumo nativo (excerpt)', 'u_correio68' ),
            ),
        ) );

        // Chamada definida no tema
        $wp_customize->add_setting( 'u_correio68_theme_chamada', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'u_correio68_theme_chamada', array(
            'label'       => __( 'Chamada do tema', 'u_correio68' ),
            'description' => __( 'Texto usado quando a fonte estiver em "Chamada do tema".', 'u_correio68' ),
            'section'     => 'u_correio68_posts_badges',
            'type'        => 'text',
        ) );

        // Exibir categoria na badge com a cor primária
        $wp_customize->add_setting( 'u_correio68_badge_show_category_primary', array(
            'default'           => 0,
            'sanitize_callback' => 'u_correio68_sanitize_checkbox',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'u_correio68_badge_show_category_primary', array(
            'label'       => __( 'Badge com categoria na cor primária', 'u_correio68' ),
            'description' => __( 'Substitui o texto da badge pela categoria principal do post e força o uso da cor primária do tema.', 'u_correio68' ),
            'section'     => 'u_correio68_posts_badges',
            'type'        => 'checkbox',
        ) );

        // Texto padrão para a chamada (fallback)
        $wp_customize->add_setting( 'u_correio68_default_chamada', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'u_correio68_default_chamada', array(
            'label'       => __( 'Chamada padrão', 'u_correio68' ),
            'description' => __( 'Usada quando o campo personalizado estiver vazio ou o plugin de campos não estiver ativo.', 'u_correio68' ),
            'section'     => 'u_correio68_posts_badges',
            'type'        => 'text',
        ) );

        // Ícone Font Awesome padrão para a badge
        $wp_customize->add_setting( 'u_correio68_badge_icon_class', array(
            'default'           => 'fa-star',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'u_correio68_badge_icon_class', array(
            'label'       => __( 'Ícone padrão da badge (Font Awesome)', 'u_correio68' ),
            'description' => __( 'Ex.: fa-star, fa-bolt, fa-fire. Usado se o post não definir outro ícone.', 'u_correio68' ),
            'section'     => 'u_correio68_posts_badges',
            'type'        => 'text',
        ) );

        // Cor opcional do fundo da badge definida pelo usuário
        $wp_customize->add_setting( 'u_correio68_badge_custom_color', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'u_correio68_badge_custom_color', array(
            'label'       => __( 'Cor opcional do fundo da badge', 'u_correio68' ),
            'description' => __( 'Se definido, usa esta cor no fundo; caso contrário, usa a cor primária do tema.', 'u_correio68' ),
            'section'     => 'u_correio68_posts_badges',
            'settings'    => 'u_correio68_badge_custom_color',
        ) ) );

        // Cor do texto/ícone da badge
        $wp_customize->add_setting( 'u_correio68_badge_text_color', array(
            'default'           => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'u_correio68_badge_text_color', array(
            'label'       => __( 'Cor do texto/ícone da badge', 'u_correio68' ),
            'description' => __( 'Define a cor do texto e do ícone da badge. O ícone seguirá a mesma cor do texto.', 'u_correio68' ),
            'section'     => 'u_correio68_posts_badges',
            'settings'    => 'u_correio68_badge_text_color',
        ) ) );
}
add_action( 'customize_register', 'u_correio68_customize_register_extended', 20 );

/**
 * Sanitize float values
 */
function u_correio68_sanitize_float( $value ) {
    return floatval( $value );
}

/**
 * Sanitize checkbox values (1 or 0)
 */
function u_correio68_sanitize_checkbox( $checked ) {
    return isset( $checked ) && (bool) $checked ? 1 : 0;
}

/**
 * Sanitize Google Fonts family query string.
 */
function u_correio68_sanitize_google_fonts_family( $value ) {
    $value = sanitize_text_field( $value );
    $value = trim( $value );
    $value = str_replace( array( "\n", "\r", "\t" ), '', $value );
    $value = preg_replace( '/\s+/', ' ', $value );
    $value = str_replace( ' ', '+', $value );
    $value = preg_replace( '/[^A-Za-z0-9\+\:\;\@\|\,_-]/', '', $value );
    $value = preg_replace( '/\+{2,}/', '+', $value );
    return $value;
}

/**
 * Sanitize font family display name.
 */
function u_correio68_sanitize_font_name( $value ) {
    $value = sanitize_text_field( $value );
    $value = preg_replace( '/[^A-Za-z0-9\s\-_]/', '', $value );
    $value = preg_replace( '/\s+/', ' ', $value );
    return trim( $value );
}

/**
 * Generate custom CSS from Customizer settings
 */
function u_correio68_customizer_css() {
    $primary_color    = get_theme_mod( 'u_correio68_primary_color', '#0a4579' );
    $badge_color      = get_theme_mod( 'u_correio68_badge_color', '#ec940d' );
    $badge_text_color = get_theme_mod( 'u_correio68_badge_text_color', '#ffffff' );
    $highlight_bg     = get_theme_mod( 'u_correio68_highlight_bg', '#efefef' );
    $team_bg          = get_theme_mod( 'u_correio68_team_bg', '#f6f6f6' );
    $header_bg        = get_theme_mod( 'u_correio68_header_bg', $primary_color );
    $footer_bg        = get_theme_mod( 'u_correio68_footer_bg', $badge_color );
    
    $title_large      = get_theme_mod( 'u_correio68_title_large_size', 35 );
    $title_medium     = get_theme_mod( 'u_correio68_title_medium_size', 19 );
    $post_title       = get_theme_mod( 'u_correio68_post_title_size', 2.5 );
    $title_weight     = get_theme_mod( 'u_correio68_title_weight', '600' );
    
    $featured_height  = get_theme_mod( 'u_correio68_featured_height', 425 );
    $card_spacing     = get_theme_mod( 'u_correio68_card_spacing', 8 );
    $enable_hyphenation = (bool) get_theme_mod( 'u_correio68_enable_hyphenation', false );
    $hyphens_value      = $enable_hyphenation ? 'auto' : 'manual';
    $overflow_wrap      = $enable_hyphenation ? 'anywhere' : 'normal';
    $font_body          = get_theme_mod( 'u_correio68_font_body', 'Open Sans' );
    $font_heading       = get_theme_mod( 'u_correio68_font_heading', 'Lato' );
    $preset_key         = get_theme_mod( 'u_correio68_google_fonts_preset', 'custom' );
    $presets            = u_correio68_get_google_font_presets();
    if ( isset( $presets[ $preset_key ] ) && $preset_key !== 'custom' ) {
        $font_body    = $presets[ $preset_key ]['body'];
        $font_heading = $presets[ $preset_key ]['heading'];
    }
    
    ?>
    <style type="text/css" id="u-correio68-customizer-css">
        :root {
            --u68-primary-color: <?php echo esc_attr( $primary_color ); ?>;
            --u68-badge-color: <?php echo esc_attr( $badge_color ); ?>;
            --u68-badge-text-color: <?php echo esc_attr( $badge_text_color ); ?>;
            --u68-highlight-bg: <?php echo esc_attr( $highlight_bg ); ?>;
            --u68-team-bg: <?php echo esc_attr( $team_bg ); ?>;
            --u68-header-bg: <?php echo esc_attr( $header_bg ); ?>;
            --u68-footer-bg: <?php echo esc_attr( $footer_bg ); ?>;
            --u68-title-large-size: <?php echo absint( $title_large ); ?>px;
            --u68-title-medium-size: <?php echo absint( $title_medium ); ?>px;
            --u68-post-title-size: <?php echo floatval( $post_title ); ?>rem;
            --u68-title-weight: <?php echo esc_attr( $title_weight ); ?>;
            --u68-featured-height: <?php echo absint( $featured_height ); ?>px;
            --u68-card-spacing: <?php echo absint( $card_spacing ); ?>px;
            --u68-font-body: '<?php echo esc_attr( $font_body ); ?>', sans-serif;
            --u68-font-heading: '<?php echo esc_attr( $font_heading ); ?>', sans-serif;
        }

        /* Cores primárias */
        ol li::before,
        .our-team .picture::before,
        .our-team .picture::after {
            background-color: <?php echo esc_attr( $primary_color ); ?>;
        }

        /* Badges e destaques */
        .gradiente {
            border-bottom-color: <?php echo esc_attr( $badge_color ); ?>;
        }

        /* Backgrounds */
        .destaquebg {
            background: <?php echo esc_attr( $highlight_bg ); ?>;
        }

        .our-team {
            background-color: <?php echo esc_attr( $team_bg ); ?>;
        }

        /* Tipografia */
        body,
        .entry-content,
        .entry-content p,
        button,
        input,
        select,
        textarea,
        .wp-block,
        .widget,
        .widget-area,
        .site-footer,
        .site-header,
        .navbar,
        .nav,
        .nav-link,
        .menu,
        .menu a {
            font-family: var(--u68-font-body, 'Open Sans', sans-serif);
        }

        h1, h2, h3, h4, h5, h6,
        .TituloGrande,
        .TituloGrande2,
        .title-post,
        .news-grid .card-title,
        .colunista-bubble p,
        .site-title,
        .site-description,
        .widget-title,
        .card-title,
        .badge,
        .btn,
        .navbar .nav-link,
        .menu a {
            font-family: var(--u68-font-heading, 'Lato', sans-serif);
        }

        body,
        .entry-content,
        .entry-content p,
        .TituloGrande,
        .TituloGrande2,
        .title-post,
        .news-grid,
        .colunista-bubble p {
            hyphens: <?php echo esc_attr( $hyphens_value ); ?>;
            -webkit-hyphens: <?php echo esc_attr( $hyphens_value ); ?>;
            -ms-hyphens: <?php echo esc_attr( $hyphens_value ); ?>;
            overflow-wrap: <?php echo esc_attr( $overflow_wrap ); ?>;
            word-break: normal;
        }

        .TituloGrande {
            font-size: <?php echo absint( $title_large ); ?>px;
            font-weight: <?php echo esc_attr( $title_weight ); ?>;
        }

        .TituloGrande2 {
            font-size: <?php echo absint( $title_medium ); ?>px;
        }

        .title-post {
            font-size: <?php echo floatval( $post_title ); ?>rem;
        }

        /* Layout */
        .imagem-destaque {
            height: <?php echo absint( $featured_height ); ?>px;
        }

        .spaces {
            margin-bottom: <?php echo absint( $card_spacing ); ?>px;
        }

        /* Header e Footer */
        .bg-primary,
        .topbar,
        .navbar-categorias {
            background-color: <?php echo esc_attr( $header_bg ); ?> !important;
        }

        /* Sidebar acompanha a cor do header */
        #sidebar,
        #sidebar .sidebar-header {
            background-color: <?php echo esc_attr( $header_bg ); ?> !important;
        }

        /* Sidebar header visuals */
        #sidebar .sidebar-header .custom-logo {
            max-width: 160px;
            height: auto;
        }
        #sidebar .sidebar-header .sidebar-intro {
            display: flex;
            align-items: flex-start;
            padding: 10px 12px;
            margin-top: 12px;
            border-left: 4px solid <?php echo esc_attr( $badge_color ); ?>;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.06);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
            color: #fff;
            line-height: 1.35;
            letter-spacing: 0.1px;
        }
        #sidebar .sidebar-header .sidebar-intro i {
            color: <?php echo esc_attr( $badge_color ); ?>;
            font-size: 1rem;
            margin-top: 1px;
        }

        .bg-orange {
            background-color: <?php echo esc_attr( $badge_color ); ?> !important;
            color: <?php echo esc_attr( $badge_text_color ); ?> !important;
        }

        .badge,
        .badge i {
            color: <?php echo esc_attr( $badge_text_color ); ?>;
        }

        /* Mobile search toggle: outline white, pressed uses badge color */
        #headnev #searchToggle {
            border-color: #ffffff !important;
            color: #ffffff !important;
            background-color: transparent !important;
        }
        /* Hover/Focus: stay discreet (transparent background, white outline) */
        #headnev #searchToggle:hover,
        #headnev #searchToggle:focus {
            background-color: transparent !important;
            border-color: #ffffff !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25) !important; /* subtle visible hover */
        }
        /* Active/Expanded: use badge color to indicate pressed state */
        #headnev #searchToggle:active,
        #headnev #searchToggle[aria-expanded="true"] {
            background-color: <?php echo esc_attr( $badge_color ); ?> !important;
            border-color: <?php echo esc_attr( $badge_color ); ?> !important;
            color: #ffffff !important;
            box-shadow: none !important;
        }

        /* Categorias: tornar hover/active visíveis e alinhados ao tema */
        .navbar-categorias .navbar-nav .nav-link,
        .navbar-categorias .navbar-nav > .menu-item > a {
            color: #ffffff !important;
            transition: background-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
            border-radius: 4px;
        }
        /* Hover/Focus: leve overlay para visibilidade */
        .navbar-categorias .navbar-nav .nav-link:hover,
        .navbar-categorias .navbar-nav .nav-link:focus,
        .navbar-categorias .navbar-nav > .menu-item > a:hover,
        .navbar-categorias .navbar-nav > .menu-item > a:focus {
            background-color: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
        }
        /* Active/Current: destacar com a cor de badge */
        .navbar-categorias .navbar-nav .nav-link.active,
        .navbar-categorias .navbar-nav > .menu-item.current-menu-item > a,
        .navbar-categorias .navbar-nav > .menu-item.active > a {
            background-color: <?php echo esc_attr( $badge_color ); ?> !important;
            color: #ffffff !important;
        }

        /* Footer wrapper override to ensure Customizer color applies */
        #wrapper-footer,
        .site-footer {
            background-color: <?php echo esc_attr( $footer_bg ); ?> !important;
        }

        /* Single post reader helpers */
        .single-post #single-wrapper { --u68-post-scale: 1; }
        .single-post #single-wrapper .entry-content {
            font-size: calc(1rem * var(--u68-post-scale, 1));
            line-height: 1.7;
        }
        .single-post #single-wrapper .entry-content p,
        .single-post #single-wrapper .entry-content li,
        .single-post #single-wrapper .entry-content blockquote,
        .single-post #single-wrapper .entry-content figcaption {
            font-size: calc(1rem * var(--u68-post-scale, 1));
        }
        .single-post #single-wrapper .entry-content img,
        .single-post #single-wrapper .entry-content figure,
        .single-post #single-wrapper .entry-content iframe,
        .single-post #single-wrapper .entry-content video {
            max-width: 100% !important;
            height: auto !important;
        }
        .single-post #single-wrapper .entry-content table {
            width: 100%;
            overflow-x: auto;
            display: block;
        }

        /* Reader toolbar buttons follow theme variables */
        .single-post #single-wrapper #reader-toolbar .btn {
            color: var(--u68-primary-color) !important;
            border-color: var(--u68-primary-color) !important;
            background-color: transparent !important;
        }
        .single-post #single-wrapper #reader-toolbar .btn:hover,
        .single-post #single-wrapper #reader-toolbar .btn:focus {
            color: var(--u68-primary-color) !important;
            border-color: var(--u68-primary-color) !important;
            background-color: transparent !important;
            outline: 2px solid var(--u68-primary-color);
            outline-offset: 2px;
        }
        .single-post #single-wrapper #reader-toolbar .btn:active {
            background-color: var(--u68-badge-color) !important;
            border-color: var(--u68-badge-color) !important;
            color: #ffffff !important;
        }

        /* Mobile Responsive */
        @media (max-width: 575px) {
            .TituloGrande {
                font-size: <?php echo absint( $title_medium ); ?>px;
            }
        }

        /* Weather block eye-candy minimal (default on light backgrounds) */
        .weather-eyecandy {
            background-color: #ffffff !important;
            border: 1px solid #e9ecef !important;
            border-radius: 12px !important;
            color: #212529 !important; /* default text on light bg */
        }
        .weather-eyecandy .text-muted { color: #6c757d !important; }
        /* In dark headers / topbars, invert for contrast */
        .bg-primary .weather-eyecandy,
        .topbar .weather-eyecandy {
            background-color: rgba(255,255,255,0.08) !important;
            border: 1px solid rgba(255,255,255,0.12) !important;
            color: #ffffff !important;
        }
        .bg-primary .weather-eyecandy .text-muted,
        .topbar .weather-eyecandy .text-muted { color: rgba(255,255,255,0.85) !important; }

        /* Sidebar menu hover/active: ensure contrast using theme variables */
        #sidebar ul li a {
            color: #ffffff !important;
        }
        #sidebar ul li a:hover,
        #sidebar ul li a:focus {
            background-color: rgba(255,255,255,0.12) !important;
            color: #ffffff !important;
        }
        #sidebar ul li.active > a,
        #sidebar ul li.current-menu-item > a,
        #sidebar ul li > a[aria-expanded="true"] {
            background-color: var(--u68-badge-color) !important;
            color: #ffffff !important;
        }
        .weather-fa-icon {
            font-size: 28px;
            line-height: 1;
            color: <?php echo esc_attr( $badge_color ); ?> !important;
            text-shadow: 0 1px 0 rgba(0,0,0,0.15);
        }
        .weather-eyecandy .gap-3 > * { margin-right: 1rem; }
    </style>
    <?php
}
add_action( 'wp_head', 'u_correio68_customizer_css' );

/**
 * Retorna o nome da categoria principal do post (Yoast se existir, senão a primeira).
 */
function u_correio68_get_primary_category_name( $post_id = null ) {
    $post_id = $post_id ? intval( $post_id ) : get_the_ID();
    if ( ! $post_id ) {
        return '';
    }

    $cat_id       = 0;
    $yoast_primary = intval( get_post_meta( $post_id, '_yoast_wpseo_primary_category', true ) );
    if ( $yoast_primary && term_exists( $yoast_primary, 'category' ) ) {
        $cat_id = $yoast_primary;
    } else {
        $cats = get_the_category( $post_id );
        if ( ! empty( $cats ) && isset( $cats[0]->term_id ) ) {
            $cat_id = intval( $cats[0]->term_id );
        }
    }

    if ( $cat_id ) {
        $term = get_term( $cat_id );
        if ( $term && ! is_wp_error( $term ) ) {
            return (string) $term->name;
        }
    }

    return '';
}

/**
 * Filtra o valor de "chamada" para suportar as opções do Customizer.
 */
function u_correio68_filter_chamada_value( $value, $post_id, $field ) {
    $post_id         = $post_id ? intval( $post_id ) : get_the_ID();
    $source          = get_theme_mod( 'u_correio68_chamada_source', '' );
    $legacy_native   = (bool) get_theme_mod( 'u_correio68_use_native_chamada', false );
    $theme_callout   = get_theme_mod( 'u_correio68_theme_chamada', '' );
    if ( empty( $source ) ) {
        $source = $legacy_native ? 'native' : 'acf';
    }
    $use_cat_badge   = (bool) get_theme_mod( 'u_correio68_badge_show_category_primary', false );
    $default_callout = get_theme_mod( 'u_correio68_default_chamada', '' );
    $value           = is_string( $value ) ? $value : '';

    // Só considera o valor do ACF quando o usuário escolhe "acf".
    if ( $source !== 'acf' ) {
        $value = '';
    }

    // Se o usuário quer mostrar a categoria no badge, prioriza o nome dela.
    if ( $use_cat_badge ) {
        $cat_label = u_correio68_get_primary_category_name( $post_id );
        if ( $cat_label ) {
            return wp_strip_all_tags( $cat_label );
        }
    }

    // Usa o texto do tema como chamada quando selecionado.
    if ( $source === 'theme' && ! empty( $theme_callout ) ) {
        $value = $theme_callout;
    }

    // Usa o excerpt do post como chamada nativa, quando selecionado.
    if ( $source === 'native' ) {
        $value = get_the_excerpt( $post_id );
    }

    // Fallback para texto padrão configurável
    if ( empty( $value ) && ! empty( $default_callout ) ) {
        $value = $default_callout;
    }

    // Fallback final: excerpt caso o campo esteja vazio.
    if ( empty( $value ) ) {
        $value = get_the_excerpt( $post_id );
    }

    return wp_strip_all_tags( (string) $value );
}

/**
 * Força a cor da badge para a cor primária quando a opção estiver ativa.
 */
function u_correio68_filter_badge_color_value( $value, $post_id, $field ) {
    $use_primary_badge = (bool) get_theme_mod( 'u_correio68_badge_show_category_primary', false );
    $custom_color      = get_theme_mod( 'u_correio68_badge_custom_color', '' );

    // Cor definida pelo usuário no Customizer
    if ( ! empty( $custom_color ) ) {
        return $custom_color;
    }

    // Se opção de categoria estiver ativa, força cor primária
    if ( $use_primary_badge ) {
        return get_theme_mod( 'u_correio68_primary_color', '#0a4579' );
    }

    // Fallback: cor do campo ou cor principal do tema
    if ( empty( $value ) ) {
        $value = get_theme_mod( 'u_correio68_badge_color', '#ec940d' );
    }

    return $value;
}

/**
 * Define ícone padrão (Font Awesome) quando ausente.
 */
function u_correio68_filter_badge_icon_value( $value, $post_id, $field ) {
    $value         = is_string( $value ) ? trim( $value ) : '';
    if ( empty( $value ) ) {
        $value = (string) get_theme_mod( 'u_correio68_badge_icon_class', '' );
        $value = trim( $value );
    }
    if ( empty( $value ) ) {
        return '';
    }
    $value = u_correio68_normalize_fa_icon_class( $value );
    return sanitize_text_field( $value );
}

/**
 * Normaliza a classe de ícone para Font Awesome 4.7 (garante prefixo "fa").
 */
function u_correio68_normalize_fa_icon_class( $value ) {
    $value = is_string( $value ) ? trim( $value ) : '';
    if ( $value === '' ) {
        return $value;
    }
    $parts = preg_split( '/\s+/', $value );
    $has_fa = in_array( 'fa', $parts, true ) || in_array( 'fas', $parts, true ) || in_array( 'far', $parts, true ) || in_array( 'fal', $parts, true ) || in_array( 'fab', $parts, true );
    $has_fa_dash = false;
    foreach ( $parts as $part ) {
        if ( strpos( $part, 'fa-' ) === 0 ) {
            $has_fa_dash = true;
            break;
        }
    }
    if ( ! $has_fa && $has_fa_dash ) {
        array_unshift( $parts, 'fa' );
    }
    return implode( ' ', $parts );
}

// Aplica os filtros aos campos ACF correspondentes, se o plugin estiver presente ou shimado.
add_filter( 'acf/format_value/name=chamada', 'u_correio68_filter_chamada_value', 10, 3 );
add_filter( 'acf/format_value/name=cor', 'u_correio68_filter_badge_color_value', 10, 3 );
add_filter( 'acf/format_value/name=icones', 'u_correio68_filter_badge_icon_value', 10, 3 );

/**
 * Live preview support for Customizer
 */
function u_correio68_customize_preview_js() {
    if ( ! is_customize_preview() ) {
        return;
    }
    
    $script_path = get_template_directory() . '/js/customizer-preview.js';
    if ( ! file_exists( $script_path ) ) {
        return;
    }
    
    wp_enqueue_script(
        'u-correio68-customizer-preview',
        get_template_directory_uri() . '/js/customizer-preview.js',
        array( 'customize-preview' ),
        '1.0.0',
        true
    );
}
add_action( 'customize_preview_init', 'u_correio68_customize_preview_js' );
