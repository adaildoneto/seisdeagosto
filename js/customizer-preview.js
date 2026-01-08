/**
 * Customizer Live Preview
 * Atualiza as cores e estilos em tempo real no Customizer
 */
(function($) {
    'use strict';

    // Cor primária
    wp.customize('u_correio68_primary_color', function(value) {
        value.bind(function(to) {
            $(':root').css('--u68-primary-color', to);
            $('ol li::before, .our-team .picture::before, .our-team .picture::after').css('background-color', to);
        });
    });

    // Cor do badge
    wp.customize('u_correio68_badge_color', function(value) {
        value.bind(function(to) {
            $(':root').css('--u68-badge-color', to);
            $('.gradiente').css('border-bottom-color', to);
        });
    });

    // Fundo dos destaques
    wp.customize('u_correio68_highlight_bg', function(value) {
        value.bind(function(to) {
            $(':root').css('--u68-highlight-bg', to);
            $('.destaquebg').css('background', to);
        });
    });

    // Fundo colunistas
    wp.customize('u_correio68_team_bg', function(value) {
        value.bind(function(to) {
            $(':root').css('--u68-team-bg', to);
            $('.our-team').css('background-color', to);
        });
    });

    // Tamanho título grande
    wp.customize('u_correio68_title_large_size', function(value) {
        value.bind(function(to) {
            $(':root').css('--u68-title-large-size', to + 'px');
            $('.TituloGrande').css('font-size', to + 'px');
        });
    });

    // Tamanho título médio
    wp.customize('u_correio68_title_medium_size', function(value) {
        value.bind(function(to) {
            $(':root').css('--u68-title-medium-size', to + 'px');
            $('.TituloGrande2').css('font-size', to + 'px');
        });
    });

    // Tamanho título do post
    wp.customize('u_correio68_post_title_size', function(value) {
        value.bind(function(to) {
            $(':root').css('--u68-post-title-size', to + 'rem');
            $('.title-post').css('font-size', to + 'rem');
        });
    });

    // Peso da fonte
    wp.customize('u_correio68_title_weight', function(value) {
        value.bind(function(to) {
            $(':root').css('--u68-title-weight', to);
            $('.TituloGrande').css('font-weight', to);
        });
    });

    // Altura imagem destaque
    wp.customize('u_correio68_featured_height', function(value) {
        value.bind(function(to) {
            $(':root').css('--u68-featured-height', to + 'px');
            $('.imagem-destaque').css('height', to + 'px');
        });
    });

    // Espaçamento entre cards
    wp.customize('u_correio68_card_spacing', function(value) {
        value.bind(function(to) {
            $(':root').css('--u68-card-spacing', to + 'px');
            $('.spaces').css('margin-bottom', to + 'px');
        });
    });

})(jQuery);
