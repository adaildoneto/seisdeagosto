/**
 * Editor do bloco "Anúncio (AdSense)".
 *
 * No editor mostra só uma caixa com o tamanho aproximado do anúncio; o script do
 * Google nunca é carregado no editor.
 */
(function (blocks, element, blockEditor, components, i18n) {
	const el = element.createElement;
	const { Fragment } = element;
	const { InspectorControls, useBlockProps } = blockEditor;
	const { PanelBody, TextControl, SelectControl } = components;
	const { __ } = i18n;

	const FORMATOS = [
		{ value: 'auto', label: __('Responsivo (automático)', 'u_correio68'), altura: 250 },
		{ value: 'horizontal', label: __('Horizontal (faixa)', 'u_correio68'), altura: 90 },
		{ value: 'vertical', label: __('Vertical (arranha-céu)', 'u_correio68'), altura: 600 },
		{ value: 'retangulo', label: __('Retângulo', 'u_correio68'), altura: 250 },
		{ value: 'materia', label: __('No artigo (in-article)', 'u_correio68'), altura: 250 },
	];

	blocks.registerBlockType('seisdeagosto/anuncio', {
		edit: function (props) {
			const { attributes, setAttributes } = props;
			const slot = attributes.slot || '';
			const formato = FORMATOS.find(function (f) { return f.value === attributes.formato; }) || FORMATOS[0];
			const slotValido = /^\d{6,20}$/.test(slot);

			const blockProps = useBlockProps({
				style: {
					minHeight: Math.min(formato.altura, 300) + 'px',
					border: '2px dashed #c3c4c7',
					borderRadius: '4px',
					background: '#f6f7f7',
					display: 'flex',
					flexDirection: 'column',
					alignItems: 'center',
					justifyContent: 'center',
					textAlign: 'center',
					padding: '12px',
					color: '#50575e',
					fontSize: '13px',
				},
			});

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Anúncio', 'u_correio68'), initialOpen: true },
						el(TextControl, {
							label: __('ID do bloco de anúncio (data-ad-slot)', 'u_correio68'),
							help: __('Só os números. Crie o bloco em AdSense → Anúncios → Por bloco de anúncios.', 'u_correio68'),
							value: slot,
							onChange: function (v) { setAttributes({ slot: (v || '').replace(/\D/g, '') }); },
						}),
						el(SelectControl, {
							label: __('Formato', 'u_correio68'),
							value: formato.value,
							options: FORMATOS.map(function (f) { return { value: f.value, label: f.label }; }),
							onChange: function (v) { setAttributes({ formato: v }); },
						})
					)
				),
				el(
					'div',
					blockProps,
					el('strong', null, __('Anúncio AdSense', 'u_correio68')),
					el('span', null, formato.label),
					el(
						'span',
						{ style: { marginTop: '4px', color: slotValido ? '#50575e' : '#b32d2e' } },
						slotValido
							? __('ID: ', 'u_correio68') + slot
							: __('Informe o ID do bloco de anúncio na barra lateral →', 'u_correio68')
					)
				)
			);
		},
		save: function () {
			return null; // Renderizado no servidor.
		},
	});
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);
