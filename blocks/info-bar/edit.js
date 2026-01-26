/**
 * Info Bar Block Editor Script
 */
(function (blocks, element, blockEditor, components, i18n) {
    const { registerBlockType } = blocks;
    const { Fragment } = element;
    const { InspectorControls, useBlockProps } = blockEditor;
    const { PanelBody, TextControl, ToggleControl, RangeControl, ColorPicker } = components;
    const { __ } = i18n;

    registerBlockType('seisdeagosto/info-bar', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                cityName,
                latitude,
                longitude,
                fontFamily,
                fontSize,
                showLocation,
                showWeather,
                showDate,
                showTime,
                backgroundColor,
                textColor
            } = attributes;

            const blockProps = useBlockProps({
                style: {
                    backgroundColor: backgroundColor,
                    color: textColor,
                    fontFamily: fontFamily,
                    fontSize: fontSize + 'px',
                    padding: '8px 0',
                    borderBottom: '1px solid rgba(255, 255, 255, 0.1)'
                }
            });

            return element.createElement(
                Fragment,
                null,
                element.createElement(
                    InspectorControls,
                    null,
                        element.createElement(
                            PanelBody,
                            { title: __('Configurações de Localização', 'seisdeagosto'), initialOpen: true },
                            element.createElement(TextControl, {
                                label: __('Nome da Cidade', 'seisdeagosto'),
                                value: cityName,
                                onChange: (value) => setAttributes({ cityName: value })
                            }),
                            element.createElement(TextControl, {
                                label: __('Latitude', 'seisdeagosto'),
                                value: latitude,
                                onChange: (value) => setAttributes({ latitude: value }),
                                help: __('Ex: -9.9749', 'seisdeagosto')
                            }),
                            element.createElement(TextControl, {
                                label: __('Longitude', 'seisdeagosto'),
                                value: longitude,
                                onChange: (value) => setAttributes({ longitude: value }),
                                help: __('Ex: -67.8103', 'seisdeagosto')
                            })
                        ),
                        element.createElement(
                            PanelBody,
                            { title: __('Opções de Exibição', 'seisdeagosto'), initialOpen: true },
                            element.createElement(ToggleControl, {
                                label: __('Mostrar Localização', 'seisdeagosto'),
                                checked: showLocation,
                                onChange: (value) => setAttributes({ showLocation: value })
                            }),
                            element.createElement(ToggleControl, {
                                label: __('Mostrar Clima', 'seisdeagosto'),
                                checked: showWeather,
                                onChange: (value) => setAttributes({ showWeather: value })
                            }),
                            element.createElement(ToggleControl, {
                                label: __('Mostrar Data', 'seisdeagosto'),
                                checked: showDate,
                                onChange: (value) => setAttributes({ showDate: value })
                            }),
                            element.createElement(ToggleControl, {
                                label: __('Mostrar Hora', 'seisdeagosto'),
                                checked: showTime,
                                onChange: (value) => setAttributes({ showTime: value })
                            })
                        ),
                        element.createElement(
                            PanelBody,
                            { title: __('Estilo', 'seisdeagosto'), initialOpen: false },
                            element.createElement(TextControl, {
                                label: __('Família da Fonte', 'seisdeagosto'),
                                value: fontFamily,
                                onChange: (value) => setAttributes({ fontFamily: value }),
                                help: __('Ex: Roboto, Arial, sans-serif', 'seisdeagosto')
                            }),
                            element.createElement(RangeControl, {
                                label: __('Tamanho da Fonte (px)', 'seisdeagosto'),
                                value: fontSize,
                                onChange: (value) => setAttributes({ fontSize: value }),
                                min: 10,
                                max: 20,
                                step: 1
                            }),
                            element.createElement(
                                'div',
                                { style: { marginBottom: '16px' } },
                                element.createElement('label', { style: { display: 'block', marginBottom: '8px', fontWeight: '500' } }, __('Cor de Fundo', 'seisdeagosto')),
                                element.createElement(ColorPicker, {
                                    color: backgroundColor,
                                    onChangeComplete: (value) => setAttributes({ backgroundColor: value.hex }),
                                    disableAlpha: false
                                })
                            ),
                            element.createElement(
                                'div',
                                { style: { marginBottom: '16px' } },
                                element.createElement('label', { style: { display: 'block', marginBottom: '8px', fontWeight: '500' } }, __('Cor do Texto', 'seisdeagosto')),
                                element.createElement(ColorPicker, {
                                    color: textColor,
                                    onChangeComplete: (value) => setAttributes({ textColor: value.hex }),
                                    disableAlpha: false
                                })
                            )
                        )
                    ),
                    element.createElement(
                        'div',
                        blockProps,
                        element.createElement(
                            'div',
                            { className: 'info-bar-container', style: { maxWidth: '1200px', margin: '0 auto', padding: '0 20px', display: 'flex', gap: '30px', flexWrap: 'wrap' } },
                            showLocation && element.createElement(
                                'div',
                                { className: 'info-bar-item info-location', style: { display: 'flex', alignItems: 'center', gap: '8px' } },
                                element.createElement('i', { className: 'fa fa-map-marker', 'aria-hidden': 'true' }),
                                element.createElement('span', null, cityName || 'Rio Branco')
                            ),
                            showWeather && element.createElement(
                                'div',
                                { className: 'info-bar-item info-weather', style: { display: 'flex', alignItems: 'center', gap: '8px' } },
                                element.createElement(
                                    'div',
                                    { className: 'weather-icon-mini icon-clear', style: { position: 'relative', width: '24px', height: '24px', display: 'inline-flex', alignItems: 'center', justifyContent: 'center' } },
                                    element.createElement('div', { className: 'icon-base', style: { position: 'absolute', inset: '0', borderRadius: '50%', background: '#f9db62', opacity: '0.15' } }),
                                    element.createElement('i', { className: 'fa fa-sun-o weather-fa-icon-mini', style: { position: 'relative', zIndex: '1', fontSize: '14px', color: '#ffd93d' }, 'aria-hidden': 'true' })
                                ),
                                element.createElement('span', null, '25°C')
                            ),
                            showDate && element.createElement(
                                'div',
                                { className: 'info-bar-item info-date', style: { display: 'flex', alignItems: 'center', gap: '8px' } },
                                element.createElement('i', { className: 'fa fa-calendar', 'aria-hidden': 'true' }),
                                element.createElement('span', null, new Date().toLocaleDateString('pt-BR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }))
                            ),
                            showTime && element.createElement(
                                'div',
                                { className: 'info-bar-item info-time', style: { display: 'flex', alignItems: 'center', gap: '8px' } },
                                element.createElement('i', { className: 'fa fa-clock-o', 'aria-hidden': 'true' }),
                                element.createElement('span', null, new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }))
                            )
                        )
                    )
                );
        },
        save: function () {
            // Server-side rendering
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
