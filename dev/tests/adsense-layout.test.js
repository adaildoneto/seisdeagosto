/**
 * Teste de layout do AdSense: os anúncios não podem deformar o conteúdo.
 *
 * Roda contra um WordPress de teste com o tema ativo e o AdSense configurado
 * (veja README → "Testar o tema localmente"). O script do Google é interceptado
 * e trocado por um AdSense simulado, que:
 *   - preenche cada <ins class="adsbygoogle"> com um anúncio do tamanho pedido;
 *   - imita os Anúncios automáticos, inserindo .google-auto-placed DENTRO dos
 *     grids e linhas do tema (o pior caso).
 *
 * Para cada página x largura de tela x tamanho de anúncio, compara com a mesma
 * página sem anúncios:
 *   1. sem rolagem horizontal;
 *   2. colunas, cards, miniaturas e parágrafos com a mesma largura (±1 px);
 *   3. nenhum anúncio por cima de texto ou imagem, nem dentro do cabeçalho ou de
 *      um elemento fixo (sticky/fixed), que cobriria o conteúdo ao rolar;
 *   4. CLS: as posições fixas do tema (espaço reservado) não aumentam o CLS em 0,05
 *      ou mais em relação à página sem anúncios. Medido sem Anúncios automáticos,
 *      porque o Google os insere depois do carregamento e o tema não tem como
 *      reservar espaço para eles; o CLS com automáticos é só informado.
 *
 * Uso:
 *   BASE_URL=http://127.0.0.1:9400 POST_PATH=/2026/11/06/materia-longa-adsense/ \
 *   LIST_PATH=/?tag=acre ADS_TOGGLE_URL=http://127.0.0.1:9400/set-ads.php \
 *   node dev/tests/adsense-layout.test.js
 *
 * ADS_TOGGLE_URL: endpoint do ambiente de teste que liga (?on=1) e desliga (?on=0)
 * a opção "Ativar anúncios", para medir a página sem anúncios.
 */
const { chromium } = require('playwright');

const BASE = process.env.BASE_URL || 'http://127.0.0.1:9400';
const TOGGLE = process.env.ADS_TOGGLE_URL || BASE + '/set-ads.php';
const PAGES = {
	capa: '/',
	materia: process.env.POST_PATH || '/',
	lista: process.env.LIST_PATH || '/?tag=acre',
};
const VIEWPORTS = { desktop: [1366, 900], tablet: [820, 1100], celular: [390, 844] };
// "fit": como o AdSense real (nunca mais largo que o espaço). "fixo": pior caso, largura fixa.
const SIZES = [
	['300x250', 300, 250, 'fit'],
	['336x280', 336, 280, 'fit'],
	['728x90', 728, 90, 'fit'],
	['970x250', 970, 250, 'fit'],
	['1200x300 fixo', 1200, 300, 'fixo'],
];

// Elementos do conteúdo cujas larguras não podem mudar por causa dos anúncios.
const MEASURE = [
	'#primary', '.content-area', '.widget-area', 'main .wp-block-post-content > p',
	'.news-grid > [class*="col"]', '.destaques-home-wrapper .card',
	'.destaque-misto-wrapper .card', '.destaque-misto-wrapper .list-thumb',
	'.destaque-misto-wrapper .list-content', 'li.wp-block-post:not(.sda-ad-item)',
	'.wp-block-post-featured-image', '.wp-block-post-title',
];

const MOCK = `
(function () {
	var cfg = window.__SDA || { w: 300, h: 250, mode: 'fit', auto: false, unfilled: false };
	function creative(ins) {
		var room = ins.parentElement ? ins.parentElement.clientWidth : cfg.w;
		var w = cfg.mode === 'fit' ? Math.min(cfg.w, room || cfg.w) : cfg.w;
		var d = document.createElement('div');
		d.className = 'sda-mock-creative';
		d.style.cssText = 'display:block;margin:0 auto;background:#fbbc04;width:' + w + 'px;height:' + cfg.h + 'px';
		return d;
	}
	function fill(ins) {
		if (ins.getAttribute('data-adsbygoogle-status')) return;
		ins.setAttribute('data-adsbygoogle-status', 'done');
		if (cfg.unfilled) { ins.setAttribute('data-ad-status', 'unfilled'); return; }
		ins.setAttribute('data-ad-status', 'filled');
		ins.appendChild(creative(ins));
	}
	function autoAd() {
		var wrap = document.createElement('div');
		wrap.className = 'google-auto-placed ap_container';
		wrap.style.cssText = 'width:100%;height:auto;clear:both;text-align:center;';
		var ins = document.createElement('ins');
		ins.className = 'adsbygoogle adsbygoogle-noablate';
		ins.style.cssText = 'display:block;margin:auto;background-color:transparent;';
		wrap.appendChild(ins);
		return wrap;
	}
	function insertAuto() {
		var spots = [
			['.news-grid', 1], ['.destaques-home-wrapper .row', 1], ['.destaque-misto-wrapper .row .row', 1],
			['.destaque-misto-wrapper .list-item', 1], ['.wp-block-post-template', 2],
			['main .wp-block-post-content', 2], ['.widget-area', 0]
		];
		spots.forEach(function (s) {
			var parent = document.querySelector(s[0]);
			if (!parent) return;
			var ref = parent.children[s[1]] || null;
			parent.insertBefore(autoAd(), ref);
		});
	}
	function run() {
		if (cfg.auto) insertAuto();
		document.querySelectorAll('ins.adsbygoogle').forEach(fill);
	}
	window.adsbygoogle = { loaded: true, push: function () { document.querySelectorAll('ins.adsbygoogle').forEach(fill); } };
	// O script é async e pode rodar antes de o <body> existir: como o Google, espera o DOM.
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', run); } else { run(); }
})();`;

async function measure(page) {
	return page.evaluate((selectors) => {
		const out = { widths: {}, scrollW: document.documentElement.scrollWidth, innerW: window.innerWidth };
		selectors.forEach((sel) => {
			out.widths[sel] = Array.from(document.querySelectorAll(sel)).slice(0, 12)
				.filter((e) => !e.closest('.sda-ad, .google-auto-placed'))
				.map((e) => Math.round(e.getBoundingClientRect().width));
		});
		const ads = Array.from(document.querySelectorAll('.sda-ad, .google-auto-placed'))
			.filter((a) => getComputedStyle(a).display !== 'none' && a.getBoundingClientRect().height > 0);
		out.ads = ads.length;
		out.autos = ads.filter((a) => a.classList.contains('google-auto-placed')).length;
		out.adsMaisLargos = ads.filter((a) => a.getBoundingClientRect().right > window.innerWidth + 1).length;
		// Nenhum anúncio dentro do cabeçalho ou de um elemento fixo/sticky (ele cobriria o conteúdo ao rolar).
		out.adsFixos = ads.filter((a) => {
			if (a.closest('header')) return true;
			for (let e = a.parentElement; e; e = e.parentElement) { const p = getComputedStyle(e).position; if (p === 'sticky' || p === 'fixed') return true; }
			return false;
		}).length;
		const content = Array.from(document.querySelectorAll('main p, main h1, main h2, main h3, main h5, main img, .card, .news-grid img, .list-thumb, .wp-block-post-title, .widget-area .widget'))
			.filter((e) => !e.closest('.sda-ad, .google-auto-placed') && e.getBoundingClientRect().width > 0);
		const overlaps = [];
		ads.forEach((a) => {
			const r = a.getBoundingClientRect();
			content.forEach((e) => {
				if (a.contains(e) || e.contains(a)) return;
				const c = e.getBoundingClientRect();
				const x = Math.min(r.right, c.right) - Math.max(r.left, c.left);
				const y = Math.min(r.bottom, c.bottom) - Math.max(r.top, c.top);
				if (x > 1 && y > 1) overlaps.push((a.className.split(' ')[0]) + ' x ' + e.tagName.toLowerCase() + '.' + String(e.className).split(' ')[0]);
			});
		});
		out.overlaps = Array.from(new Set(overlaps)).slice(0, 5);
		out.cls = window.__sdaCLS || 0;
		out.unfilledVisiveis = Array.from(document.querySelectorAll('.sda-ad')).filter((a) => a.querySelector('ins[data-ad-status="unfilled"]') && a.getBoundingClientRect().height > 0).length;
		return out;
	}, MEASURE);
}

function diffWidths(base, test) {
	const diffs = [];
	Object.keys(base).forEach((sel) => {
		const a = base[sel], b = test[sel] || [];
		if (a.length !== b.length) { diffs.push(sel + ': ' + a.length + ' -> ' + b.length + ' elementos'); return; }
		a.forEach((w, i) => { if (Math.abs(w - b[i]) > 1) diffs.push(sel + '[' + i + ']: ' + w + ' -> ' + b[i] + 'px'); });
	});
	return diffs;
}

async function open(browser, vp, path, cfg) {
	const ctx = await browser.newContext({ viewport: { width: vp[0], height: vp[1] } });
	await ctx.addCookies([{ name: 'playground_auto_login_already_happened', value: '1', url: BASE }]);
	await ctx.route('**/pagead2.googlesyndication.com/**', (r) => r.fulfill({ contentType: 'application/javascript', body: cfg ? MOCK : '' }));
	await ctx.addInitScript((c) => {
		window.__SDA = c;
		window.__sdaCLS = 0;
		new PerformanceObserver((l) => { l.getEntries().forEach((e) => { if (!e.hadRecentInput) window.__sdaCLS += e.value; }); })
			.observe({ type: 'layout-shift', buffered: true });
	}, cfg || {});
	const page = await ctx.newPage();
	await page.goto(BASE + path, { waitUntil: 'networkidle', timeout: 90000 });
	await page.waitForTimeout(400);
	return { ctx, page };
}

(async () => {
	// O cookie evita o login automático do WordPress Playground (inofensivo em outros ambientes).
	const http = async (u) => (await fetch(u, { headers: { cookie: 'playground_auto_login_already_happened=1' } })).text();
	const browser = await chromium.launch();
	let pass = 0, fail = 0;
	const check = (ok, msg) => { if (ok) { pass++; } else { fail++; console.log('FAIL ' + msg); } };

	for (const [pname, path] of Object.entries(PAGES)) {
		for (const [vname, vp] of Object.entries(VIEWPORTS)) {
			await http(TOGGLE + '?on=0');
			const base = await open(browser, vp, path, null);
			const b = await measure(base.page);
			await base.ctx.close();
			check(b.scrollW <= b.innerW, `${pname}/${vname} sem anúncios já tem rolagem horizontal (${b.scrollW} > ${b.innerW})`);

			await http(TOGGLE + '?on=1');
			for (const [sname, w, h, mode] of SIZES) {
				const label = `${pname}/${vname}/${sname}`;
				const t = await open(browser, vp, path, { w, h, mode, auto: true, unfilled: false });
				const m = await measure(t.page);
				await t.ctx.close();
				const diffs = diffWidths(b.widths, m.widths);
				check(m.ads > 0, `${label}: nenhum anúncio na página`);
				check(m.autos > 0, `${label}: nenhum Anúncio automático simulado foi inserido`);
				check(m.scrollW <= m.innerW, `${label}: rolagem horizontal (${m.scrollW} > ${m.innerW})`);
				check(m.adsMaisLargos === 0, `${label}: ${m.adsMaisLargos} anúncio(s) passando da tela`);
				check(m.adsFixos === 0, `${label}: ${m.adsFixos} anúncio(s) dentro do cabeçalho ou de elemento fixo`);
				check(diffs.length === 0, `${label}: larguras mudaram -> ${diffs.slice(0, 4).join(' | ')}`);
				check(m.overlaps.length === 0, `${label}: anúncio sobre conteúdo -> ${m.overlaps.join(' | ')}`);
				// CLS: só as posições fixas (espaço reservado), sem Anúncios automáticos.
				const f = await open(browser, vp, path, { w, h, mode, auto: false, unfilled: false });
				const mf = await measure(f.page);
				await f.ctx.close();
				check(mf.cls - b.cls < 0.05, `${label}: posições fixas aumentaram o CLS ${b.cls.toFixed(3)} -> ${mf.cls.toFixed(3)}`);
				console.log(`ok? ${label}: anúncios=${m.ads} (automáticos=${m.autos}) rolagem=${m.scrollW > m.innerW ? 'SIM' : 'não'} larguras=${diffs.length ? diffs.length + ' mudaram' : 'iguais'} sobreposições=${m.overlaps.length} | CLS sem anúncios=${b.cls.toFixed(3)} posições fixas=${mf.cls.toFixed(3)} com automáticos=${m.cls.toFixed(3)} (informativo)`);
			}
			const u = await open(browser, vp, path, { w: 300, h: 250, mode: 'fit', auto: false, unfilled: true });
			const mu = await measure(u.page);
			await u.ctx.close();
			check(mu.unfilledVisiveis === 0, `${pname}/${vname}: ${mu.unfilledVisiveis} espaço(s) vazio(s) visível(is) com anúncio unfilled`);
		}
	}
	await http(TOGGLE + '?on=1');
	await browser.close();
	console.log(`\n${pass} passaram, ${fail} falharam`);
	process.exit(fail ? 1 : 0);
})();
