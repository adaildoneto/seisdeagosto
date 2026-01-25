/**
 * Título com Ícone - Editor Block
 * Integração com Icon Picker usando wp.compose
 */

(function() {
    if (typeof wp === 'undefined') return;

    const { addFilter } = wp.hooks;
    const { Fragment } = wp.element;
    const { InspectorControls } = wp.blockEditor || wp.editor;
    const { PanelBody, TextControl, Button } = wp.components;
    const { useState } = wp.element;
    const { __ } = wp.i18n;
    const { createHigherOrderComponent } = wp.compose;

    // Adicionar controles customizados ao bloco
    const withIconPicker = createHigherOrderComponent(function(BlockEdit) {
        return function(props) {
            if (props.name !== 'seisdeagosto/titulo-com-icone') {
                return wp.element.createElement(BlockEdit, props);
            }

            const { attributes, setAttributes } = props;
            const { icone, corIcone, mostrarIcone } = attributes;

            const [showModal, setShowModal] = useState(false);
            const [icons, setIcons] = useState([]);
            const [search, setSearch] = useState('');
            const [loading, setLoading] = useState(false);

            const fallbackIcons = [
                { name: 'fa-star', label: 'Star' },
                { name: 'fa-heart', label: 'Heart' },
                { name: 'fa-home', label: 'Home' },
                { name: 'fa-user', label: 'User' },
                { name: 'fa-search', label: 'Search' },
                { name: 'fa-cog', label: 'Settings' }
            ];

            const loadIcons = function() {
                if (icons.length > 0) {
                    setShowModal(true);
                    return;
                }

                if (typeof jQuery === 'undefined') {
                    setIcons(fallbackIcons);
                    setShowModal(true);
                    return;
                }

                setLoading(true);
                
                // Get AJAX URL with fallback
                var ajaxUrl = '/wp-admin/admin-ajax.php';
                if (window.tituloComIconeData && window.tituloComIconeData.ajaxUrl) {
                    ajaxUrl = window.tituloComIconeData.ajaxUrl;
                } else if (window.seideagostoBlocks && window.seideagostoBlocks.ajaxUrl) {
                    // Fallback to main seideagostoBlocks if available
                    ajaxUrl = window.seideagostoBlocks.ajaxUrl;
                }
                
                jQuery.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'get_fontawesome_icons'
                    },
                    success: function(response) {
                        if (response && response.success && response.data && response.data.icons) {
                            setIcons(response.data.icons);
                        } else {
                            setIcons(fallbackIcons);
                        }
                        setShowModal(true);
                        setLoading(false);
                    },
                    error: function() {
                        setIcons(fallbackIcons);
                        setShowModal(true);
                        setLoading(false);
                    }
                });
            };

            const filtered = icons.filter(function(icon) {
                if (!search) return true;
                const s = search.toLowerCase();
                return icon.name.toLowerCase().indexOf(s) !== -1 || 
                       icon.label.toLowerCase().indexOf(s) !== -1;
            });

            return wp.element.createElement(
                Fragment,
                null,
                wp.element.createElement(BlockEdit, props),
                
                mostrarIcone && wp.element.createElement(
                    InspectorControls,
                    null,
                    wp.element.createElement(
                        PanelBody,
                        { title: __('Ícone Font Awesome', 'seisdeagosto'), initialOpen: false },
                        
                        wp.element.createElement('div', { style: { marginBottom: '12px' } },
                            wp.element.createElement('div', { style: { display: 'flex', gap: '8px' } },
                                wp.element.createElement(TextControl, {
                                    value: icone,
                                    onChange: function(v) { setAttributes({ icone: v }); },
                                    placeholder: 'fa-star'
                                }),
                                wp.element.createElement(Button, {
                                    isSecondary: true,
                                    onClick: loadIcons,
                                    disabled: loading
                                }, loading ? '...' : __('Escolher', 'seisdeagosto'))
                            )
                        ),
                        
                        wp.element.createElement('div', {
                            style: {
                                padding: '16px',
                                background: '#f0f0f0',
                                borderRadius: '4px',
                                textAlign: 'center'
                            }
                        },
                            wp.element.createElement('i', {
                                className: 'fa ' + icone,
                                style: { fontSize: '32px', color: corIcone }
                            }),
                            wp.element.createElement('div', {
                                style: { fontSize: '11px', marginTop: '8px', color: '#666' }
                            }, icone)
                        )
                    )
                ),
                
                showModal && wp.element.createElement('div', {
                    style: {
                        position: 'fixed',
                        top: 0,
                        left: 0,
                        right: 0,
                        bottom: 0,
                        background: 'rgba(0,0,0,0.7)',
                        zIndex: 100000,
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center'
                    },
                    onClick: function() { setShowModal(false); }
                },
                    wp.element.createElement('div', {
                        style: {
                            background: '#fff',
                            borderRadius: '8px',
                            width: '90%',
                            maxWidth: '700px',
                            maxHeight: '80vh',
                            display: 'flex',
                            flexDirection: 'column'
                        },
                        onClick: function(e) { e.stopPropagation(); }
                    },
                        wp.element.createElement('div', {
                            style: {
                                padding: '20px',
                                borderBottom: '1px solid #e0e0e0',
                                display: 'flex',
                                justifyContent: 'space-between'
                            }
                        },
                            wp.element.createElement('h3', { style: { margin: 0 } }, __('Selecione um Ícone', 'seisdeagosto')),
                            wp.element.createElement('button', {
                                onClick: function() { setShowModal(false); },
                                style: {
                                    background: 'none',
                                    border: 'none',
                                    fontSize: '24px',
                                    cursor: 'pointer'
                                }
                            }, '×')
                        ),
                        
                        wp.element.createElement('div', {
                            style: { padding: '16px 20px', borderBottom: '1px solid #e0e0e0' }
                        },
                            wp.element.createElement('input', {
                                type: 'text',
                                placeholder: __('Pesquisar...', 'seisdeagosto'),
                                value: search,
                                onChange: function(e) { setSearch(e.target.value); },
                                style: {
                                    width: '100%',
                                    padding: '10px',
                                    border: '1px solid #ddd',
                                    borderRadius: '4px'
                                }
                            })
                        ),
                        
                        wp.element.createElement('div', {
                            style: { padding: '20px', overflowY: 'auto', flex: 1 }
                        },
                            wp.element.createElement('div', {
                                style: {
                                    display: 'grid',
                                    gridTemplateColumns: 'repeat(auto-fill, minmax(100px, 1fr))',
                                    gap: '12px'
                                }
                            },
                                filtered.map(function(icon) {
                                    return wp.element.createElement('div', {
                                        key: icon.name,
                                        onClick: function() {
                                            setAttributes({ icone: icon.name });
                                            setShowModal(false);
                                            setSearch('');
                                        },
                                        style: {
                                            display: 'flex',
                                            flexDirection: 'column',
                                            alignItems: 'center',
                                            padding: '16px 8px',
                                            border: icone === icon.name ? '2px solid #0073aa' : '2px solid #e0e0e0',
                                            borderRadius: '6px',
                                            cursor: 'pointer',
                                            background: icone === icon.name ? '#0073aa' : '#fff',
                                            color: icone === icon.name ? '#fff' : '#333'
                                        }
                                    },
                                        wp.element.createElement('i', {
                                            className: 'fa ' + icon.name,
                                            style: { fontSize: '28px', marginBottom: '8px' }
                                        }),
                                        wp.element.createElement('span', {
                                            style: { fontSize: '11px', textAlign: 'center' }
                                        }, icon.label)
                                    );
                                })
                            )
                        )
                    )
                )
            );
        };
    }, 'withIconPicker');

    addFilter(
        'editor.BlockEdit',
        'seisdeagosto/with-icon-picker',
        withIconPicker
    );

})();
