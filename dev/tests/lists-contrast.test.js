/**
 * Contraste e numeração das listas.
 *
 * Verifica, numa matéria com listas em vários contextos (bloco Lista, Grupo, Citação,
 * Colunas, HTML sem classe, classe "listanumero") e no bloco "Mais lidas":
 *   1. texto de cada item de lista do conteúdo com contraste >= 4,5:1;
 *   2. número decorado das listas numeradas (círculo do ::before) com contraste >= 3:1
 *      (texto grande e em negrito, WCAG 1.4.3);
 *   3. nenhuma numeração duplicada (marcador nativo + círculo ao mesmo tempo);
 *   4. números do "Mais lidas" com contraste >= 4,5:1 sobre a cor do selo.
 *
 * Uso: BASE_URL=http://127.0.0.1:9400 POST_PATH=/2026/11/12/teste-listas/ \
 *      FRONT_PATH=/ node dev/tests/lists-contrast.test.js
 * Para o site real, rode com BASE_URL e POST_PATH de uma matéria com listas.
 */
const { chromium } = require('playwright');

const BASE = process.env.BASE_URL || 'http://127.0.0.1:9400';
const POST = process.env.POST_PATH || '/2026/11/12/teste-listas/';
const FRONT = process.env.FRONT_PATH || '/';

const MEASURE = () => {
	const lum = (c) => {
		const m = (c || '').match(/[\d.]+/g);
		if (!m) return 1;
		const v = m.slice(0, 3).map((x) => { x = x / 255; return x <= 0.03928 ? x / 12.92 : Math.pow((x + 0.055) / 1.055, 2.4); });
		return 0.2126 * v[0] + 0.7152 * v[1] + 0.0722 * v[2];
	};
	const ratio = (a, b) => { const x = lum(a), y = lum(b); return (Math.max(x, y) + 0.05) / (Math.min(x, y) + 0.05); };
	const bg = (e) => { for (; e; e = e.parentElement) { const c = getComputedStyle(e).backgroundColor; if (!/rgba\(.*, 0\)$/.test(c) && c !== 'transparent') return c; } return 'rgb(255, 255, 255)'; };
	const label = (li) => { const prev = li.closest('ol, ul').parentElement.previousElementSibling; const p = li.closest('.wp-block-group, .wp-block-quote, .wp-block-column'); return (p ? p.className.split(' ')[0] + ' > ' : '') + li.parentElement.tagName.toLowerCase() + (li.parentElement.className ? '.' + li.parentElement.className.split(' ')[0] : '') + ' "' + li.firstChild.textContent.trim().slice(0, 22) + '"'; };
	const out = { itens: [], rank: [] };
	const content = document.querySelector('.wp-block-post-content, .entry-content');
	if (content) {
		content.querySelectorAll('li').forEach((li) => {
			if (li.closest('.top-most-read, .news-grid, .sda-ad')) return;
			const cs = getComputedStyle(li);
			const before = getComputedStyle(li, '::before');
			const badge = before.content && before.content !== 'none' && before.display !== 'none';
			out.itens.push({
				nome: label(li),
				texto: ratio(cs.color, bg(li)),
				marcadorNativo: getComputedStyle(li.parentElement).listStyleType,
				circulo: badge ? ratio(before.color, before.backgroundColor) : null,
			});
		});
	}
	document.querySelectorAll('.top-most-read .rank').forEach((r) => {
		const cs = getComputedStyle(r);
		out.rank.push(ratio(cs.color, cs.backgroundColor));
	});
	return out;
};

(async () => {
	const b = await chromium.launch();
	const ctx = await b.newContext({ viewport: { width: 1366, height: 900 } });
	await ctx.addCookies([{ name: 'playground_auto_login_already_happened', value: '1', url: BASE }]);
	const p = await ctx.newPage();
	let pass = 0, fail = 0;
	const check = (ok, msg) => { if (ok) pass++; else fail++; console.log((ok ? 'PASS ' : 'FAIL ') + msg); };

	await p.goto(BASE + POST, { waitUntil: 'networkidle', timeout: 90000 });
	const m = await p.evaluate(MEASURE);
	check(m.itens.length > 0, `matéria: ${m.itens.length} itens de lista encontrados`);
	m.itens.forEach((i) => {
		check(i.texto >= 4.5, `texto ${i.nome}: ${i.texto.toFixed(2)}:1`);
		if (i.circulo !== null) {
			check(i.circulo >= 3, `número decorado ${i.nome}: ${i.circulo.toFixed(2)}:1`);
			check(i.marcadorNativo === 'none', `sem numeração duplicada ${i.nome} (marcador nativo: ${i.marcadorNativo})`);
		}
	});

	await p.goto(BASE + FRONT, { waitUntil: 'networkidle', timeout: 90000 });
	const f = await p.evaluate(MEASURE);
	check(f.rank.length > 0, `Mais lidas: ${f.rank.length} números encontrados`);
	if (f.rank.length) check(Math.min(...f.rank) >= 4.5, `Mais lidas: contraste dos números ${Math.min(...f.rank).toFixed(2)}:1`);

	console.log(`\n${pass} passaram, ${fail} falharam`);
	await b.close();
	process.exit(fail ? 1 : 0);
})();
