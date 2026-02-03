( function( blocks, element, blockEditor, components ) {
    const el = element.createElement;
    const { InspectorControls, useBlockProps } = blockEditor;
    const { PanelBody, ToggleControl, TextControl, ColorPicker } = components;
    const { Fragment } = element;

    blocks.registerBlockType( 'seisdeagosto/mega-sena', {
        edit: function( props ) {
            const { attributes, setAttributes } = props;
            const {
                title,
                showConcurso,
                showData,
                showPremio,
                showProximoConcurso,
                showConcursoAnterior,
                showMenuJogos,
                jogoSelecionado,
                autoUpdate,
                backgroundColor,
                textColor,
                ballColor
            } = attributes;

            const blockProps = useBlockProps({
                style: {
                    backgroundColor: backgroundColor,
                    color: textColor
                }
            });

            return el(
                Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Configurações', initialOpen: true },
                        el( TextControl, {
                            label: 'Título',
                            value: title,
                            onChange: function( value ) {
                                setAttributes( { title: value } );
                            },
                            __nextHasNoMarginBottom: true
                        } ),
                        el( ToggleControl, {
                            label: 'Mostrar número do concurso',
                            checked: showConcurso,
                            onChange: function( value ) {
                                setAttributes( { showConcurso: value } );
                            },
                            __nextHasNoMarginBottom: true
                        } ),
                        el( ToggleControl, {
                            label: 'Mostrar data do sorteio',
                            checked: showData,
                            onChange: function( value ) {
                                setAttributes( { showData: value } );
                            },
                            __nextHasNoMarginBottom: true
                        } ),
                        el( ToggleControl, {
                            label: 'Mostrar prêmio',
                            checked: showPremio,
                            onChange: function( value ) {
                                setAttributes( { showPremio: value } );
                            },
                            __nextHasNoMarginBottom: true
                        } ),
                        el( ToggleControl, {
                            label: 'Mostrar próximo concurso',
                            checked: showProximoConcurso,
                            onChange: function( value ) {
                                setAttributes( { showProximoConcurso: value } );
                            },
                            __nextHasNoMarginBottom: true
                        } ),
                        el( ToggleControl, {
                            label: 'Mostrar concurso anterior',
                            checked: showConcursoAnterior,
                            onChange: function( value ) {
                                setAttributes( { showConcursoAnterior: value } );
                            },
                            __nextHasNoMarginBottom: true
                        } ),
                        el( ToggleControl, {
                            label: 'Mostrar menu de jogos',
                            checked: showMenuJogos,
                            onChange: function( value ) {
                                setAttributes( { showMenuJogos: value } );
                            },
                            __nextHasNoMarginBottom: true
                        } ),
                        el( ToggleControl, {
                            label: 'Atualização automática',
                            checked: autoUpdate,
                            onChange: function( value ) {
                                setAttributes( { autoUpdate: value } );
                            },
                            __nextHasNoMarginBottom: true
                        } )
                    ),
                    el(
                        PanelBody,
                        { title: 'Cores', initialOpen: false },
                        el( 'p', {}, 'Cor de fundo:' ),
                        el( ColorPicker, {
                            color: backgroundColor,
                            onChangeComplete: function( value ) {
                                setAttributes( { backgroundColor: value.hex } );
                            }
                        } ),
                        el( 'p', { style: { marginTop: '20px' } }, 'Cor do texto:' ),
                        el( ColorPicker, {
                            color: textColor,
                            onChangeComplete: function( value ) {
                                setAttributes( { textColor: value.hex } );
                            }
                        } ),
                        el( 'p', { style: { marginTop: '20px' } }, 'Cor das bolas:' ),
                        el( ColorPicker, {
                            color: ballColor,
                            onChangeComplete: function( value ) {
                                setAttributes( { ballColor: value.hex } );
                            }
                        } )
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el(
                        'div',
                        { className: 'mega-sena-header' },
                        el( 'h2', {},
                            el( 'i', { className: 'fas fa-trophy' } ),
                            ' ' + title
                        )
                    ),
                    el(
                        'div',
                        { className: 'mega-sena-loading' },
                        el( 'i', { className: 'fas fa-spinner fa-spin', style: { fontSize: '2rem' } } ),
                        el( 'p', { style: { marginTop: '1rem' } }, 'Carregando resultado...' )
                    ),
                    el(
                        'p',
                        { style: { textAlign: 'center', color: '#6c757d', fontSize: '0.875rem', marginTop: '1rem' } },
                        '💡 Preview será exibido no front-end'
                    )
                )
            );
        },

        save: function() {
            return null; // Dynamic block - rendering handled by PHP
        }
    } );
} )(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components
);
