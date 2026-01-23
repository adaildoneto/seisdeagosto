/**
 * Font Awesome Icon Picker - JavaScript
 * Handles AJAX loading and icon selection
 */

(function($) {
    'use strict';

    /**
     * Icon Picker Class
     */
    class IconPicker {
        constructor(inputElement, options = {}) {
            this.input = $(inputElement);
            this.options = $.extend({
                onSelect: null,
                modalTitle: 'Selecione um Ícone',
                searchPlaceholder: 'Pesquisar ícones...',
            }, options);
            
            this.modal = null;
            this.icons = [];
            this.selectedIcon = this.input.val() || '';
            this.searchTerm = '';
            
            this.init();
        }
        
        init() {
            this.createButton();
            this.createModal();
            this.bindEvents();
        }
        
        createButton() {
            const wrapper = $('<div class="icon-picker-wrapper"></div>');
            const inputGroup = $('<div class="icon-picker-input-group"></div>');
            
            this.input.wrap(wrapper);
            this.input.wrap(inputGroup);
            
            const button = $('<button type="button" class="icon-picker-button"><i class="fa fa-search"></i> Escolher Ícone</button>');
            this.input.parent().append(button);
            
            this.button = button;
        }
        
        createModal() {
            const modalHtml = `
                <div class="icon-picker-modal">
                    <div class="icon-picker-content">
                        <div class="icon-picker-header">
                            <h3>${this.options.modalTitle}</h3>
                            <button type="button" class="icon-picker-close" title="Fechar">×</button>
                        </div>
                        <div class="icon-picker-search">
                            <input type="text" placeholder="${this.options.searchPlaceholder}">
                        </div>
                        <div class="icon-picker-body">
                            <div class="icon-picker-loading">
                                <i class="fa fa-spinner fa-spin"></i>
                                <p>Carregando ícones...</p>
                            </div>
                        </div>
                        <div class="icon-picker-footer">
                            <button type="button" class="icon-picker-cancel">Cancelar</button>
                            <button type="button" class="icon-picker-select" disabled>Selecionar</button>
                        </div>
                    </div>
                </div>
            `;
            
            this.modal = $(modalHtml);
            $('body').append(this.modal);
            
            this.searchInput = this.modal.find('.icon-picker-search input');
            this.gridContainer = this.modal.find('.icon-picker-body');
            this.selectButton = this.modal.find('.icon-picker-select');
        }
        
        bindEvents() {
            // Open modal
            this.button.on('click', (e) => {
                e.preventDefault();
                this.openModal();
            });
            
            // Close modal
            this.modal.find('.icon-picker-close, .icon-picker-cancel').on('click', () => {
                this.closeModal();
            });
            
            // Close on background click
            this.modal.on('click', (e) => {
                if ($(e.target).hasClass('icon-picker-modal')) {
                    this.closeModal();
                }
            });
            
            // Search
            this.searchInput.on('input', (e) => {
                this.searchTerm = e.target.value.toLowerCase();
                this.renderIcons();
            });
            
            // Select icon
            this.selectButton.on('click', () => {
                this.confirmSelection();
            });
            
            // Close on ESC
            $(document).on('keydown', (e) => {
                if (e.key === 'Escape' && this.modal.hasClass('active')) {
                    this.closeModal();
                }
            });
        }
        
        openModal() {
            this.modal.addClass('active');
            this.searchInput.val('').focus();
            
            if (this.icons.length === 0) {
                this.loadIcons();
            } else {
                this.renderIcons();
            }
        }
        
        closeModal() {
            this.modal.removeClass('active');
            this.searchTerm = '';
        }
        
        loadIcons() {
            $.ajax({
                url: seisdeagostoIconPicker.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_fontawesome_icons',
                    nonce: seisdeagostoIconPicker.nonce
                },
                success: (response) => {
                    if (response.success && response.data.icons) {
                        this.icons = response.data.icons;
                        this.renderIcons();
                    } else {
                        this.showError('Erro ao carregar ícones.');
                    }
                },
                error: () => {
                    this.showError('Erro de conexão. Tente novamente.');
                }
            });
        }
        
        renderIcons() {
            const filteredIcons = this.icons.filter(icon => {
                if (!this.searchTerm) return true;
                return icon.name.toLowerCase().includes(this.searchTerm) ||
                       icon.label.toLowerCase().includes(this.searchTerm);
            });
            
            if (filteredIcons.length === 0) {
                this.showEmpty();
                return;
            }
            
            const grid = $('<div class="icon-picker-grid"></div>');
            
            filteredIcons.forEach(icon => {
                const item = $(`
                    <div class="icon-picker-item" data-icon="${icon.name}">
                        <i class="fa ${icon.name}"></i>
                        <span>${icon.label}</span>
                    </div>
                `);
                
                if (icon.name === this.selectedIcon) {
                    item.addClass('selected');
                    this.selectButton.prop('disabled', false);
                }
                
                item.on('click', () => {
                    this.selectIcon(icon.name);
                });
                
                grid.append(item);
            });
            
            this.gridContainer.html(grid);
        }
        
        selectIcon(iconName) {
            this.selectedIcon = iconName;
            this.gridContainer.find('.icon-picker-item').removeClass('selected');
            this.gridContainer.find(`[data-icon="${iconName}"]`).addClass('selected');
            this.selectButton.prop('disabled', false);
        }
        
        confirmSelection() {
            if (this.selectedIcon) {
                this.input.val(this.selectedIcon).trigger('change');
                
                if (typeof this.options.onSelect === 'function') {
                    this.options.onSelect(this.selectedIcon);
                }
                
                this.closeModal();
            }
        }
        
        showError(message) {
            this.gridContainer.html(`
                <div class="icon-picker-empty">
                    <i class="fa fa-exclamation-triangle"></i>
                    <p>${message}</p>
                </div>
            `);
        }
        
        showEmpty() {
            this.gridContainer.html(`
                <div class="icon-picker-empty">
                    <i class="fa fa-search"></i>
                    <p>Nenhum ícone encontrado.</p>
                </div>
            `);
        }
    }
    
    /**
     * jQuery Plugin
     */
    $.fn.iconPicker = function(options) {
        return this.each(function() {
            if (!$(this).data('iconPicker')) {
                $(this).data('iconPicker', new IconPicker(this, options));
            }
        });
    };
    
    /**
     * Auto-initialize on elements with data-icon-picker attribute
     */
    $(document).ready(function() {
        $('[data-icon-picker]').iconPicker();
    });
    
})(jQuery);
