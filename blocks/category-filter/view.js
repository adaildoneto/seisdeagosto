/**
 * Category Filter Block - Frontend JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Adiciona animações e melhorias de UX
        $('.category-filter-link').on('mouseenter', function() {
            $(this).css('transform', 'translateX(4px)');
        }).on('mouseleave', function() {
            if (!$(this).hasClass('active')) {
                $(this).css('transform', 'translateX(0)');
            }
        });
        
        // Adiciona loading state ao clicar
        $('.category-filter-link, .category-filter-button').on('click', function() {
            $(this).closest('.category-filter-wrapper').addClass('category-filter-loading');
        });
        
        // Melhora acessibilidade do dropdown
        $('.category-filter-dropdown').on('focus', function() {
            $(this).parent().addClass('category-filter-focused');
        }).on('blur', function() {
            $(this).parent().removeClass('category-filter-focused');
        });
        
        // AJAX filter (opcional - para filtrar sem reload de página)
        if (window.categoryFilterAjax) {
            $('.category-filter-link, .category-filter-button').on('click', function(e) {
                e.preventDefault();
                
                var $this = $(this);
                var url = $this.attr('href');
                var categoryId = $this.data('category-id');
                
                // Atualiza estado ativo
                $this.siblings().removeClass('active');
                $this.addClass('active');
                
                // Aqui você pode implementar AJAX para carregar posts sem reload
                // Por exemplo, atualizar um wp:query block dinamicamente
                
                console.log('Category filter clicked:', categoryId);
                
                // Por padrão, navega normalmente
                window.location.href = url;
            });
        }
    });
    
})(jQuery);
