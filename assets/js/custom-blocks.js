(function(wp) {
    // Ensure seideagostoBlocks exists with default values
    window.seideagostoBlocks = window.seideagostoBlocks || {
        categories: [{ value: 0, label: 'Todas as Categorias' }],
        sidebars: []
    };

    // Inject CSS for grid layout
    var styleSheet = document.createElement('style');
    styleSheet.innerHTML = `
        .wp-block-seideagosto-colunistas-grid {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 12px !important;
            width: 100% !important;
        }
        .wp-block-seideagosto-colunista-item {
            grid-column: span 1 !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        /* Editor-specific adjustments */
        .editor-styles-wrapper .wp-block-seideagosto-colunistas-grid {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 12px !important;
            width: 100% !important;
        }
        .editor-styles-wrapper .wp-block-seideagosto-colunista-item {
            grid-column: span 1 !important;
            width: 100% !important;
        }
    `;
    document.head.appendChild(styleSheet);

    var registerBlockType = wp.blocks.registerBlockType;
    var el = wp.element.createElement;
    var useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var InnerBlocks = wp.blockEditor.InnerBlocks;
    var MediaUpload = wp.blockEditor.MediaUpload;
    var SelectControl = wp.components.SelectControl;
    var TextControl = wp.components.TextControl;
    var PanelBody = wp.components.PanelBody;
    var RangeControl = wp.components.RangeControl;
    var ColorPalette = wp.components.ColorPalette;
    var Button = wp.components.Button;
    var ToggleControl = wp.components.ToggleControl;
    var ServerSideRender = wp.serverSideRender;
    var addFilter = wp.hooks && wp.hooks.addFilter ? wp.hooks.addFilter : function(){};

    var TYPOGRAPHY_DEFAULTS = {
        fontSize: 16,
        fontFamily: 'Arial, sans-serif',
        fontWeight: 'normal',
        titleColor: '#000000'
    };

    function getTypographyAttributes(defaultColor) {
        var color = defaultColor || TYPOGRAPHY_DEFAULTS.titleColor;
        return {
            fontSize: { type: 'number', default: TYPOGRAPHY_DEFAULTS.fontSize },
            fontFamily: { type: 'string', default: TYPOGRAPHY_DEFAULTS.fontFamily },
            fontWeight: { type: 'string', default: TYPOGRAPHY_DEFAULTS.fontWeight },
            titleColor: { type: 'string', default: color }
        };
    }

    function TypographyPanel(props, defaultColor) {
        var attributes = props.attributes;
        var setAttributes = props.setAttributes;

        var resolvedFontSize = typeof attributes.fontSize === 'number' ? attributes.fontSize : TYPOGRAPHY_DEFAULTS.fontSize;
        var resolvedFontFamily = attributes.fontFamily || TYPOGRAPHY_DEFAULTS.fontFamily;
        var resolvedFontWeight = attributes.fontWeight || TYPOGRAPHY_DEFAULTS.fontWeight;
        var resolvedColor = attributes.titleColor || defaultColor || TYPOGRAPHY_DEFAULTS.titleColor;

        return el(
            PanelBody,
            { title: 'Estilo dos Títulos', initialOpen: false },
            el(RangeControl, {
                label: 'Tamanho da Fonte (px)',
                value: resolvedFontSize,
                onChange: function(val) { setAttributes({ fontSize: val }); },
                min: 12,
                max: 48
            }),
            el(TextControl, {
                label: 'Familia da Fonte',
                value: resolvedFontFamily,
                onChange: function(val) { setAttributes({ fontFamily: val }); },
                help: 'Ex: Arial, sans-serif ou "Open Sans", sans-serif'
            }),
            el(SelectControl, {
                label: 'Peso da Fonte',
                value: resolvedFontWeight,
                options: [
                    { label: 'Normal', value: 'normal' },
                    { label: 'Bold', value: 'bold' },
                    { label: '300 (Light)', value: '300' },
                    { label: '400 (Regular)', value: '400' },
                    { label: '600 (Semibold)', value: '600' },
                    { label: '700 (Bold)', value: '700' },
                    { label: '900 (Black)', value: '900' }
                ],
                onChange: function(val) { setAttributes({ fontWeight: val }); }
            }),
            el(ColorPalette, {
                label: 'Cor do Título',
                value: resolvedColor,
                onChange: function(val) { setAttributes({ titleColor: val || resolvedColor }); }
            })
        );
    }

    // Destaques Home
    registerBlockType('seideagosto/destaques-home', {
        title: 'Destaques Home (1 Grande + 2 Pequenos)',
        icon: 'layout',
        category: 'layout',
        attributes: {
            categoryId: { type: 'string', default: '0' },
            layoutType: { type: 'string', default: 'default' }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Configurações' },
                        el(SelectControl, {
                            label: 'Layout',
                            value: attributes.layoutType || 'default',
                            options: [
                                { label: '1 Grande + 2 Pequenos', value: 'default' },
                                { label: 'Somente 1 Grande', value: 'single' }
                            ],
                            onChange: function(val) { setAttributes({ layoutType: String(val || 'default') }); }
                        }),
                        el(SelectControl, {
                            label: 'Categoria',
                            value: attributes.categoryId,
                            options: seideagostoBlocks.categories,
                            onChange: function(val) { setAttributes({ categoryId: String(val || '0') }); }
                        })
                    )
                ),
                el('div', {
                    style: {
                        border: '2px dashed #fd7e14',
                        borderRadius: '4px',
                        padding: '12px',
                        background: '#fff8f3',
                        color: '#555',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }
                },
                    el('div', { style: { fontWeight: 600, marginBottom: '8px', fontSize: '14px' } }, '🎨 Destaques Home'),
                    el('div', { style: { fontSize: '12px', color: '#666' } }, '1 Grande + 2 Pequenos'),
                    el('div', { style: { fontSize: '11px', color: '#999', marginTop: '8px' } }, '(Preview - ver no frontend)')
                )
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });

    // Colunistas Grid (lightweight editor preview)
    registerBlockType('seideagosto/colunistas-grid', {
        title: 'Grid de Colunistas',
        icon: 'groups',
        category: 'layout',
        attributes: {
            previewColumns: { type: 'number', default: 4 }
        },
        edit: function(props) {
            var attributes = props.attributes || {};
            var setAttributes = props.setAttributes;
            var cols = (typeof attributes.previewColumns === 'number' && attributes.previewColumns > 0) ? attributes.previewColumns : 4;
            
            var innerBlocksProps = useInnerBlocksProps(
                {
                    style: {
                        display: 'grid',
                        gridTemplateColumns: 'repeat(' + cols + ', 1fr)',
                        gap: '12px',
                        width: '100%',
                        marginTop: '12px'
                    }
                },
                {
                    allowedBlocks: ['seideagosto/colunista-item'],
                    template: [
                        ['seideagosto/colunista-item', { name: '', columnTitle: '', imageUrl: '', categoryId: '0' }],
                        ['seideagosto/colunista-item', { name: '', columnTitle: '', imageUrl: '', categoryId: '0' }],
                        ['seideagosto/colunista-item', { name: '', columnTitle: '', imageUrl: '', categoryId: '0' }],
                        ['seideagosto/colunista-item', { name: '', columnTitle: '', imageUrl: '', categoryId: '0' }]
                    ],
                    templateLock: false,
                    renderAppender: wp.blockEditor.InnerBlocks.ButtonBlockAppender
                }
            );
            
            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Layout', initialOpen: true },
                        el(RangeControl, {
                            label: 'Colunas',
                            value: cols,
                            min: 1,
                            max: 6,
                            onChange: function(val) { setAttributes({ previewColumns: val }); }
                        })
                    )
                ),
                el('div', {
                    style: {
                        border: '2px dashed #17a2b8',
                        borderRadius: '4px',
                        padding: '12px',
                        marginBottom: '12px',
                        background: '#f0f8fb',
                        color: '#555',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }
                },
                    el('div', { style: { fontWeight: 600, marginBottom: '8px', fontSize: '14px' } }, '👥 Grid de Colunistas'),
                    el('div', { style: { fontSize: '12px', color: '#666' } }, cols + ' colunas')
                ),
                el('div', innerBlocksProps)
            );
        },
        save: function() {
            return el(InnerBlocks.Content);
        }
    });

    // Colunista Item (lightweight editor preview)
    registerBlockType('seideagosto/colunista-item', {
        title: 'Colunista Item',
        icon: 'admin-users',
        category: 'layout',
        parent: ['seideagosto/colunistas-grid'],
        attributes: {
            name: { type: 'string', default: '' },
            columnTitle: { type: 'string', default: '' },
            imageUrl: { type: 'string', default: '' },
            categoryId: { type: 'string', default: '0' }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Dados do Colunista', initialOpen: true },
                        el(TextControl, {
                            label: 'Nome do Autor',
                            value: attributes.name,
                            onChange: function(val) { setAttributes({ name: val }); }
                        }),
                        el(TextControl, {
                            label: 'Título da Coluna',
                            value: attributes.columnTitle,
                            onChange: function(val) { setAttributes({ columnTitle: val }); }
                        }),
                        el(TextControl, {
                            label: 'URL da Imagem',
                            value: attributes.imageUrl,
                            onChange: function(val) { setAttributes({ imageUrl: val }); }
                        }),
                        el(SelectControl, {
                            label: 'Categoria dos Posts',
                            value: attributes.categoryId,
                            options: seideagostoBlocks.categories,
                            onChange: function(val) { setAttributes({ categoryId: String(val || '0') }); }
                        })
                    )
                ),
                el('div', {
                    className: 'seideagosto-colunista-item',
                    style: {
                        border: '2px dashed #6f42c1',
                        borderRadius: '8px',
                        padding: '16px 14px',
                        background: '#f9f5ff',
                        textAlign: 'center',
                        minHeight: '220px',
                        display: 'flex',
                        flexDirection: 'column',
                        alignItems: 'center',
                        justifyContent: 'center',
                        gap: '10px',
                        boxSizing: 'border-box'
                    }
                },
                    attributes.imageUrl ? el('img', { src: attributes.imageUrl, style: { width: '70px', height: '70px', borderRadius: '50%', objectFit: 'cover', display: 'block', flexShrink: 0 } }) : el('div', { style: { width: '70px', height: '70px', borderRadius: '50%', background: '#e0d5f0', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '32px', flexShrink: 0 } }, '👤'),
                    el('div', { style: { fontWeight: 600, fontSize: '14px', color: '#333', wordBreak: 'break-word', lineHeight: '1.3' } }, attributes.name || '(sem nome)'),
                    el('div', { style: { fontSize: '12px', color: '#666', wordBreak: 'break-word', lineHeight: '1.3' } }, attributes.columnTitle || '(sem coluna)')
                )
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });

    // News Grid (Grid de Notícias)
    registerBlockType('seideagosto/news-grid', {
        title: 'Grid de Notícias',
        icon: 'grid-view',
        category: 'layout',
        attributes: Object.assign({
            categoryId: { type: 'string', default: '0' },
            numberOfPosts: { type: 'number', default: 9 },
            offset: { type: 'number', default: 0 },
            columns: { type: 'number', default: 3 },
            paginate: { type: 'boolean', default: false }
        }, getTypographyAttributes()),
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Configurações' },
                        el(SelectControl, {
                            label: 'Categoria',
                            value: attributes.categoryId,
                            options: seideagostoBlocks.categories,
                            onChange: function(val) { setAttributes({ categoryId: String(val || '0') }); }
                        }),
                        el(RangeControl, {
                            label: 'Número de Posts',
                            value: attributes.numberOfPosts,
                            onChange: function(val) { setAttributes({ numberOfPosts: parseInt(val) }); },
                            min: 1,
                            max: 50
                        }),
                        el(RangeControl, {
                            label: 'Offset (Pular posts)',
                            value: attributes.offset,
                            onChange: function(val) { setAttributes({ offset: parseInt(val) }); },
                            min: 0,
                            max: 50
                        }),
                        el(RangeControl, {
                            label: 'Colunas',
                            value: attributes.columns,
                            onChange: function(val) { setAttributes({ columns: parseInt(val) }); },
                            min: 2,
                            max: 6
                        }),
                        el(ToggleControl, {
                            label: 'Mostrar paginação',
                            checked: !!attributes.paginate,
                            onChange: function(val) { setAttributes({ paginate: !!val }); }
                        })
                    ),
                    TypographyPanel(props)
                ),
                el('div', {
                    style: {
                        border: '2px dashed #6f42c1',
                        borderRadius: '4px',
                        padding: '12px',
                        background: '#f8f5ff',
                        color: '#555',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }
                },
                    el('div', { style: { fontWeight: 600, marginBottom: '8px', fontSize: '14px' } }, '📰 Grid de Notícias'),
                    el('div', { style: { fontSize: '12px', color: '#666', marginBottom: '6px' } }, attributes.numberOfPosts + ' posts • ' + attributes.columns + ' colunas'),
                    el('div', { style: { fontSize: '11px', color: '#999' } }, attributes.paginate ? '✓ Com paginação' : 'Sem paginação'),
                    el('div', { style: { fontSize: '11px', color: '#999', marginTop: '8px' } }, '(Preview - ver no frontend)')
                )
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });

    // Category Highlight (1 Big + 3 List)
    registerBlockType('seideagosto/category-highlight', {
        title: 'Destaque Categoria (1 Grande + 3 Lista)',
        icon: 'list-view',
        category: 'layout',
        supports: { inserter: false },
        attributes: Object.assign({
            categoryId: { type: 'string', default: '0' },
            title: { type: 'string', default: '' },
            bigCount: { type: 'number', default: 1 },
            listCount: { type: 'number', default: 3 }
        }, getTypographyAttributes()),
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Configurações' },
                        el(TextControl, {
                            label: 'Título da Seção',
                            value: attributes.title,
                            onChange: function(val) { setAttributes({ title: val }); }
                        }),
                        el(SelectControl, {
                            label: 'Categoria',
                            value: attributes.categoryId,
                            options: seideagostoBlocks.categories,
                            onChange: function(val) { setAttributes({ categoryId: String(val || '0') }); }
                        }),
                        el(RangeControl, {
                            label: 'Quantidade de Destaques Grandes',
                            value: attributes.bigCount,
                            onChange: function(val) { setAttributes({ bigCount: parseInt(val) }); },
                            min: 0,
                            max: 4
                        }),
                        el(RangeControl, {
                            label: 'Quantidade de Itens na Lista',
                            value: attributes.listCount,
                            onChange: function(val) { setAttributes({ listCount: parseInt(val) }); },
                            min: 0,
                            max: 12
                        })
                    ),
                    TypographyPanel(props)
                ),
                el('div', {
                    style: {
                        border: '2px dashed #e83e8c',
                        borderRadius: '4px',
                        padding: '12px',
                        background: '#fff5f8',
                        color: '#555',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }
                },
                    el('div', { style: { fontWeight: 600, marginBottom: '8px', fontSize: '14px' } }, '🎯 ' + (attributes.title || 'Destaque Categoria')),
                    el('div', { style: { fontSize: '12px', color: '#666', marginBottom: '6px' } }, attributes.bigCount + ' Grande + ' + attributes.listCount + ' Lista'),
                    el('div', { style: { fontSize: '11px', color: '#999', marginTop: '8px' } }, '(Preview - ver no frontend)')
                )
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });

    // Destaque Misto (2 Big + List + 1 Column)
    registerBlockType('seideagosto/destaque-misto', {
        title: 'Destaque Misto (2 Grandes + Lista + 1 Coluna)',
        icon: 'layout',
        category: 'layout',
        attributes: Object.assign({
            categoryId: { type: 'string', default: '0' },
            showList: { type: 'boolean', default: true },
            showListThumbs: { type: 'boolean', default: true },
            showBadges: { type: 'boolean', default: true }
        }, getTypographyAttributes('#FFFFFF')),
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Configurações' },
                        el(SelectControl, {
                            label: 'Categoria',
                            value: attributes.categoryId,
                            options: seideagostoBlocks.categories,
                            onChange: function(val) { setAttributes({ categoryId: String(val || '0') }); }
                        })
                    ),
                    TypographyPanel(props, '#FFFFFF'),
                    el(PanelBody, { title: 'Lista e Aparência', initialOpen: false },
                        el(wp.components.ToggleControl, {
                            label: 'Mostrar lista de matérias',
                            checked: attributes.showList,
                            onChange: function(val){ setAttributes({ showList: !!val }); }
                        }),
                        el(wp.components.ToggleControl, {
                            label: 'Mostrar fotos na lista',
                            checked: attributes.showListThumbs,
                            onChange: function(val){ setAttributes({ showListThumbs: !!val }); }
                        }),
                        el(wp.components.ToggleControl, {
                            label: 'Mostrar badges (categorias)',
                            checked: attributes.showBadges,
                            onChange: function(val){ setAttributes({ showBadges: !!val }); }
                        })
                    )
                ),
                el('div', {
                    style: {
                        border: '2px dashed #20c997',
                        borderRadius: '4px',
                        padding: '12px',
                        background: '#f0fef4',
                        color: '#555',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }
                },
                    el('div', { style: { fontWeight: 600, marginBottom: '8px', fontSize: '14px' } }, '🔀 Destaque Misto'),
                    el('div', { style: { fontSize: '12px', color: '#666' } }, '2 Grandes + Lista + 1 Coluna'),
                    el('div', { style: { fontSize: '11px', color: '#999', marginTop: '8px' } }, '(Preview - ver no frontend)')
                )
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });

    // Top Most Read (Top N)
    registerBlockType('seideagosto/top-most-read', {
        title: 'Top Mais Lidas',
        icon: 'chart-area',
        category: 'layout',
        supports: { inserter: false },
        attributes: {
            title: { type: 'string', default: 'Mais lidas' },
            count: { type: 'number', default: 5 },
            metaKey: { type: 'string', default: 'post_views_count' },
            categoryId: { type: 'string', default: '0' },
            period: { type: 'string', default: 'year' }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Configurações' },
                        el(TextControl, {
                            label: 'Título',
                            value: attributes.title,
                            onChange: function(val) { setAttributes({ title: val }); }
                        }),
                        el(SelectControl, {
                            label: 'Categoria (opcional)',
                            value: attributes.categoryId,
                            options: seideagostoBlocks.categories,
                            onChange: function(val) { setAttributes({ categoryId: String(val || '0') }); }
                        }),
                        el(SelectControl, {
                            label: 'Período',
                            value: attributes.period,
                            options: [
                                { label: 'Última semana', value: 'week' },
                                { label: 'Últimos 30 dias', value: '30days' },
                                { label: 'Últimos 90 dias', value: '90days' },
                                { label: 'Último ano', value: 'year' }
                            ],
                            onChange: function(val) { setAttributes({ period: val }); }
                        }),
                        el(RangeControl, {
                            label: 'Quantidade (Top N)',
                            value: attributes.count,
                            onChange: function(val) { setAttributes({ count: parseInt(val) }); },
                            min: 1,
                            max: 10
                        }),
                        el(TextControl, {
                            label: 'Meta Key (views)',
                            value: attributes.metaKey,
                            onChange: function(val) { setAttributes({ metaKey: val || 'post_views_count' }); },
                            help: 'Ex.: post_views_count (ajuste conforme seu plugin)'
                        })
                    )
                ),
                el('div', {
                    style: {
                        border: '2px dashed #0073aa',
                        borderRadius: '4px',
                        padding: '12px',
                        background: '#f0f6fc',
                        color: '#555',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }
                },
                    el('div', { style: { fontWeight: 600, marginBottom: '8px', fontSize: '14px' } }, '📊 ' + (attributes.title || 'Mais lidas')),
                    el('div', { style: { fontSize: '12px', color: '#666', marginBottom: '8px' } }, 'Período: ' + (attributes.period || 'year') + ' • Top ' + (attributes.count || 5)),
                    el('ul', { style: { margin: '8px 0', paddingLeft: '20px', fontSize: '12px' } },
                        Array.from({ length: Math.min(attributes.count || 5, 5) }).map(function(_, i) {
                            return el('li', { key: i, style: { marginBottom: '4px' } }, 'Notícia ' + (i + 1) + ' (preview - ver no frontend)');
                        })
                    )
                )
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });

    // Weather (Clima/Tempo)
    registerBlockType('seideagosto/weather', {
        title: 'Clima / Tempo',
        icon: 'cloud',
        category: 'widgets',
        supports: { inserter: false },
        attributes: {
            cityName: { type: 'string', default: '' },
            latitude: { type: 'string', default: '' },
            longitude: { type: 'string', default: '' },
            units: { type: 'string', default: 'c' }, // c or f
            showWind: { type: 'boolean', default: true },
            showRain: { type: 'boolean', default: true },
            forecastDays: { type: 'number', default: 5 }, // 3, 5, or 7 days
            showForecast: { type: 'boolean', default: true }, // Show/hide forecast
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Localização' },
                        el(TextControl, {
                            label: 'Cidade',
                            value: attributes.cityName,
                            onChange: function(val) { setAttributes({ cityName: val }); },
                            help: 'Ex.: Salvador, Lisboa, Madrid'
                        }),
                        el(TextControl, {
                            label: 'Latitude',
                            value: attributes.latitude,
                            onChange: function(val) { setAttributes({ latitude: val }); },
                            help: 'Opcional (sobrepõe cidade)'
                        }),
                        el(TextControl, {
                            label: 'Longitude',
                            value: attributes.longitude,
                            onChange: function(val) { setAttributes({ longitude: val }); },
                            help: 'Opcional (sobrepõe cidade)'
                        })
                    ),
                    el(
                        PanelBody,
                        { title: 'Exibição' },
                        el(SelectControl, {
                            label: 'Unidades',
                            value: attributes.units,
                            options: [
                                { label: 'Celsius (°C)', value: 'c' },
                                { label: 'Fahrenheit (°F)', value: 'f' }
                            ],
                            onChange: function(val) { setAttributes({ units: val }); }
                        }),
                        el(SelectControl, {
                            label: 'Mostrar Vento',
                            value: attributes.showWind ? 'yes' : 'no',
                            options: [
                                { label: 'Sim', value: 'yes' },
                                { label: 'Não', value: 'no' }
                            ],
                            onChange: function(val) { setAttributes({ showWind: val === 'yes' }); }
                        }),
                        el(SelectControl, {
                            label: 'Mostrar Chuva',
                            value: attributes.showRain ? 'yes' : 'no',
                            options: [
                                { label: 'Sim', value: 'yes' },
                                { label: 'Não', value: 'no' }
                            ],
                            onChange: function(val) { setAttributes({ showRain: val === 'yes' }); }
                        }),
                        el(SelectControl, {
                            label: 'Mostrar Previsão',
                            value: attributes.showForecast ? 'yes' : 'no',
                            options: [
                                { label: 'Sim', value: 'yes' },
                                { label: 'Não', value: 'no' }
                            ],
                            onChange: function(val) { setAttributes({ showForecast: val === 'yes' }); }
                        }),
                        attributes.showForecast ? el(SelectControl, {
                            label: 'Dias de Previsão',
                            value: attributes.forecastDays.toString(),
                            options: [
                                { label: '3 dias', value: '3' },
                                { label: '5 dias', value: '5' },
                                { label: '7 dias', value: '7' }
                            ],
                            onChange: function(val) { setAttributes({ forecastDays: parseInt(val) }); }
                        }) : null
                    )
                ),
                el('div', {
                    style: {
                        border: '2px dashed #ffc107',
                        borderRadius: '4px',
                        padding: '12px',
                        background: '#fffbf0',
                        color: '#555',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }
                },
                    el('div', { style: { fontWeight: 600, marginBottom: '8px', fontSize: '14px' } }, '☀️ Clima / Tempo'),
                    el('div', { style: { fontSize: '12px', color: '#666', marginBottom: '8px' } }, 
                        (attributes.cityName || 'Localização não configurada') + ' • ' + (attributes.units === 'f' ? '°F' : '°C')
                    ),
                    el('div', { style: { fontSize: '11px', color: '#999', lineHeight: '1.4' } },
                        'Vento: ' + (attributes.showWind ? '✓' : '✗') + ' | ',
                        'Chuva: ' + (attributes.showRain ? '✓' : '✗') + ' | ',
                        'Previsão: ' + (attributes.showForecast ? (attributes.forecastDays + 'd') : '✗'),
                        el('div', { style: { marginTop: '8px' } }, '(Preview - ver no frontend)')
                    )
                )
            );
        },
        save: function() { return null; }
    });

    // Currency Monitor
    var ToggleControl = wp.components.ToggleControl;
    registerBlockType('seideagosto/currency-monitor', {
        title: 'Monitor de Câmbio',
        icon: 'money',
        category: 'widgets',
        supports: { inserter: true },
        attributes: {
            provider: { type: 'string', default: 'currencyfreaks' },
            base: { type: 'string', default: 'BRL' },
            baseAmount: { type: 'number', default: 100 },
            showBRL: { type: 'boolean', default: true },
            showUSD: { type: 'boolean', default: true },
            showEUR: { type: 'boolean', default: true },
            showPEN: { type: 'boolean', default: true },
            showARS: { type: 'boolean', default: true },
            showBOB: { type: 'boolean', default: true },
            showCLP: { type: 'boolean', default: false },
            showCOP: { type: 'boolean', default: false },
            showUYU: { type: 'boolean', default: false },
            showPYG: { type: 'boolean', default: false },
            showMXN: { type: 'boolean', default: false },
            spread: { type: 'number', default: 0 },
            showUpdated: { type: 'boolean', default: true },
            slidesToShow: { type: 'number', default: 2 },
            autoplay: { type: 'boolean', default: true },
            autoplaySpeed: { type: 'number', default: 3000 },
            showFlags: { type: 'boolean', default: true },
            showNames: { type: 'boolean', default: true }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Moedas' },
                        el(ToggleControl, {
                            label: 'Real Brasileiro (BRL)',
                            checked: !!attributes.showBRL,
                            onChange: function(val) { setAttributes({ showBRL: !!val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Dólar Americano (USD)',
                            checked: !!attributes.showUSD,
                            onChange: function(val) { setAttributes({ showUSD: !!val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Euro (EUR)',
                            checked: !!attributes.showEUR,
                            onChange: function(val) { setAttributes({ showEUR: !!val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Sol Peruano (PEN)',
                            checked: !!attributes.showPEN,
                            onChange: function(val) { setAttributes({ showPEN: !!val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Peso Argentino (ARS)',
                            checked: !!attributes.showARS,
                            onChange: function(val) { setAttributes({ showARS: !!val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Boliviano (BOB)',
                            checked: !!attributes.showBOB,
                            onChange: function(val) { setAttributes({ showBOB: !!val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Peso Chileno (CLP)',
                            checked: !!attributes.showCLP,
                            onChange: function(val) { setAttributes({ showCLP: !!val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Peso Colombiano (COP)',
                            checked: !!attributes.showCOP,
                            onChange: function(val) { setAttributes({ showCOP: !!val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Peso Uruguaio (UYU)',
                            checked: !!attributes.showUYU,
                            onChange: function(val) { setAttributes({ showUYU: !!val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Guaraní (PYG)',
                            checked: !!attributes.showPYG,
                            onChange: function(val) { setAttributes({ showPYG: !!val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Peso Mexicano (MXN)',
                            checked: !!attributes.showMXN,
                            onChange: function(val) { setAttributes({ showMXN: !!val }); }
                        })
                    ),
                    el(
                        PanelBody,
                        { title: 'Configuração' },
                        el(RangeControl, {
                            label: 'Valor base em BRL',
                            value: attributes.baseAmount,
                            onChange: function(val) { setAttributes({ baseAmount: parseInt(val) }); },
                            min: 1,
                            max: 1000,
                            step: 1
                        }),
                        // Fonte fixa: currencyfreaks (chave em wp-config ou env)
                        el(SelectControl, {
                            label: 'Base',
                            value: attributes.base,
                            options: [
                                { label: 'Real Brasileiro (BRL)', value: 'BRL' }
                            ],
                            onChange: function(val) { setAttributes({ base: val }); }
                        }),
                        el(RangeControl, {
                            label: 'Spread (%)',
                            value: attributes.spread,
                            onChange: function(val) { setAttributes({ spread: parseFloat(val) }); },
                            min: 0,
                            max: 5,
                            step: 0.1
                        }),
                        el(ToggleControl, {
                            label: 'Mostrar data de atualização',
                            checked: !!attributes.showUpdated,
                            onChange: function(val) { setAttributes({ showUpdated: !!val }); }
                        }),
                        el(RangeControl, {
                            label: 'Slides visíveis',
                            value: attributes.slidesToShow,
                            onChange: function(val) { setAttributes({ slidesToShow: parseInt(val) }); },
                            min: 1,
                            max: 5,
                            step: 1
                        }),
                        el(ToggleControl, {
                            label: 'Autoplay',
                            checked: !!attributes.autoplay,
                            onChange: function(val) { setAttributes({ autoplay: !!val }); }
                        }),
                        el(RangeControl, {
                            label: 'Velocidade do Autoplay (ms)',
                            value: attributes.autoplaySpeed,
                            onChange: function(val) { setAttributes({ autoplaySpeed: parseInt(val) }); },
                            min: 1000,
                            max: 10000,
                            step: 500
                        })
                    ),
                    el(
                        PanelBody,
                        { title: 'Exibição' },
                        el(ToggleControl, {
                            label: 'Mostrar bandeiras',
                            checked: !!attributes.showFlags,
                            onChange: function(val) { setAttributes({ showFlags: !!val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Mostrar nomes das moedas',
                            checked: !!attributes.showNames,
                            onChange: function(val) { setAttributes({ showNames: !!val }); }
                        })
                    )
                ),
                el('div', {
                    style: {
                        border: '2px dashed #28a745',
                        borderRadius: '4px',
                        padding: '12px',
                        background: '#f0fdf4',
                        color: '#555',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }
                },
                    el('div', { style: { fontWeight: 600, marginBottom: '8px', fontSize: '14px' } }, '💱 Câmbio: ' + attributes.baseAmount + ' BRL'),
                    el('div', { style: { fontSize: '11px', color: '#666', marginBottom: '8px' } }, 
                        (attributes.showUSD ? 'USD ' : '') +
                        (attributes.showEUR ? 'EUR ' : '') +
                        (attributes.showPEN ? 'PEN ' : '') +
                        (attributes.showARS ? 'ARS ' : '') +
                        (attributes.showBOB ? 'BOB' : '')
                    ),
                    el('div', { style: { fontSize: '11px', color: '#999' } }, '(Preview - ver no frontend)')
                )
            );
        },
        save: function() { return null; }
    });

    // Sidebar Area (Widget Area Renderer)
    registerBlockType('seideagosto/sidebar-area', {
        title: 'Área de Widgets (Sidebar)',
        icon: 'screenoptions',
        category: 'widgets',
        attributes: {
            sidebarId: { type: 'string', default: 'right-sidebar' },
            title: { type: 'string', default: '' }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var sidebarOptions = (typeof seideagostoBlocks !== 'undefined' && seideagostoBlocks.sidebars) ? seideagostoBlocks.sidebars : [];

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Configurações' },
                        el(SelectControl, {
                            label: 'Sidebar',
                            value: attributes.sidebarId,
                            options: sidebarOptions,
                            onChange: function(val) { setAttributes({ sidebarId: val }); }
                        }),
                        el(TextControl, {
                            label: 'Título (opcional)',
                            value: attributes.title,
                            onChange: function(val) { setAttributes({ title: val }); }
                        })
                    )
                ),
                el('div', {
                    style: {
                        border: '2px dashed #dc3545',
                        borderRadius: '4px',
                        padding: '12px',
                        background: '#fff5f5',
                        color: '#555',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }
                },
                    el('div', { style: { fontWeight: 600, marginBottom: '8px', fontSize: '14px' } }, '📦 Área de Widgets'),
                    el('div', { style: { fontSize: '12px', color: '#666', marginBottom: '6px' } }, 'Sidebar: ' + attributes.sidebarId),
                    el('div', { style: { fontSize: '11px', color: '#999', marginTop: '8px' } }, '(Preview - ver no frontend)')
                )
            );
        },
        save: function() { return null; }
    });

    // Lightweight previews for metadata-registered blocks (no ServerSideRender)
    function lightPreview(name, props) {
        var a = props.attributes || {};
        var header = '📰 Bloco do Tema';
        if (name === 'u-correio68/destaque-grande') header = '⭐ Destaque Grande';
        if (name === 'u-correio68/destaque-pequeno') header = '⭐ Destaque Pequeno';
        if (name === 'u-correio68/lista-noticias') header = '🗂️ Lista de Notícias';
        var lines = [];
        if (typeof a.numberOfPosts !== 'undefined') lines.push('Posts: ' + a.numberOfPosts);
        if (typeof a.offset !== 'undefined') lines.push('Offset: ' + a.offset);
        if (typeof a.columns !== 'undefined') lines.push('Colunas: ' + a.columns);
        if (typeof a.category !== 'undefined' && a.category) lines.push('Categoria: ' + a.category);
        return el('div', {
            style: {
                border: '2px dashed #17a2b8',
                borderRadius: '4px',
                padding: '12px',
                background: '#f0fcff',
                color: '#444',
                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
            }
        },[
            el('div', { style: { fontWeight: 600, marginBottom: '8px', fontSize: '14px' } }, header),
            lines.length ? el('div', { style: { fontSize: '12px', color: '#555' } }, lines.join(' · ')) : null,
            el('div', { style: { fontSize: '11px', color: '#999', marginTop: '8px' } }, '(Preview leve - ver no frontend)')
        ]);
    }

    try {
        addFilter('blocks.registerBlockType', 'seisdeagosto/light-previews', function(settings, name) {
            var targets = [
                'u-correio68/destaque-grande',
                'u-correio68/destaque-pequeno',
                'u-correio68/lista-noticias'
            ];
            if (targets.indexOf(name) !== -1) {
                settings.edit = function(props) { return lightPreview(name, props); };
                settings.save = function() { return null; };
            }
            return settings;
        });
    } catch (e) {
        // no-op if hooks not available
    }

    // Fallback: if blocks already registered before filters, override via domReady
    wp.domReady(function() {
        var targets = [
            'u-correio68/destaque-grande',
            'u-correio68/destaque-pequeno',
            'u-correio68/lista-noticias'
        ];
        targets.forEach(function(name) {
            var existing = wp.blocks.getBlockType(name);
            if (existing) {
                try {
                    wp.blocks.unregisterBlockType(name);
                    var merged = Object.assign({}, existing, {
                        edit: function(props) { return lightPreview(name, props); },
                        save: function() { return null; }
                    });
                    wp.blocks.registerBlockType(name, merged);
                } catch (err) {
                    // ignore
                }
            }
        });
    });

    // ============================================================================
    // CONTENT MIGRATION: Auto-upgrade old namespace blocks to new namespace
    // ============================================================================
    // Automatically convert old namespace blocks in content to new namespace
    // This runs on editor initialization and transparently migrates content
    try {
        addFilter('blocks.registerBlockType', 'seideagosto/migrate-old-blocks', function(settings, name) {
            // Ensure name is a string
            if (typeof name !== 'string') {
                return settings;
            }
            
            var oldNamespaces = ['u-correio68/', 'correio68/'];
            var isOld = oldNamespaces.some(function(ns) { return name.indexOf(ns) === 0; });
            
            if (isOld) {
                // Mark as non-insertable - will only render existing content
                if (!settings.supports) {
                    settings.supports = {};
                }
                settings.supports.inserter = false;
                
                // Modify title to indicate it's legacy
                settings.title = settings.title + ' (legacy - use new version)';
            }
            
            return settings;
        });
    } catch (e) {
        // Filter not available, skip
    }

    // Image Slider Block
    registerBlockType('seideagosto/image-slider', {
        title: 'Galeria em Slider (Slick)',
        icon: 'images-alt2',
        category: 'media',
        attributes: {
            images: { type: 'array', default: [] },
            speed: { type: 'number', default: 3000 },
            autoplaySpeed: { type: 'number', default: 5000 },
            vertical: { type: 'boolean', default: false },
            rtl: { type: 'boolean', default: false },
            fade: { type: 'boolean', default: false },
            autoplay: { type: 'boolean', default: true },
            pauseOnHover: { type: 'boolean', default: true },
            slidesToShow: { type: 'number', default: 1 },
            slidesToScroll: { type: 'number', default: 1 }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var images = attributes.images || [];
            
            return el(
                'div',
                {},
                el(
                    InspectorControls,
                    {},
                    el(PanelBody, { title: 'Configurações do Slider', initialOpen: true },
                        el(RangeControl, {
                            label: 'Velocidade de Transição (ms)',
                            value: attributes.speed,
                            onChange: function(val) { setAttributes({ speed: val }); },
                            min: 300,
                            max: 5000,
                            step: 100
                        }),
                        el(RangeControl, {
                            label: 'Velocidade do Autoplay (ms)',
                            value: attributes.autoplaySpeed,
                            onChange: function(val) { setAttributes({ autoplaySpeed: val }); },
                            min: 1000,
                            max: 10000,
                            step: 500
                        }),
                        el(ToggleControl, {
                            label: 'Autoplay',
                            checked: attributes.autoplay,
                            onChange: function(val) { setAttributes({ autoplay: val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Pausar ao passar o mouse',
                            checked: attributes.pauseOnHover,
                            onChange: function(val) { setAttributes({ pauseOnHover: val }); }
                        }),
                        el(RangeControl, {
                            label: 'Slides visíveis',
                            value: attributes.slidesToShow,
                            onChange: function(val) { setAttributes({ slidesToShow: val }); },
                            min: 1,
                            max: 5
                        }),
                        el(RangeControl, {
                            label: 'Slides a rolar',
                            value: attributes.slidesToScroll,
                            onChange: function(val) { setAttributes({ slidesToScroll: val }); },
                            min: 1,
                            max: 5
                        })
                    ),
                    el(PanelBody, { title: 'Efeitos de Animação', initialOpen: false },
                        el(ToggleControl, {
                            label: 'Direção Vertical',
                            checked: attributes.vertical,
                            onChange: function(val) { setAttributes({ vertical: val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Direita para Esquerda (RTL)',
                            checked: attributes.rtl,
                            onChange: function(val) { setAttributes({ rtl: val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Efeito Fade',
                            checked: attributes.fade,
                            onChange: function(val) { 
                                if (val) {
                                    setAttributes({ 
                                        fade: val,
                                        slidesToShow: 1,
                                        slidesToScroll: 1
                                    }); 
                                } else {
                                    setAttributes({ fade: val }); 
                                }
                            },
                            help: 'Ativa fade, força 1 slide visível'
                        })
                    ),
                    el(PanelBody, { title: 'Imagens', initialOpen: true },
                        el(
                            MediaUpload,
                            {
                                onSelect: function(media) {
                                    var newImages = media.map(function(img) {
                                        return {
                                            id: img.id,
                                            url: img.url,
                                            alt: img.alt || '',
                                            link: ''
                                        };
                                    });
                                    setAttributes({ images: newImages });
                                },
                                allowedTypes: ['image'],
                                multiple: true,
                                gallery: true,
                                value: images.map(function(img) { return img.id; }),
                                render: function(obj) {
                                    return el(
                                        Button,
                                        { 
                                            onClick: obj.open,
                                            variant: 'primary',
                                            style: { marginBottom: '10px', width: '100%' }
                                        },
                                        images.length > 0 ? '➕ Editar Imagens (' + images.length + ')' : '➕ Adicionar Imagens'
                                    );
                                }
                            }
                        ),
                        images.length > 0 && el(
                            'div',
                            { style: { display: 'grid', gridTemplateColumns: '1fr', gap: '10px', marginTop: '10px' } },
                            images.map(function(image, index) {
                                return el(
                                    'div',
                                    { key: index, style: { position: 'relative', border: '1px solid #ddd', padding: '8px', borderRadius: '4px', background: '#fff' } },
                                    el('img', { 
                                        src: image.url, 
                                        alt: image.alt,
                                        style: { width: '100%', height: 'auto', display: 'block', marginBottom: '8px', borderRadius: '2px' }
                                    }),
                                    el(TextControl, {
                                        label: 'Link (opcional)',
                                        value: image.link || '',
                                        onChange: function(val) {
                                            var newImages = images.slice();
                                            newImages[index].link = val;
                                            setAttributes({ images: newImages });
                                        },
                                        placeholder: 'https://...'
                                    }),
                                    el(Button, {
                                        onClick: function() {
                                            var newImages = images.filter(function(img, i) { return i !== index; });
                                            setAttributes({ images: newImages });
                                        },
                                        variant: 'secondary',
                                        isDestructive: true,
                                        style: { marginTop: '5px', width: '100%', fontSize: '11px' }
                                    }, '🗑️ Remover')
                                );
                            })
                        ),
                        images.length === 0 && el('div', { 
                            style: { 
                                fontSize: '12px', 
                                color: '#999', 
                                marginTop: '8px',
                                fontStyle: 'italic',
                                textAlign: 'center',
                                padding: '8px'
                            } 
                        }, 'Nenhuma imagem adicionada')
                    )
                ),
                el(
                    'div',
                    { 
                        className: 'image-slider-editor', 
                        style: { 
                            border: '2px dashed #6c757d',
                            borderRadius: '4px',
                            padding: '12px',
                            background: '#f8f9fa',
                            color: '#444',
                            fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                        } 
                    },
                    el('div', { style: { fontWeight: 600, marginBottom: '8px', fontSize: '14px' } }, '🎞️ Galeria em Slider (Slick)'),
                    images.length > 0 ? el('div', { style: { fontSize: '12px', color: '#555' } }, 
                        images.length + ' imagem' + (images.length !== 1 ? 'ns' : '') + ' · ' +
                        'Autoplay: ' + (attributes.autoplay ? 'Sim' : 'Não') + ' · ' +
                        'Velocidade: ' + attributes.autoplaySpeed + 'ms'
                    ) : el('div', { 
                        style: { 
                            fontSize: '11px', 
                            color: '#999', 
                            marginTop: '8px',
                            fontStyle: 'italic'
                        } 
                    }, '(Configure as imagens no painel lateral →)')
                )
            );
        },
        save: function() {
            return null;
        }
    });

})(window.wp);