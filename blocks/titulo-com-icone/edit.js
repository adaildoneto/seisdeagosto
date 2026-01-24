/**
 * Edit component for Título com Ícone block
 * Provides visual editor interface
 */

(function() {
    if (typeof wp === 'undefined') return;

    const { registerBlockType } = wp.blocks;
    const { RichText, InspectorControls, ColorPalette, useBlockProps } = wp.blockEditor || wp.editor;
    const { PanelBody, RangeControl, ToggleControl, SelectControl } = wp.components;
    const { __ } = wp.i18n;

    // Only register if not already registered
    if (wp.blocks.getBlockType('seisdeagosto/titulo-com-icone')) {
        console.log('[Título com Ícone] Block already registered, skipping');
        return;
    }

    registerBlockType('seisdeagosto/titulo-com-icone', {
        title: __('Título com Ícone', 'seisdeagosto'),
        icon: 'heading',
        category: 'seisdeagosto',
        attributes: {
            titulo: {
                type: 'string',
                default: 'CTA'
            },
            icone: {
                type: 'string',
                default: 'fa-star'
            },
            mostrarIcone: {
                type: 'boolean',
                default: true
            },
            corIcone: {
                type: 'string',
                default: '#fd7e14'
            },
            corLinha: {
                type: 'string',
                default: '#fd7e14'
            },
            tamanhoIcone: {
                type: 'number',
                default: 24
            },
            tamanhoTitulo: {
                type: 'number',
                default: 28
            },
            espessuraLinha: {
                type: 'number',
                default: 3
            },
            alinhamento: {
                type: 'string',
                default: 'left'
            }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { 
                titulo, icone, mostrarIcone, corIcone, corLinha,
                tamanhoIcone, tamanhoTitulo, espessuraLinha, alinhamento
            } = attributes;

            const blockProps = useBlockProps ? useBlockProps() : {};

            const alignClass = alinhamento === 'center' ? 'justify-content-center' : 
                             alinhamento === 'right' ? 'justify-content-end' : 
                             'justify-content-start';

            return wp.element.createElement(
                'div',
                blockProps,
                
                // Inspector Controls
                wp.element.createElement(
                    InspectorControls,
                    null,
                    
                    // Configurações Gerais
                    wp.element.createElement(
                        PanelBody,
                        { title: __('Configurações', 'seisdeagosto'), initialOpen: true },
                        
                        wp.element.createElement(ToggleControl, {
                            label: __('Mostrar Ícone', 'seisdeagosto'),
                            checked: mostrarIcone,
                            onChange: function(value) { setAttributes({ mostrarIcone: value }); }
                        }),
                        
                        wp.element.createElement(SelectControl, {
                            label: __('Alinhamento', 'seisdeagosto'),
                            value: alinhamento,
                            options: [
                                { label: __('Esquerda', 'seisdeagosto'), value: 'left' },
                                { label: __('Centro', 'seisdeagosto'), value: 'center' },
                                { label: __('Direita', 'seisdeagosto'), value: 'right' }
                            ],
                            onChange: function(value) { setAttributes({ alinhamento: value }); }
                        })
                    ),
                    
                    // Tamanhos
                    wp.element.createElement(
                        PanelBody,
                        { title: __('Tamanhos', 'seisdeagosto'), initialOpen: false },
                        
                        wp.element.createElement(RangeControl, {
                            label: __('Tamanho do Ícone', 'seisdeagosto'),
                            value: tamanhoIcone,
                            onChange: function(value) { setAttributes({ tamanhoIcone: value }); },
                            min: 16,
                            max: 64
                        }),
                        
                        wp.element.createElement(RangeControl, {
                            label: __('Tamanho do Título', 'seisdeagosto'),
                            value: tamanhoTitulo,
                            onChange: function(value) { setAttributes({ tamanhoTitulo: value }); },
                            min: 16,
                            max: 48
                        }),
                        
                        wp.element.createElement(RangeControl, {
                            label: __('Espessura da Linha', 'seisdeagosto'),
                            value: espessuraLinha,
                            onChange: function(value) { setAttributes({ espessuraLinha: value }); },
                            min: 1,
                            max: 10
                        })
                    ),
                    
                    // Cores
                    wp.element.createElement(
                        PanelBody,
                        { title: __('Cores', 'seisdeagosto'), initialOpen: false },
                        
                        wp.element.createElement('p', null, wp.element.createElement('strong', null, __('Cor do Ícone', 'seisdeagosto'))),
                        wp.element.createElement(ColorPalette, {
                            value: corIcone,
                            onChange: function(value) { setAttributes({ corIcone: value || '#fd7e14' }); }
                        }),
                        
                        wp.element.createElement('p', null, wp.element.createElement('strong', null, __('Cor da Linha', 'seisdeagosto'))),
                        wp.element.createElement(ColorPalette, {
                            value: corLinha,
                            onChange: function(value) { setAttributes({ corLinha: value || '#fd7e14' }); }
                        })
                    )
                ),
                
                // Block Preview
                wp.element.createElement(
                    'div',
                    { 
                        className: 'titulo-com-icone-wrapper d-flex align-items-center ' + alignClass,
                        style: { gap: '12px', padding: '16px 0' }
                    },
                    
                    // Icon
                    mostrarIcone && wp.element.createElement(
                        'div',
                        { className: 'titulo-com-icone-icon', style: { flexShrink: 0 } },
                        wp.element.createElement('i', {
                            className: 'fa ' + icone,
                            style: { 
                                fontSize: tamanhoIcone + 'px',
                                color: corIcone
                            }
                        })
                    ),
                    
                    // Title with line
                    wp.element.createElement(
                        'div',
                        { className: 'titulo-com-icone-content' },
                        wp.element.createElement(
                            'div',
                            { 
                                className: 'titulo-com-icone-line-wrapper',
                                style: { position: 'relative', display: 'inline-block' }
                            },
                            wp.element.createElement(RichText, {
                                tagName: 'h3',
                                className: 'titulo-com-icone-titulo m-0',
                                value: titulo,
                                onChange: function(value) { setAttributes({ titulo: value }); },
                                placeholder: __('Digite o título...', 'seisdeagosto'),
                                style: {
                                    fontSize: tamanhoTitulo + 'px',
                                    fontWeight: 700,
                                    margin: 0
                                }
                            }),
                            wp.element.createElement('div', {
                                className: 'titulo-com-icone-line',
                                style: {
                                    position: 'absolute',
                                    bottom: '-8px',
                                    left: 0,
                                    height: espessuraLinha + 'px',
                                    backgroundColor: corLinha,
                                    width: '80%',
                                    opacity: 0.7
                                }
                            })
                        )
                    )
                )
            );
        },

        save: function() {
            // Server-side rendering
            return null;
        }
    });

    console.log('[Título com Ícone] Block registered successfully');

})();
