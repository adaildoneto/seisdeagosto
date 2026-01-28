/**
 * Instagram Reels Gallery Block Editor Script
 * Using Instagram Graph API with Access Token
 */
(function (blocks, element, blockEditor, components, i18n) {
    const { registerBlockType } = blocks;
    const { Fragment } = element;
    const { InspectorControls, useBlockProps } = blockEditor;
    const { PanelBody, TextControl, TextareaControl, RangeControl, ToggleControl, SelectControl } = components;
    const { __ } = i18n;

    registerBlockType('seisdeagosto/instagram-reels', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                title,
                description,
                profileUrl,
                instagramUrls,
                accessToken,
                instagramUsername,
                numberOfReels,
                columns,
                showCaptions,
                mediaType
            } = attributes;

            const blockProps = useBlockProps({
                className: 'instagram-reels-gallery'
            });

            return element.createElement(
                Fragment,
                null,
                element.createElement(
                    InspectorControls,
                    null,
                    element.createElement(
                        PanelBody,
                        { title: __('Configurações do Instagram', 'seisdeagosto'), initialOpen: true },
                        element.createElement(
                            'div',
                            { style: { padding: '10px', background: '#d1ecf1', borderRadius: '4px', marginBottom: '15px', border: '1px solid #bee5eb' } },
                            element.createElement('p', { style: { margin: '0 0 8px 0', fontSize: '13px', fontWeight: '600', color: '#0c5460' } }, 
                                '✨ Método Simplificado (Recomendado):'
                            ),
                            element.createElement('p', { style: { margin: '0 0 5px 0', fontSize: '12px', color: '#0c5460' } },
                                'Cole as URLs dos posts do Instagram (uma por linha) no campo abaixo. Não precisa de Access Token!'
                            )
                        ),
                        element.createElement(TextareaControl, {
                            label: __('URLs dos Posts do Instagram', 'seisdeagosto'),
                            value: instagramUrls,
                            onChange: (value) => setAttributes({ instagramUrls: value }),
                            help: __('Cole as URLs dos posts/reels (uma por linha). Exemplo: https://www.instagram.com/p/ABC123/', 'seisdeagosto'),
                            rows: 6,
                            placeholder: 'https://www.instagram.com/reel/ABC123/\nhttps://www.instagram.com/p/DEF456/\nhttps://www.instagram.com/reel/GHI789/'
                        }),
                        element.createElement('hr', { style: { margin: '20px 0', borderTop: '1px solid #ddd' } }),
                        element.createElement(
                            'div',
                            { style: { padding: '10px', background: '#fff3cd', borderRadius: '4px', marginBottom: '15px', border: '1px solid #ffc107' } },
                            element.createElement('p', { style: { margin: '0 0 8px 0', fontSize: '13px', fontWeight: '600', color: '#856404' } }, 
                                '⚙️ Método Alternativo (API Oficial):'
                            ),
                            element.createElement('ol', { style: { margin: '0', paddingLeft: '20px', fontSize: '12px', color: '#856404' } },
                                element.createElement('li', null, 'Acesse '),
                                element.createElement('a', { href: 'https://developers.facebook.com/', target: '_blank', style: { color: '#004085', textDecoration: 'underline' } }, 'Facebook Developers'),
                                element.createElement('li', null, 'Crie um app tipo "Consumidor"'),
                                element.createElement('li', null, 'Adicione "Exibição Básica do Instagram"'),
                                element.createElement('li', null, 'Gere o User Token')
                            ),
                            element.createElement('p', { style: { margin: '10px 0 0 0', fontSize: '12px' } },
                                element.createElement('a', { href: 'https://matteus.dev/contratar/incorporar-posts-do-instagram-no-site-2024/', target: '_blank', style: { color: '#004085', fontWeight: '600', textDecoration: 'underline' } }, 
                                    '📖 Ver tutorial completo'
                                )
                            )
                        ),
                        element.createElement(TextareaControl, {
                            label: __('Access Token do Instagram', 'seisdeagosto'),
                            value: accessToken,
                            onChange: (value) => setAttributes({ accessToken: value }),
                            help: __('Cole aqui o token gerado no Facebook Developers', 'seisdeagosto'),
                            rows: 3
                        }),
                        element.createElement(TextControl, {
                            label: __('Perfil do Instagram (opcional)', 'seisdeagosto'),
                            value: instagramUsername,
                            onChange: (value) => setAttributes({ instagramUsername: value }),
                            placeholder: __('@usuario ou usuario', 'seisdeagosto'),
                            help: __('Deixe em branco para exibir seus próprios posts ou digite o @ de outro perfil (requer conta Business)', 'seisdeagosto')
                        })
                    ),
                    element.createElement(
                        PanelBody,
                        { title: __('Configurações de Exibição', 'seisdeagosto'), initialOpen: true },
                        element.createElement(TextControl, {
                            label: __('Título', 'seisdeagosto'),
                            value: title,
                            onChange: (value) => setAttributes({ title: value })
                        }),
                        element.createElement(TextareaControl, {
                            label: __('Descrição', 'seisdeagosto'),
                            value: description,
                            onChange: (value) => setAttributes({ description: value })
                        }),
                        element.createElement(TextControl, {
                            label: __('URL do Perfil do Instagram', 'seisdeagosto'),
                            value: profileUrl,
                            onChange: (value) => setAttributes({ profileUrl: value }),
                            placeholder: __('https://www.instagram.com/seuusuario/', 'seisdeagosto'),
                            help: __('Link para o perfil do Instagram (aparecerá como botão "Seguir no Instagram")', 'seisdeagosto')
                        }),
                        element.createElement(RangeControl, {
                            label: __('Número de Posts', 'seisdeagosto'),
                            value: numberOfReels,
                            onChange: (value) => setAttributes({ numberOfReels: value }),
                            min: 3,
                            max: 12,
                            step: 1
                        }),
                        element.createElement(RangeControl, {
                            label: __('Colunas', 'seisdeagosto'),
                            value: columns,
                            onChange: (value) => setAttributes({ columns: value }),
                            min: 2,
                            max: 4,
                            step: 1
                        }),
                        element.createElement(SelectControl, {
                            label: __('Tipo de Mídia', 'seisdeagosto'),
                            value: mediaType,
                            options: [
                                { label: 'Todos (Fotos + Vídeos)', value: 'all' },
                                { label: 'Apenas Vídeos/Reels', value: 'reels' },
                                { label: 'Apenas Imagens', value: 'images' }
                            ],
                            onChange: (value) => setAttributes({ mediaType: value })
                        }),
                        element.createElement(ToggleControl, {
                            label: __('Mostrar Legendas', 'seisdeagosto'),
                            checked: showCaptions,
                            onChange: (value) => setAttributes({ showCaptions: value })
                        })
                    )
                ),
                element.createElement(
                    'div',
                    blockProps,
                    element.createElement(
                        'div',
                        { className: 'ig-reels-header', style: { textAlign: 'center', marginBottom: '30px' } },
                        title && element.createElement('h2', { className: 'ig-reels-title' }, title),
                        description && element.createElement('p', { className: 'ig-reels-description' }, description)
                    ),
                    (instagramUrls || accessToken) ? 
                        element.createElement(
                            'div',
                            { style: { padding: '40px', textAlign: 'center', background: '#d4edda', borderRadius: '8px', border: '1px solid #c3e6cb' } },
                            element.createElement('i', { className: 'fa fa-check-circle', style: { fontSize: '3rem', color: '#155724', marginBottom: '15px' } }),
                            element.createElement('h3', { style: { color: '#155724', margin: '0 0 10px 0' } }, instagramUrls ? '✅ URLs Configuradas' : '✅ Access Token Configurado'),
                            element.createElement('p', { style: { color: '#155724', margin: '0' } }, 'Os posts do Instagram serão exibidos no frontend')
                        )
                    : element.createElement(
                        'div',
                        { className: 'ig-reels-instructions', style: { padding: '40px 20px', textAlign: 'center', background: '#f0f6fc', borderRadius: '8px', border: '2px dashed #ccc' } },
                        element.createElement('i', { className: 'fa fa-instagram', style: { fontSize: '3rem', color: '#999', marginBottom: '15px' } }),
                        element.createElement('h3', { style: { marginTop: '0' } }, '📷 Configure os Posts do Instagram'),
                        element.createElement('p', { style: { color: '#666' } }, 'Use o painel à direita "Configurações do Instagram" para adicionar URLs ou token')
                    )
                )
            );
        },
        save: function () {
            return null;
        }
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components,
    window.wp.i18n
);
