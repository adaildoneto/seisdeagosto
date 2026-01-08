(function(wp) {
    var registerBlockType = wp.blocks.registerBlockType;
    var el = wp.element.createElement;
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
    registerBlockType('u-correio68/destaques-home', {
        title: 'Destaques Home (1 Grande + 2 Pequenos)',
        icon: 'layout',
        category: 'layout',
        supports: { inserter: false },
        attributes: {
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
                        { title: 'Configurações' },
                        el(SelectControl, {
                            label: 'Categoria',
                            value: attributes.categoryId,
                            options: uCorreio68Blocks.categories,
                            onChange: function(val) { setAttributes({ categoryId: String(val || '0') }); }
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'u-correio68/destaques-home',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });
    // Duplicate registration under new namespace for forward compatibility
    registerBlockType('seideagosto/destaques-home', {
        title: 'Destaques Home (1 Grande + 2 Pequenos)',
        icon: 'layout',
        category: 'layout',
        attributes: {
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
                        { title: 'Configurações' },
                        el(SelectControl, {
                            label: 'Categoria',
                            value: attributes.categoryId,
                            options: (typeof seideagostoBlocks !== 'undefined' ? seideagostoBlocks.categories : uCorreio68Blocks.categories),
                            onChange: function(val) { setAttributes({ categoryId: String(val || '0') }); }
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'seideagosto/destaques-home',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });

    // Colunistas Grid
    registerBlockType('u-correio68/colunistas-grid', {
        title: 'Grid de Colunistas',
        icon: 'groups',
        category: 'layout',
        supports: { inserter: false },
        edit: function(props) {
            return el(
                'div',
                { className: 'row colunistas-grid' },
                el(InnerBlocks, {
                    allowedBlocks: ['u-correio68/colunista-item']
                })
            );
        },
        save: function() {
            return el(InnerBlocks.Content);
        }
    });
    // Duplicate registration under new namespace for forward compatibility
    registerBlockType('seideagosto/colunistas-grid', {
        title: 'Grid de Colunistas',
        icon: 'groups',
        category: 'layout',
        edit: function(props) {
            return el(
                'div',
                { className: 'row colunistas-grid' },
                el(InnerBlocks, {
                    allowedBlocks: ['seideagosto/colunista-item']
                })
            );
        },
        save: function() {
            return el(InnerBlocks.Content);
        }
    });

    // Colunista Item
    registerBlockType('u-correio68/colunista-item', {
        title: 'Colunista Item',
        icon: 'admin-users',
        category: 'layout',
        supports: { inserter: false },
        parent: ['u-correio68/colunistas-grid'],
        getEditWrapperProps: function(attributes) {
            return { className: 'col-6 col-sm-6 col-md-4 col-lg-3 mb-3' };
        },
        attributes: {
            name: { type: 'string', default: '' },
            columnTitle: { type: 'string', default: '' },
            imageUrl: { type: 'string', default: '' },
            categoryId: { type: 'string', default: '0' }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var latestTitle = '';
            var catId = attributes.categoryId && attributes.categoryId !== '0' ? parseInt(attributes.categoryId) : 0;
            try {
                if (catId > 0 && wp && wp.data && wp.data.select) {
                    var posts = wp.data.select('core').getEntityRecords('postType', 'post', { per_page: 1, categories: [catId], status: 'publish' });
                    if (posts && posts.length > 0 && posts[0] && posts[0].title && posts[0].title.rendered) {
                        latestTitle = posts[0].title.rendered.replace(/<[^>]*>?/gm, '');
                    }
                }
            } catch (e) {}

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Dados do Colunista' },
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
                            options: uCorreio68Blocks.categories,
                            onChange: function(val) { setAttributes({ categoryId: String(val || '0') }); }
                        })
                    )
                ),
                el(
                    PanelBody,
                    { title: 'Imagem do Colunista', initialOpen: false },
                    el(MediaUpload, {
                        onSelect: function(media) { setAttributes({ imageUrl: media && media.url ? media.url : '' }); },
                        allowedTypes: ['image'],
                        value: attributes.imageUrl,
                        render: function(obj) {
                            return el(Button, { isPrimary: true, onClick: obj.open }, attributes.imageUrl ? 'Trocar imagem' : 'Selecionar imagem');
                        }
                    }),
                    attributes.imageUrl ? el('div', { className: 'mt-2' }, el('img', { src: attributes.imageUrl, style: { maxWidth: '160px', borderRadius: '50%' } })) : null
                ),
                el(ServerSideRender, {
                    block: 'u-correio68/colunista-item',
                    attributes: attributes,
                    className: 'col-preview'
                })
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });
    // Duplicate registration under new namespace for forward compatibility
    registerBlockType('seideagosto/colunista-item', {
        title: 'Colunista Item',
        icon: 'admin-users',
        category: 'layout',
        parent: ['seideagosto/colunistas-grid'],
        getEditWrapperProps: function(attributes) {
            return { className: 'col-6 col-sm-6 col-md-4 col-lg-3 mb-3' };
        },
        attributes: {
            name: { type: 'string', default: '' },
            columnTitle: { type: 'string', default: '' },
            imageUrl: { type: 'string', default: '' },
            categoryId: { type: 'string', default: '0' }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var latestTitle = '';
            var catId = attributes.categoryId && attributes.categoryId !== '0' ? parseInt(attributes.categoryId) : 0;
            try {
                if (catId > 0 && wp && wp.data && wp.data.select) {
                    var posts = wp.data.select('core').getEntityRecords('postType', 'post', { per_page: 1, categories: [catId], status: 'publish' });
                    if (posts && posts.length > 0 && posts[0] && posts[0].title && posts[0].title.rendered) {
                        latestTitle = posts[0].title.rendered.replace(/<[^>]*>?/gm, '');
                    }
                }
            } catch (e) {}

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Dados do Colunista' },
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
                            options: (typeof seideagostoBlocks !== 'undefined' ? seideagostoBlocks.categories : uCorreio68Blocks.categories),
                            onChange: function(val) { setAttributes({ categoryId: String(val || '0') }); }
                        })
                    )
                ),
                el(
                    PanelBody,
                    { title: 'Imagem do Colunista', initialOpen: false },
                    el(MediaUpload, {
                        onSelect: function(media) { setAttributes({ imageUrl: media && media.url ? media.url : '' }); },
                        allowedTypes: ['image'],
                        value: attributes.imageUrl,
                        render: function(obj) {
                            return el(Button, { isPrimary: true, onClick: obj.open }, attributes.imageUrl ? 'Trocar imagem' : 'Selecionar imagem');
                        }
                    }),
                    attributes.imageUrl ? el('div', { className: 'mt-2' }, el('img', { src: attributes.imageUrl, style: { maxWidth: '160px', borderRadius: '50%' } })) : null
                ),
                el(ServerSideRender, {
                    block: 'seideagosto/colunista-item',
                    attributes: attributes,
                    className: 'col-preview'
                })
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });

    // News Grid (Grid de Notícias)
    registerBlockType('u-correio68/news-grid', {
        title: 'Grid de Notícias',
        icon: 'grid-view',
        category: 'layout',
        supports: { inserter: false },
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
                            options: uCorreio68Blocks.categories,
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
                el(ServerSideRender, {
                    block: 'u-correio68/news-grid',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });

    // Duplicate registration under new namespace for forward compatibility
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
                            options: (typeof seideagostoBlocks !== 'undefined' ? seideagostoBlocks.categories : uCorreio68Blocks.categories),
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
                el(ServerSideRender, {
                    block: 'seideagosto/news-grid',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });

    // Category Highlight (1 Big + 3 List)
    registerBlockType('u-correio68/category-highlight', {
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
                            options: uCorreio68Blocks.categories,
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
                el(ServerSideRender, {
                    block: 'u-correio68/category-highlight',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });
    // Duplicate registration under new namespace for forward compatibility
    registerBlockType('seideagosto/category-highlight', {
        title: 'Destaque Categoria (1 Grande + 3 Lista)',
        icon: 'list-view',
        category: 'layout',
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
                            options: (typeof seideagostoBlocks !== 'undefined' ? seideagostoBlocks.categories : uCorreio68Blocks.categories),
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
                el(ServerSideRender, {
                    block: 'seideagosto/category-highlight',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });

    // Destaque Misto (2 Big + List + 1 Column)
    registerBlockType('u-correio68/destaque-misto', {
        title: 'Destaque Misto (2 Grandes + Lista + 1 Coluna)',
        icon: 'layout',
        category: 'layout',
        supports: { inserter: false },
        attributes: Object.assign({
            categoryId: { type: 'string', default: '0' }
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
                            options: uCorreio68Blocks.categories,
                            onChange: function(val) { setAttributes({ categoryId: String(val || '0') }); }
                        })
                    ),
                    TypographyPanel(props, '#FFFFFF')
                ),
                el(ServerSideRender, {
                    block: 'u-correio68/destaque-misto',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });
    // Duplicate registration under new namespace for forward compatibility
    registerBlockType('seideagosto/destaque-misto', {
        title: 'Destaque Misto (2 Grandes + Lista + 1 Coluna)',
        icon: 'layout',
        category: 'layout',
        attributes: Object.assign({
            categoryId: { type: 'string', default: '0' }
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
                            options: (typeof seideagostoBlocks !== 'undefined' ? seideagostoBlocks.categories : uCorreio68Blocks.categories),
                            onChange: function(val) { setAttributes({ categoryId: String(val || '0') }); }
                        })
                    ),
                    TypographyPanel(props, '#FFFFFF')
                ),
                el(ServerSideRender, {
                    block: 'seideagosto/destaque-misto',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });

    // Top Most Read (Top N)
    registerBlockType('u-correio68/top-most-read', {
        title: 'Top Mais Lidas',
        icon: 'chart-area',
        category: 'layout',
        supports: { inserter: false },
        attributes: {
            title: { type: 'string', default: 'Mais lidas' },
            count: { type: 'number', default: 5 },
            metaKey: { type: 'string', default: 'post_views_count' },
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
                        { title: 'Configurações' },
                        el(TextControl, {
                            label: 'Título',
                            value: attributes.title,
                            onChange: function(val) { setAttributes({ title: val }); }
                        }),
                        el(SelectControl, {
                            label: 'Categoria (opcional)',
                            value: attributes.categoryId,
                            options: uCorreio68Blocks.categories,
                            onChange: function(val) { setAttributes({ categoryId: String(val || '0') }); }
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
                el(ServerSideRender, {
                    block: 'u-correio68/top-most-read',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });
    // Duplicate registration under new namespace for forward compatibility
    registerBlockType('seideagosto/top-most-read', {
        title: 'Top Mais Lidas',
        icon: 'chart-area',
        category: 'layout',
        attributes: {
            title: { type: 'string', default: 'Mais lidas' },
            count: { type: 'number', default: 5 },
            metaKey: { type: 'string', default: 'post_views_count' },
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
                        { title: 'Configurações' },
                        el(TextControl, {
                            label: 'Título',
                            value: attributes.title,
                            onChange: function(val) { setAttributes({ title: val }); }
                        }),
                        el(SelectControl, {
                            label: 'Categoria (opcional)',
                            value: attributes.categoryId,
                            options: (typeof seideagostoBlocks !== 'undefined' ? seideagostoBlocks.categories : uCorreio68Blocks.categories),
                            onChange: function(val) { setAttributes({ categoryId: String(val || '0') }); }
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
                el(ServerSideRender, {
                    block: 'seideagosto/top-most-read',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });

    // Weather (Clima/Tempo)
    registerBlockType('u-correio68/weather', {
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
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'u-correio68/weather',
                    attributes: attributes
                })
            );
        },
        save: function() { return null; }
    });
    // Duplicate registration under new namespace for forward compatibility
    registerBlockType('seideagosto/weather', {
        title: 'Clima / Tempo',
        icon: 'cloud',
        category: 'widgets',
        attributes: {
            cityName: { type: 'string', default: '' },
            latitude: { type: 'string', default: '' },
            longitude: { type: 'string', default: '' },
            units: { type: 'string', default: 'c' }, // c or f
            showWind: { type: 'boolean', default: true },
            showRain: { type: 'boolean', default: true },
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
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'seideagosto/weather',
                    attributes: attributes
                })
            );
        },
        save: function() { return null; }
    });

    // Currency Monitor
    var ToggleControl = wp.components.ToggleControl;
    registerBlockType('u-correio68/currency-monitor', {
        title: 'Monitor de Câmbio',
        icon: 'money',
        category: 'widgets',
        supports: { inserter: false },
        attributes: {
            provider: { type: 'string', default: 'exchangerate' },
            base: { type: 'string', default: 'BRL' },
            showBRL: { type: 'boolean', default: true },
            showUSD: { type: 'boolean', default: true },
            showBOB: { type: 'boolean', default: true },
            showPEN: { type: 'boolean', default: true },
            spread: { type: 'number', default: 0.5 },
            showUpdated: { type: 'boolean', default: true }
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
                            label: 'Boliviano (BOB)',
                            checked: !!attributes.showBOB,
                            onChange: function(val) { setAttributes({ showBOB: !!val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Sol Peruano (PEN)',
                            checked: !!attributes.showPEN,
                            onChange: function(val) { setAttributes({ showPEN: !!val }); }
                        })
                    ),
                    el(
                        PanelBody,
                        { title: 'Exibição' },
                        el(SelectControl, {
                            label: 'Fonte (API livre)',
                            value: attributes.provider,
                            options: [
                                { label: 'Exchangerate.host (livre)', value: 'exchangerate' },
                                { label: 'Frankfurter.app (livre)', value: 'frankfurter' }
                            ],
                            onChange: function(val) { setAttributes({ provider: val }); }
                        }),
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
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'u-correio68/currency-monitor',
                    attributes: attributes
                })
            );
        },
        save: function() { return null; }
    });
    // Duplicate registration under new namespace for forward compatibility
    registerBlockType('seideagosto/currency-monitor', {
        title: 'Monitor de Câmbio',
        icon: 'money',
        category: 'widgets',
        attributes: {
            provider: { type: 'string', default: 'exchangerate' },
            base: { type: 'string', default: 'BRL' },
            showBRL: { type: 'boolean', default: true },
            showUSD: { type: 'boolean', default: true },
            showBOB: { type: 'boolean', default: true },
            showPEN: { type: 'boolean', default: true },
            spread: { type: 'number', default: 0.5 },
            showUpdated: { type: 'boolean', default: true }
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
                            label: 'Boliviano (BOB)',
                            checked: !!attributes.showBOB,
                            onChange: function(val) { setAttributes({ showBOB: !!val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Sol Peruano (PEN)',
                            checked: !!attributes.showPEN,
                            onChange: function(val) { setAttributes({ showPEN: !!val }); }
                        })
                    ),
                    el(
                        PanelBody,
                        { title: 'Exibição' },
                        el(SelectControl, {
                            label: 'Fonte (API livre)',
                            value: attributes.provider,
                            options: [
                                { label: 'Exchangerate.host (livre)', value: 'exchangerate' },
                                { label: 'Frankfurter.app (livre)', value: 'frankfurter' }
                            ],
                            onChange: function(val) { setAttributes({ provider: val }); }
                        }),
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
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'seideagosto/currency-monitor',
                    attributes: attributes
                })
            );
        },
        save: function() { return null; }
    });

    // Sidebar Area (Widget Area Renderer)
    registerBlockType('u-correio68/sidebar-area', {
        title: 'Área de Widgets (Sidebar)',
        icon: 'screenoptions',
        category: 'widgets',
        supports: { inserter: true },
        attributes: {
            sidebarId: { type: 'string', default: 'right-sidebar' },
            title: { type: 'string', default: '' }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var sidebarOptions = (typeof uCorreio68Blocks !== 'undefined' && uCorreio68Blocks.sidebars) ? uCorreio68Blocks.sidebars : [];

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
                el(ServerSideRender, {
                    block: 'u-correio68/sidebar-area',
                    attributes: attributes
                })
            );
        },
        save: function() { return null; }
    });

    // Duplicate under new namespace
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

            var sidebarOptions = (typeof seideagostoBlocks !== 'undefined' && seideagostoBlocks.sidebars) ? seideagostoBlocks.sidebars : (typeof uCorreio68Blocks !== 'undefined' ? uCorreio68Blocks.sidebars : []);

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
                el(ServerSideRender, {
                    block: 'seideagosto/sidebar-area',
                    attributes: attributes
                })
            );
        },
        save: function() { return null; }
    });

})(window.wp);