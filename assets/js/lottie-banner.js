/**
 * Inicializa banners Lottie (ex.: banner WordBlaster da home).
 *
 * O container do banner pode informar o JSON em data-lottie-src. Quando o
 * atributo não existe, usa o id do elemento (ex.: #wb-banner-1280x200 carrega
 * assets/lottie/wb-banner-1280x200.json).
 *
 * Se a biblioteca Lottie já estiver carregada na página, ela é reaproveitada.
 * Caso contrário, a cópia local do tema (lottie.min.js) é carregada como
 * fallback — assim o banner não depende de CDN/domínio externo.
 */
(function () {
	'use strict';

	var settings = window.u68LottieBanner || {};
	var baseUrl = settings.baseUrl || '';
	var libraryUrl = settings.libraryUrl || '';
	var fallbackSrc = settings.fallback || '';
	var libraryRequested = false;
	var started = false;

	function getBanners() {
		return document.querySelectorAll('[data-lottie-src], [id^="wb-banner-"]');
	}

	function resolveSrc(element) {
		var src = element.getAttribute('data-lottie-src');

		if (!src && element.id && element.id.indexOf('wb-banner-') === 0) {
			src = element.id + '.json';
		}

		if (!src) {
			src = fallbackSrc;
		}

		if (!src) {
			return '';
		}

		if (/^(https?:)?\/\//i.test(src) || src.charAt(0) === '/') {
			return src;
		}

		return baseUrl + src;
	}

	function start() {
		if (started || !window.lottie) {
			return;
		}

		var banners = getBanners();

		if (!banners.length) {
			return;
		}

		started = true;

		Array.prototype.forEach.call(banners, function (element) {
			if (element.getAttribute('data-u68-lottie') === 'ready') {
				return;
			}

			var src = resolveSrc(element);

			if (!src) {
				return;
			}

			element.setAttribute('data-u68-lottie', 'ready');

			window.lottie.loadAnimation({
				container: element,
				renderer: 'svg',
				loop: true,
				autoplay: true,
				path: src
			});
		});
	}

	function ensureLibrary() {
		if (window.lottie) {
			start();
			return;
		}

		if (libraryRequested) {
			return;
		}

		libraryRequested = true;

		var script = document.createElement('script');
		script.src = libraryUrl;
		script.async = true;
		script.onload = start;
		document.head.appendChild(script);
	}

	function boot() {
		if (!getBanners().length) {
			return;
		}

		ensureLibrary();

		// A biblioteca externa pode terminar de carregar depois deste script.
		window.addEventListener('load', start);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
