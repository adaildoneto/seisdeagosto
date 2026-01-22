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
    var useBlockProps = wp.blockEditor && wp.blockEditor.useBlockProps ? wp.blockEditor.useBlockProps : null;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var InnerBlocks = wp.blockEditor.InnerBlocks;
    var MediaUpload = wp.blockEditor.MediaUpload;
    var MediaUploadCheck = wp.blockEditor.MediaUploadCheck || null;
    var SelectControl = wp.components.SelectControl;
    var ComboboxControl = wp.components.ComboboxControl || null;
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
        // ...existing code...
                    wp.apiFetch({ path: '/wp/v2/' + tax + '?per_page=100' }).then(terms => {
                        if (!mounted) return;
                        let options = [{ label: 'Todas', value: '0' }];
                        if (Array.isArray(terms)) {
                            options = options.concat(terms.map(term => ({ label: term.name, value: String(term.id) })));
                        }
                        setCategories(options);
                    }).catch(() => {
                        setCategories([{ label: 'Nenhuma categoria encontrada', value: '' }]);
                    });
                });
                return () => { mounted = false; };
            }, [attributes.postType]);

            return React.createElement(
                React.Fragment,
                null,
                React.createElement(
                    InspectorControls,
                    null,
                    React.createElement(
                        PanelBody,
                        { title: 'Configurações' },
                        React.createElement(SelectControl, {
                            label: 'Tipo de Post',
                            value: attributes.postType || 'post',
                            options: postTypes.length ? postTypes : [ { label: 'Carregando...', value: '' } ],
                            onChange: val => setAttributes({ postType: val, categoryId: '0' })
                        }),
                        React.createElement(SelectControl, {
                            label: 'Categoria',
                            value: attributes.categoryId,
                            options: categories.length ? categories : [ { label: 'Carregando...', value: '' } ],
                            onChange: val => setAttributes({ categoryId: String(val || '0') })
                        }),
                        React.createElement(RangeControl, {
                            label: 'Número de Posts',
                            value: attributes.numberOfPosts,
                            onChange: val => setAttributes({ numberOfPosts: parseInt(val) }),
                            min: 1,
                            max: 50
                        }),
                        React.createElement(RangeControl, {
                            label: 'Colunas',
                            value: attributes.columns,
                            onChange: val => setAttributes({ columns: parseInt(val) }),
                            min: 2,
                            max: 6
                        }),
                        React.createElement(ToggleControl, {
                            label: 'Mostrar paginação',
                            checked: !!attributes.paginate,
                            onChange: val => setAttributes({ paginate: !!val })
                        })
                    ),
                    QueryFiltersPanel(props),
                    TypographyPanel(props)
                ),
                React.createElement('div', {
                    style: {
                        border: '2px dashed #6f42c1',
                        borderRadius: '4px',
                        padding: '12px',
                        background: '#f8f5ff',
                        color: '#555',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }
                },
                    React.createElement('div', { style: { fontWeight: 600, marginBottom: '8px', fontSize: '14px' } }, '📰 Grid de Notícias'),
                    React.createElement('div', { style: { fontSize: '12px', color: '#666', marginBottom: '6px' } }, attributes.numberOfPosts + ' posts • ' + attributes.columns + ' colunas'),
                    React.createElement('div', { style: { fontSize: '11px', color: '#999' } }, attributes.paginate ? '✓ Com paginação' : 'Sem paginação'),
                    React.createElement('div', { style: { fontSize: '11px', color: '#999', marginTop: '8px' } }, '(Preview - ver no frontend)')
                )
            );
        },
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
                    ),
                    QueryFiltersPanel(props)
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
        category: 'seisdeagosto',
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
        category: 'seisdeagosto',
        parent: ['seideagosto/colunistas-grid'],
        attributes: {
            name: { type: 'string', default: '' },
            columnTitle: { type: 'string', default: '' },
            imageUrl: { type: 'string', default: '' },
            imageId: { type: 'number', default: 0 },
            categoryId: { type: 'string', default: '0' }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var hasImage = !!attributes.imageUrl || !!attributes.imageId;
            var onSelectImage = function(media) {
                var url = media && media.url ? media.url : '';
                var id = media && media.id ? media.id : 0;
                setAttributes({ imageUrl: url, imageId: id });
            };
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
                        el('div', { style: { marginTop: '8px' } },
                            MediaUploadCheck ?
                                el(MediaUploadCheck, null,
                                    el(MediaUpload, {
                                        onSelect: onSelectImage,
                                        allowedTypes: ['image'],
                                        value: attributes.imageId,
                                        render: function(obj) {
                                            return el(Button, { onClick: obj.open, isSecondary: true }, hasImage ? 'Trocar imagem' : 'Selecionar imagem');
                                        }
                                    })
                                ) :
                                el(MediaUpload, {
                                    onSelect: onSelectImage,
                                    allowedTypes: ['image'],
                                    value: attributes.imageId,
                                    render: function(obj) {
                                        return el(Button, { onClick: obj.open, isSecondary: true }, hasImage ? 'Trocar imagem' : 'Selecionar imagem');
                                    }
                                }),
                            hasImage ? el(Button, {
                                isLink: true,
                                isDestructive: true,
                                style: { marginLeft: '8px' },
                                onClick: function() { setAttributes({ imageUrl: '', imageId: 0 }); }
                            }, 'Remover') : null
                        ),
                        el(TextControl, {
                            label: 'URL da Imagem (opcional)',
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
        category: 'seisdeagosto',
        attributes: Object.assign({
            postType: { type: 'string', default: 'post' },
            categoryId: { type: 'string', default: '0' },
            categoryIds: { type: 'array', default: [] },
            excludeCategories: { type: 'string', default: '' },
            numberOfPosts: { type: 'number', default: 9 },
            offset: { type: 'number', default: 0 },
            columns: { type: 'number', default: 3 },
            paginate: { type: 'boolean', default: false },
            tags: { type: 'string', default: '' },
            keyword: { type: 'string', default: '' }
        }, getTypographyAttributes()),
        edit: function(props) {

            // Use React hooks directly to avoid re-creating hooks on every render
            var React = wp.element;
            var _useState = React.useState([]), postTypes = _useState[0], setPostTypes = _useState[1];
            var _useState2 = React.useState([]), categories = _useState2[0], setCategories = _useState2[1];

            React.useEffect(function() {
                let mounted = true;
                wp.apiFetch({ path: '/wp/v2/types' }).then(function(types) {
                    if (!mounted) return;
                    var options = Object.keys(types)
                        .filter(function(key) { return types[key].viewable && types[key].slug !== 'attachment'; })
                        .map(function(key) {
                            return { label: types[key].name, value: types[key].slug };
                        });
                    setPostTypes(options);
                });
                return function() { mounted = false; };
            }, []);

            React.useEffect(function() {
                if (!attributes.postType) return;
                let mounted = true;
                wp.apiFetch({ path: '/wp/v2/types/' + attributes.postType }).then(function(type) {
                    if (!mounted) return;
                    if (!type.taxonomies || !type.taxonomies.length) {
                        setCategories([{ label: 'Nenhuma categoria disponível', value: '' }]);
                        return;
                    }
                    var tax = type.taxonomies[0];
                    wp.apiFetch({ path: '/wp/v2/' + tax + '?per_page=100' }).then(function(terms) {
                        if (!mounted) return;
                        var options = [{ label: 'Todas', value: '0' }];
                        if (Array.isArray(terms)) {
                            options = options.concat(terms.map(function(term) {
                                return { label: term.name, value: String(term.id) };
                            }));
                        }
                        setCategories(options);
                    }).catch(function() {
                        setCategories([{ label: 'Nenhuma categoria encontrada', value: '' }]);
                    });
                });
                return function() { mounted = false; };
            }, [attributes.postType]);

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
                            label: 'Tipo de Post',
                            value: attributes.postType || 'post',
                            options: postTypes.length ? postTypes : [ { label: 'Carregando...', value: '' } ],
                            onChange: function(val) { setAttributes({ postType: val, categoryId: '0' }); }
                        }),
                        el(SelectControl, {
                            label: 'Categoria',
                            value: attributes.categoryId,
                            options: categories.length ? categories : [ { label: 'Carregando...', value: '' } ],
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
                    QueryFiltersPanel(props),
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
        category: 'seisdeagosto',
        attributes: Object.assign({
            categoryId: { type: 'string', default: '0' },
            categoryIds: { type: 'array', default: [] },
            excludeCategories: { type: 'string', default: '' },
            title: { type: 'string', default: '' },
            bigCount: { type: 'number', default: 1 },
            listCount: { type: 'number', default: 3 },
            offset: { type: 'number', default: 0 },
            showListThumbs: { type: 'boolean', default: true },
            tags: { type: 'string', default: '' },
            keyword: { type: 'string', default: '' }
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
                        }),
                        el(ToggleControl, {
                            label: 'Mostrar fotos na lista',
                            checked: attributes.showListThumbs !== false,
                            onChange: function(val) { setAttributes({ showListThumbs: !!val }); }
                        })
                    ),
                    QueryFiltersPanel(props),
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
        category: 'seisdeagosto',
        attributes: Object.assign({
            categoryId: { type: 'string', default: '0' },
            categoryIds: { type: 'array', default: [] },
            excludeCategories: { type: 'string', default: '' },
            offset: { type: 'number', default: 0 },
            showHighlights: { type: 'boolean', default: true },
            showList: { type: 'boolean', default: true },
            showListThumbs: { type: 'boolean', default: true },
            showBadges: { type: 'boolean', default: true },
            tags: { type: 'string', default: '' },
            keyword: { type: 'string', default: '' }
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
                    QueryFiltersPanel(props),
                    TypographyPanel(props, '#FFFFFF'),
                    el(PanelBody, { title: 'Lista e Aparência', initialOpen: false },
                        el(wp.components.ToggleControl, {
                            label: 'Mostrar destaques grandes',
                            checked: attributes.showHighlights,
                            onChange: function(val){ setAttributes({ showHighlights: !!val }); }
                        }),
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
                    el('div', { style: { fontSize: '12px', color: '#666' } }, (attributes.showHighlights ? '2 Grandes + ' : '') + 'Lista + 1 Coluna'),
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
        category: 'seisdeagosto',
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

    // Weather (Clima/Tempo) with City Selector
    var CitySelectorComponent = function(props) {
        var attributes = props.attributes;
        var setAttributes = props.setAttributes;
        var searchTimeout = wp.element.useRef(null);
        var useState = wp.element.useState;
        
        var _searchState = useState('');
        var searchQuery = _searchState[0];
        var setSearchQuery = _searchState[1];
        
        var _resultsState = useState([]);
        var searchResults = _resultsState[0];
        var setSearchResults = _resultsState[1];
        
        var _loadingState = useState(false);
        var isLoading = _loadingState[0];
        var setIsLoading = _loadingState[1];
        
        var _showResultsState = useState(false);
        var showResults = _showResultsState[0];
        var setShowResults = _showResultsState[1];

        var searchCities = function(query) {
            if (query.length < 3) {
                setSearchResults([]);
                setShowResults(false);
                return;
            }
            
            setIsLoading(true);
            
            fetch('https://geocoding-api.open-meteo.com/v1/search?name=' + encodeURIComponent(query) + '&count=8&language=pt&format=json')
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    setIsLoading(false);
                    if (data.results && data.results.length > 0) {
                        setSearchResults(data.results);
                        setShowResults(true);
                    } else {
                        setSearchResults([]);
                        setShowResults(true);
                    }
                })
                .catch(function(error) {
                    console.error('City search error:', error);
                    setIsLoading(false);
                    setSearchResults([]);
                });
        };

        var handleSearchChange = function(value) {
            setSearchQuery(value);
            
            if (searchTimeout.current) {
                clearTimeout(searchTimeout.current);
            }
            
            searchTimeout.current = setTimeout(function() {
                searchCities(value);
            }, 300);
        };

        var selectCity = function(city) {
            setAttributes({
                cityName: city.name,
                latitude: String(city.latitude),
                longitude: String(city.longitude)
            });
            setSearchQuery('');
            setSearchResults([]);
            setShowResults(false);
        };

        var getFlagEmoji = function(countryCode) {
            if (!countryCode || countryCode.length !== 2) return '';
            var codePoints = countryCode.toUpperCase().split('').map(function(char) {
                return 127397 + char.charCodeAt(0);
            });
            return String.fromCodePoint.apply(null, codePoints);
        };

        var selectedCityDisplay = attributes.cityName ? 
            el('div', { 
                style: { 
                    padding: '10px', 
                    background: 'linear-gradient(135deg, #0a4579 0%, #1565c0 100%)', 
                    borderRadius: '4px', 
                    color: '#fff',
                    marginBottom: '12px'
                } 
            },
                el('div', { style: { display: 'flex', alignItems: 'center', justifyContent: 'space-between' } },
                    el('div', null,
                        el('strong', null, '📍 ', attributes.cityName),
                        el('div', { style: { fontSize: '11px', opacity: 0.8, fontFamily: 'monospace' } }, 
                            'Lat: ' + attributes.latitude + ' | Lon: ' + attributes.longitude
                        )
                    ),
                    el(Button, { 
                        isSmall: true, 
                        variant: 'secondary',
                        onClick: function() {
                            setAttributes({ cityName: '', latitude: '', longitude: '' });
                        },
                        style: { color: '#fff', borderColor: 'rgba(255,255,255,0.5)' }
                    }, '✕')
                )
            ) : null;

        var resultsDropdown = showResults ? el('div', {
            style: {
                position: 'absolute',
                top: '100%',
                left: 0,
                right: 0,
                background: '#fff',
                border: '1px solid #ddd',
                borderTop: 'none',
                borderRadius: '0 0 4px 4px',
                maxHeight: '250px',
                overflowY: 'auto',
                zIndex: 1000,
                boxShadow: '0 4px 12px rgba(0,0,0,0.15)'
            }
        }, 
            searchResults.length > 0 ? 
                searchResults.map(function(city, index) {
                    var location = [city.admin1, city.country].filter(Boolean).join(', ');
                    return el('div', {
                        key: index,
                        onClick: function() { selectCity(city); },
                        style: {
                            padding: '10px 12px',
                            cursor: 'pointer',
                            borderBottom: '1px solid #eee',
                            transition: 'background 0.15s'
                        },
                        onMouseEnter: function(e) { e.target.style.background = '#f0f8ff'; },
                        onMouseLeave: function(e) { e.target.style.background = 'transparent'; }
                    },
                        el('div', { style: { fontWeight: 500 } }, 
                            getFlagEmoji(city.country_code), ' ', city.name
                        ),
                        el('div', { style: { fontSize: '11px', color: '#666' } }, location),
                        el('div', { style: { fontSize: '10px', color: '#999', fontFamily: 'monospace' } }, 
                            city.latitude.toFixed(4) + ', ' + city.longitude.toFixed(4)
                        )
                    );
                }) :
                el('div', { style: { padding: '12px', textAlign: 'center', color: '#666' } }, 
                    '🔍 Nenhuma cidade encontrada'
                )
        ) : null;

        return el('div', null,
            selectedCityDisplay,
            el('div', { style: { position: 'relative' } },
                el(TextControl, {
                    label: 'Buscar Cidade',
                    value: searchQuery,
                    onChange: handleSearchChange,
                    placeholder: 'Digite o nome da cidade...',
                    help: isLoading ? '🔄 Buscando...' : 'Mínimo 3 caracteres para buscar'
                }),
                resultsDropdown
            )
        );
    };

    registerBlockType('seideagosto/weather', {
        title: 'Clima / Tempo',
        icon: 'cloud',
        category: 'seisdeagosto',
        attributes: {
            cityName: { type: 'string', default: '' },
            latitude: { type: 'string', default: '' },
            longitude: { type: 'string', default: '' },
            units: { type: 'string', default: 'c' }, // c or f
            theme: { type: 'string', default: 'dark' }, // dark or light
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
                        { title: '🌍 Localização', initialOpen: true },
                        el(CitySelectorComponent, { attributes: attributes, setAttributes: setAttributes }),
                        el('hr', { style: { margin: '16px 0', borderColor: '#eee' } }),
                        el('p', { style: { fontSize: '11px', color: '#666', marginBottom: '8px' } }, 
                            'Ou insira coordenadas manualmente:'
                        ),
                        el(TextControl, {
                            label: 'Latitude',
                            value: attributes.latitude,
                            onChange: function(val) { setAttributes({ latitude: val }); },
                            placeholder: 'Ex: -9.975'
                        }),
                        el(TextControl, {
                            label: 'Longitude',
                            value: attributes.longitude,
                            onChange: function(val) { setAttributes({ longitude: val }); },
                            placeholder: 'Ex: -67.824'
                        })
                    ),
                    el(
                        PanelBody,
                        { title: '🎨 Aparência', initialOpen: true },
                        el(SelectControl, {
                            label: 'Tema',
                            value: attributes.theme || 'dark',
                            options: [
                                { label: '🌙 Escuro (Dark)', value: 'dark' },
                                { label: '☀️ Claro (Light)', value: 'light' }
                            ],
                            onChange: function(val) { setAttributes({ theme: val }); },
                            help: 'Escolha o esquema de cores do widget'
                        })
                    ),
                    el(
                        PanelBody,
                        { title: '⚙️ Exibição', initialOpen: false },
                        el(SelectControl, {
                            label: 'Unidades',
                            value: attributes.units,
                            options: [
                                { label: 'Celsius (°C)', value: 'c' },
                                { label: 'Fahrenheit (°F)', value: 'f' }
                            ],
                            onChange: function(val) { setAttributes({ units: val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Mostrar Vento',
                            checked: attributes.showWind,
                            onChange: function(val) { setAttributes({ showWind: val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Mostrar Chuva',
                            checked: attributes.showRain,
                            onChange: function(val) { setAttributes({ showRain: val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Mostrar Previsão',
                            checked: attributes.showForecast,
                            onChange: function(val) { setAttributes({ showForecast: val }); }
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
                    attributes.cityName ? 
                        el('div', { 
                            style: { 
                                padding: '8px', 
                                background: 'linear-gradient(135deg, #0a4579 0%, #1565c0 100%)', 
                                borderRadius: '4px', 
                                color: '#fff',
                                marginBottom: '8px'
                            } 
                        },
                            el('strong', null, '📍 ', attributes.cityName),
                            el('div', { style: { fontSize: '10px', opacity: 0.9, fontFamily: 'monospace' } }, 
                                attributes.latitude + ', ' + attributes.longitude
                            )
                        ) :
                        el('div', { style: { fontSize: '12px', color: '#e67e22', marginBottom: '8px' } }, 
                            '⚠️ Selecione uma cidade no painel lateral'
                        ),
                    el('div', { style: { fontSize: '11px', color: '#999', lineHeight: '1.4' } },
                        'Tema: ' + (attributes.theme === 'light' ? '☀️' : '🌙') + ' | ',
                        'Unidade: ' + (attributes.units === 'f' ? '°F' : '°C') + ' | ',
                        'Vento: ' + (attributes.showWind ? '✓' : '✗') + ' | ',
                        'Chuva: ' + (attributes.showRain ? '✓' : '✗') + ' | ',
                        'Previsão: ' + (attributes.showForecast ? (attributes.forecastDays + 'd') : '✗')
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
        category: 'seisdeagosto',
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
        category: 'seisdeagosto',
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
        if (typeof a.titulo !== 'undefined' && a.titulo) lines.push('Título: ' + a.titulo);
        if (typeof a.icone !== 'undefined' && a.icone) lines.push('Ícone: ' + a.icone);
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

            // Permite inserir o bloco de Título com Ícone (CTA)
            if (name === 'u-correio68/titulo-com-icone') {
                return settings;
            }
            
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
        category: 'seisdeagosto',
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

    // CTA (Título com Ícone)
    wp.domReady(function() {
        var ctaBlockName = 'u-correio68/titulo-com-icone';
        try {
            var existing = wp.blocks.getBlockType(ctaBlockName);
            var settings = {
                title: 'Título com Ícone',
                icon: 'heading',
                category: 'seisdeagosto',
                attributes: {
                    titulo: { type: 'string', default: 'CTA' },
                    icone: { type: 'string', default: 'fa-star' },
                    mostrarIcone: { type: 'boolean', default: true },
                    corIcone: { type: 'string', default: '#fd7e14' },
                    corLinha: { type: 'string', default: '#fd7e14' },
                    tamanhoIcone: { type: 'number', default: 24 },
                    tamanhoTitulo: { type: 'number', default: 28 },
                    espessuraLinha: { type: 'number', default: 3 },
                    alinhamento: { type: 'string', default: 'left' }
                },
                edit: function(props) {
                    var attributes = props.attributes;
                    var setAttributes = props.setAttributes;
                    var blockProps = useBlockProps ? useBlockProps() : {};
                    var iconOptions = [
                        { label: '⭐ Estrela', value: 'fa-star' },
                        { label: '🔥 Fogo', value: 'fa-fire' },
                        { label: '⚡ Raio', value: 'fa-bolt' },
                        { label: '📰 Jornal', value: 'fa-newspaper-o' },
                        { label: '📌 Pin', value: 'fa-map-pin' },
                        { label: '🎯 Alvo', value: 'fa-bullseye' },
                        { label: '🏷️ Tag', value: 'fa-tag' },
                        { label: '📣 Megafone', value: 'fa-bullhorn' },
                        { label: '📢 Alto-falante', value: 'fa-volume-up' },
                        { label: '✅ Check', value: 'fa-check-circle' }
                    ];
                    
                    // Safe ServerSideRender resolution
                    var SSR = wp.serverSideRender || (wp.components && wp.components.ServerSideRender);
                    if (typeof SSR === 'object' && SSR.default) SSR = SSR.default;

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
                                    value: attributes.titulo,
                                    onChange: function(val) { setAttributes({ titulo: val }); }
                                }),
                                el(ToggleControl, {
                                    label: 'Exibir ícone',
                                    checked: !!attributes.mostrarIcone,
                                    onChange: function(val) { setAttributes({ mostrarIcone: val }); }
                                }),
                                ComboboxControl ? el(ComboboxControl, {
                                    label: 'Ícone (Font Awesome) - pesquisar',
                                    value: attributes.icone,
                                    options: iconOptions,
                                    onChange: function(val) { setAttributes({ icone: val || '' }); },
                                    allowReset: true
                                }) : el(SelectControl, {
                                    label: 'Ícone (Font Awesome)',
                                    value: attributes.icone,
                                    options: iconOptions,
                                    onChange: function(val) { setAttributes({ icone: val }); }
                                }),
                                el(TextControl, {
                                    label: 'Classe do ícone (entrada direta)',
                                    value: attributes.icone,
                                    onChange: function(val) { setAttributes({ icone: val }); },
                                    help: 'Ex: fa-star, fas fa-bolt, far fa-newspaper'
                                }),
                                el(SelectControl, {
                                    label: 'Alinhamento',
                                    value: attributes.alinhamento,
                                    options: [
                                        { label: 'Esquerda', value: 'left' },
                                        { label: 'Centro', value: 'center' },
                                        { label: 'Direita', value: 'right' }
                                    ],
                                    onChange: function(val) { setAttributes({ alinhamento: val }); }
                                }),
                                attributes.mostrarIcone ? el(RangeControl, {
                                    label: 'Tamanho do Ícone (px)',
                                    value: attributes.tamanhoIcone,
                                    onChange: function(val) { setAttributes({ tamanhoIcone: val }); },
                                    min: 12, max: 60
                                }) : null,
                                el(RangeControl, {
                                    label: 'Tamanho do Título (px)',
                                    value: attributes.tamanhoTitulo,
                                    onChange: function(val) { setAttributes({ tamanhoTitulo: val }); },
                                    min: 12, max: 60
                                }),
                                el(RangeControl, {
                                    label: 'Espessura da Linha (px)',
                                    value: attributes.espessuraLinha,
                                    onChange: function(val) { setAttributes({ espessuraLinha: val }); },
                                    min: 1, max: 10
                                }),
                                attributes.mostrarIcone ? el(ColorPalette, {
                                    label: 'Cor do Ícone',
                                    value: attributes.corIcone,
                                    onChange: function(val) { setAttributes({ corIcone: val }); }
                                }) : null,
                                el(ColorPalette, {
                                    label: 'Cor da Linha',
                                    value: attributes.corLinha,
                                    onChange: function(val) { setAttributes({ corLinha: val }); }
                                })
                            )
                        ),
                        el('div', blockProps, 
                            SSR ? el(SSR, {
                                block: 'u-correio68/titulo-com-icone',
                                attributes: attributes
                            }) : el('div', {}, 'ServerSideRender not available')
                        )
                    );
                },
                save: function() {
                    return null;
                }
            };

            // Evita erro "Block is already registered" se o PHP ou outro script já o registrou
            if (existing) {
                wp.blocks.unregisterBlockType(ctaBlockName);
            }

            registerBlockType(ctaBlockName, settings);
        } catch (e) {
            console.error('SEISDEAGOSTO: Failed to register titulo-com-icone', e);
            // Fallback: tenta restaurar a definição anterior, se existir
            try {
                if (!wp.blocks.getBlockType(ctaBlockName) && existing) {
                    wp.blocks.registerBlockType(existing.name, existing);
                }
            } catch (err) {
                // ignore
            }
        }
    });

    // Adiciona painel dinâmico de seleção de tipo de post para o bloco lista-noticias
    (function(wp) {
        var addFilter = wp.hooks.addFilter;
        var el = wp.element.createElement;
        var InspectorControls = wp.blockEditor.InspectorControls;
        var PanelBody = wp.components.PanelBody;
        var SelectControl = wp.components.SelectControl;
        var useState = wp.element.useState;
        var useEffect = wp.element.useEffect;

        addFilter(
            'editor.BlockEdit',
            'seisdeagosto/lista-noticias-posttype',
            function(BlockEdit) {
                return function(props) {
                    if (props.name !== 'u-correio68/lista-noticias') {
                        return el(BlockEdit, props);
                    }

                    // Hook para armazenar post types
                    var _useState = useState([]), postTypes = _useState[0], setPostTypes = _useState[1];
                    // Carregar post types públicos via REST API
                    useEffect(function() {
                        wp.apiFetch({ path: '/wp/v2/types' }).then(function(types) {
                            var options = Object.keys(types)
                                .filter(function(key) { return types[key].viewable && types[key].slug !== 'attachment'; })
                                .map(function(key) {
                                    return { label: types[key].name, value: types[key].slug };
                                });
                            setPostTypes(options);
                        });
                    }, []);

                    return el(
                        wp.element.Fragment,
                        {},
                        el(BlockEdit, props),
                        el(
                            InspectorControls,
                            {},
                            el(
                                PanelBody,
                                { title: 'Tipo de Post', initialOpen: true },
                                el(SelectControl, {
                                    label: 'Tipo de Post',
                                    value: props.attributes.postType || 'post',
                                    options: postTypes.length ? postTypes : [ { label: 'Carregando...', value: '' } ],
                                    onChange: function(val) {
                                        props.setAttributes({ postType: val });
                                    }
                                })
                            )
                        )
                    );
                };
            }
        );
    })(window.wp);
    // Adiciona painel de seleção de tipo de post para o bloco destaque-grande
    (function(wp) {
        var addFilter = wp.hooks.addFilter;
        var el = wp.element.createElement;
        var InspectorControls = wp.blockEditor.InspectorControls;
        var PanelBody = wp.components.PanelBody;
        var SelectControl = wp.components.SelectControl;

        addFilter(
            'editor.BlockEdit',
            'seisdeagosto/destaque-grande-posttype',
            function(BlockEdit) {
                return function(props) {
                    if (props.name !== 'u-correio68/destaque-grande') {
                        return el(BlockEdit, props);
                    }
                    return el(
                        wp.element.Fragment,
                        {},
                        el(BlockEdit, props),
                        el(
                            InspectorControls,
                            {},
                            el(
                                PanelBody,
                                { title: 'Tipo de Post', initialOpen: true },
                                el(SelectControl, {
                                    label: 'Tipo de Post',
                                    value: props.attributes.postType || 'post',
                                    options: [
                                        { label: 'Notícias (post)', value: 'post' },
                                        { label: 'Editais', value: 'edital' }
                                    ],
                                    onChange: function(val) {
                                        props.setAttributes({ postType: val });
                                    }
                                })
                            )
                        )
                    );
                };
            }
        );
    })(window.wp);
    // Adiciona painel de seleção de tipo de post para o bloco destaque-pequeno
    (function(wp) {
        var addFilter = wp.hooks.addFilter;
        var el = wp.element.createElement;
        var InspectorControls = wp.blockEditor.InspectorControls;
        var PanelBody = wp.components.PanelBody;
        var SelectControl = wp.components.SelectControl;

        addFilter(
            'editor.BlockEdit',
            'seisdeagosto/destaque-pequeno-posttype',
            function(BlockEdit) {
                return function(props) {
                    if (props.name !== 'u-correio68/destaque-pequeno') {
                        return el(BlockEdit, props);
                    }
                    return el(
                        wp.element.Fragment,
                        {},
                        el(BlockEdit, props),
                        el(
                            InspectorControls,
                            {},
                            el(
                                PanelBody,
                                { title: 'Tipo de Post', initialOpen: true },
                                el(SelectControl, {
                                    label: 'Tipo de Post',
                                    value: props.attributes.postType || 'post',
                                    options: [
                                        { label: 'Notícias (post)', value: 'post' },
                                        { label: 'Editais', value: 'edital' }
                                    ],
                                    onChange: function(val) {
                                        props.setAttributes({ postType: val });
                                    }
                                })
                            )
                        )
                    );
                };
            }
        );
    })(window.wp);
})(window.wp);