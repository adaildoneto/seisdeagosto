/**
 * Category Filter Block - Editor Component
 */

(function() {
    if (typeof wp === 'undefined') return;

    const { registerBlockType } = wp.blocks;
    const { InspectorControls, useBlockProps } = wp.blockEditor || wp.editor;
    const { PanelBody, ToggleControl, SelectControl, TextControl } = wp.components;
    const { __ } = wp.i18n;
    const { useSelect } = wp.data;

    registerBlockType('seisdeagosto/category-filter', {
        title: __('Filtro de Categorias', 'seisdeagosto'),
        icon: 'filter',
        category: 'seisdeagosto',
        
        attributes: {
            showAllOption: { type: 'boolean', default: true },
            allOptionText: { type: 'string', default: 'Todas as Categorias' },
            showCount: { type: 'boolean', default: true },
            showEmpty: { type: 'boolean', default: false },
            orderBy: { type: 'string', default: 'name' },
            order: { type: 'string', default: 'ASC' },
            hierarchical: { type: 'boolean', default: true },
            showHierarchy: { type: 'boolean', default: true },
            displayStyle: { type: 'string', default: 'dropdown' },
            showIcons: { type: 'boolean', default: true }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const {
                showAllOption, allOptionText, showCount, showEmpty,
                orderBy, order, hierarchical, showHierarchy,
                displayStyle, showIcons
            } = attributes;

            const blockProps = useBlockProps ? useBlockProps() : {};

            // Buscar categorias do WordPress
            const categories = useSelect(function(select) {
                const catList = select('core').getEntityRecords('taxonomy', 'category', {
                    per_page: -1,
                    hide_empty: !showEmpty,
                    orderby: orderBy,
                    order: order
                });
                return catList || [];
            }, [showEmpty, orderBy, order]);

            // Renderizar preview baseado no estilo
            function renderPreview() {
                if (!categories || categories.length === 0) {
                    return wp.element.createElement('div', {
                        style: { padding: '20px', textAlign: 'center', color: '#999' }
                    }, __('Carregando categorias...', 'seisdeagosto'));
                }

                if (displayStyle === 'dropdown') {
                    return wp.element.createElement('select', {
                        className: 'category-filter-dropdown',
                        disabled: true
                    },
                        showAllOption && wp.element.createElement('option', null, allOptionText),
                        categories.slice(0, 5).map(function(cat) {
                            return wp.element.createElement('option', {
                                key: cat.id,
                                value: cat.id
                            },
                                cat.name + (showCount ? ' (' + cat.count + ')' : '')
                            );
                        }),
                        categories.length > 5 && wp.element.createElement('option', null, '...')
                    );
                } else if (displayStyle === 'list') {
                    return wp.element.createElement('ul', {
                        className: 'category-filter-list'
                    },
                        categories.slice(0, 5).map(function(cat) {
                            return wp.element.createElement('li', {
                                key: cat.id,
                                className: 'category-filter-item'
                            },
                                wp.element.createElement('a', {
                                    href: '#',
                                    className: 'category-filter-link',
                                    onClick: function(e) { e.preventDefault(); }
                                },
                                    showIcons && wp.element.createElement('i', {
                                        className: 'fa fa-folder category-filter-icon'
                                    }),
                                    cat.name,
                                    showCount && wp.element.createElement('span', {
                                        className: 'category-filter-count'
                                    }, cat.count)
                                )
                            );
                        })
                    );
                } else if (displayStyle === 'grid') {
                    return wp.element.createElement('div', {
                        className: 'category-filter-grid'
                    },
                        categories.slice(0, 6).map(function(cat) {
                            return wp.element.createElement('div', {
                                key: cat.id,
                                className: 'category-filter-item'
                            },
                                wp.element.createElement('a', {
                                    href: '#',
                                    className: 'category-filter-link',
                                    onClick: function(e) { e.preventDefault(); }
                                },
                                    showIcons && wp.element.createElement('i', {
                                        className: 'fa fa-folder category-filter-icon'
                                    }),
                                    cat.name,
                                    showCount && wp.element.createElement('span', {
                                        className: 'category-filter-count'
                                    }, cat.count)
                                )
                            );
                        })
                    );
                } else if (displayStyle === 'buttons') {
                    return wp.element.createElement('div', {
                        className: 'category-filter-buttons'
                    },
                        showAllOption && wp.element.createElement('button', {
                            className: 'category-filter-button active'
                        }, allOptionText),
                        categories.slice(0, 5).map(function(cat) {
                            return wp.element.createElement('button', {
                                key: cat.id,
                                className: 'category-filter-button'
                            }, cat.name + (showCount ? ' (' + cat.count + ')' : ''));
                        })
                    );
                }
            }

            return wp.element.createElement(
                'div',
                blockProps,
                
                // Inspector Controls
                wp.element.createElement(
                    InspectorControls,
                    null,
                    
                    // Configurações de Exibição
                    wp.element.createElement(
                        PanelBody,
                        { title: __('Configurações de Exibição', 'seisdeagosto'), initialOpen: true },
                        
                        wp.element.createElement(SelectControl, {
                            label: __('Estilo de Exibição', 'seisdeagosto'),
                            value: displayStyle,
                            options: [
                                { label: __('Dropdown', 'seisdeagosto'), value: 'dropdown' },
                                { label: __('Lista', 'seisdeagosto'), value: 'list' },
                                { label: __('Grade', 'seisdeagosto'), value: 'grid' },
                                { label: __('Botões', 'seisdeagosto'), value: 'buttons' }
                            ],
                            onChange: function(value) { setAttributes({ displayStyle: value }); }
                        }),
                        
                        wp.element.createElement(ToggleControl, {
                            label: __('Mostrar opção "Todas"', 'seisdeagosto'),
                            checked: showAllOption,
                            onChange: function(value) { setAttributes({ showAllOption: value }); }
                        }),
                        
                        showAllOption && wp.element.createElement(TextControl, {
                            label: __('Texto da opção "Todas"', 'seisdeagosto'),
                            value: allOptionText,
                            onChange: function(value) { setAttributes({ allOptionText: value }); }
                        }),
                        
                        wp.element.createElement(ToggleControl, {
                            label: __('Mostrar contador de posts', 'seisdeagosto'),
                            checked: showCount,
                            onChange: function(value) { setAttributes({ showCount: value }); }
                        }),
                        
                        wp.element.createElement(ToggleControl, {
                            label: __('Mostrar ícones', 'seisdeagosto'),
                            checked: showIcons,
                            onChange: function(value) { setAttributes({ showIcons: value }); }
                        })
                    ),
                    
                    // Configurações de Categorias
                    wp.element.createElement(
                        PanelBody,
                        { title: __('Configurações de Categorias', 'seisdeagosto'), initialOpen: false },
                        
                        wp.element.createElement(ToggleControl, {
                            label: __('Incluir categorias vazias', 'seisdeagosto'),
                            checked: showEmpty,
                            onChange: function(value) { setAttributes({ showEmpty: value }); }
                        }),
                        
                        wp.element.createElement(ToggleControl, {
                            label: __('Hierárquico', 'seisdeagosto'),
                            checked: hierarchical,
                            onChange: function(value) { setAttributes({ hierarchical: value }); }
                        }),
                        
                        hierarchical && wp.element.createElement(ToggleControl, {
                            label: __('Mostrar hierarquia visualmente', 'seisdeagosto'),
                            checked: showHierarchy,
                            onChange: function(value) { setAttributes({ showHierarchy: value }); }
                        }),
                        
                        wp.element.createElement(SelectControl, {
                            label: __('Ordenar por', 'seisdeagosto'),
                            value: orderBy,
                            options: [
                                { label: __('Nome', 'seisdeagosto'), value: 'name' },
                                { label: __('Contagem', 'seisdeagosto'), value: 'count' },
                                { label: __('ID', 'seisdeagosto'), value: 'id' },
                                { label: __('Slug', 'seisdeagosto'), value: 'slug' }
                            ],
                            onChange: function(value) { setAttributes({ orderBy: value }); }
                        }),
                        
                        wp.element.createElement(SelectControl, {
                            label: __('Ordem', 'seisdeagosto'),
                            value: order,
                            options: [
                                { label: __('Crescente', 'seisdeagosto'), value: 'ASC' },
                                { label: __('Decrescente', 'seisdeagosto'), value: 'DESC' }
                            ],
                            onChange: function(value) { setAttributes({ order: value }); }
                        })
                    )
                ),
                
                // Block Preview
                wp.element.createElement(
                    'div',
                    { className: 'category-filter-wrapper' },
                    wp.element.createElement('div', {
                        style: {
                            padding: '12px',
                            background: '#f5f5f5',
                            borderRadius: '4px',
                            marginBottom: '12px',
                            fontSize: '12px',
                            color: '#666'
                        }
                    },
                        wp.element.createElement('strong', null, __('Preview do Filtro de Categorias', 'seisdeagosto')),
                        wp.element.createElement('br'),
                        __('Estilo: ', 'seisdeagosto') + displayStyle
                    ),
                    renderPreview()
                )
            );
        },

        save: function() {
            return null; // Server-side rendering
        }
    });

    console.log('[Category Filter] Block registered successfully');
})();
