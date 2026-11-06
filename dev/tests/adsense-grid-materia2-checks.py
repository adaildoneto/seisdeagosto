# Verificações das posições "Entre notícias" na Grade de Notícias e do 2º anúncio na matéria.
# Pré-requisitos no site de teste: AdSense configurado com slots, a reportagem longa (18
# parágrafos) em /2026/11/06/reportagem-longa/, a categoria Política com template em Grade.
# Uso: BASE_URL=http://127.0.0.1:9400 python3 dev/tests/adsense-grid-materia2-checks.py
import re, sys, urllib.request

import os
B = os.environ.get('BASE_URL', 'http://127.0.0.1:9400')
HDR = {'Cookie': 'playground_auto_login_already_happened=1'}
ok = fail = 0


def get(path):
    req = urllib.request.Request(B + path, headers=HDR)
    return urllib.request.urlopen(req, timeout=120).read().decode('utf-8', 'ignore')


def check(cond, msg):
    global ok, fail
    if cond:
        ok += 1
        print('PASS', msg)
    else:
        fail += 1
        print('FAIL', msg)


def grid_items_before_ad(h):
    """Quantos itens diretos da primeira .news-grid vêm antes do anúncio (ou None)."""
    g = re.search(r'<div\s[^>]*class="[^"]*\bnews-grid\b[^"]*"[^>]*>', h)
    if not g:
        return None, 0
    depth, items, before = 0, 0, None
    for t in re.finditer(r'<(/?)div\b[^>]*>', h[g.end():]):
        if t.group(1) == '':
            if depth == 0:
                if 'sda-ad-item' in t.group(0):
                    before = items
                else:
                    items += 1
            depth += 1
        elif depth == 0:
            break
        else:
            depth -= 1
    return before, items


# Matéria longa
h = get('/2026/11/06/reportagem-longa/')
check(h.count('data-sda-position="materia"') == 1, 'matéria longa: 1º anúncio uma vez')
check(h.count('data-sda-position="materia-2"') == 1, 'matéria longa: 2º anúncio uma vez')
body = h[h.find('wp-block-post-content'):]
pos = [(m.start(), m.group(1)) for m in re.finditer(r'Parágrafo (\d+) da reportagem', body)]
def after(marker):
    i = body.find(marker)
    prev = [int(n) for p, n in pos if p < i]
    return max(prev) if prev else 0
a1, a2 = after('data-sda-position="materia"'), after('data-sda-position="materia-2"')
check(a1 == 3, f'matéria longa: 1º anúncio após o parágrafo 3 (foi {a1})')
check(a2 == 11, f'matéria longa: 2º após o parágrafo 11, pulando a citação após o 10º (foi {a2})')
check(a2 - a1 >= 4, f'matéria longa: 2º a pelo menos 4 parágrafos do 1º ({a2 - a1})')
for marker in ('data-sda-position="materia"', 'data-sda-position="materia-2"'):
    i = body.find(marker)
    nxt = re.search(r'<(p|h2|blockquote|figure|ul|ol)\b', body[body.find('</div>', body.find('</script>', i)):])
    check(nxt and nxt.group(1) == 'p', f'{marker.split("=")[1]}: o bloco seguinte é parágrafo ({nxt.group(1) if nxt else "?"})')
check(h.count('data-ad-slot="2222222222"') == 2, 'matéria longa: os dois usam o mesmo bloco de anúncio')

# Matéria curta (7 parágrafos): só o 1º
h = get('/2026/11/06/materia-longa-adsense/')
check(h.count('data-sda-position="materia"') == 1 and 'materia-2' not in h, 'matéria de 7 parágrafos: só 1 anúncio dentro do texto')

# Categoria com Grade de Notícias (template no banco)
h = get('/category/politica/')
check(h.count('sda-ad-item') == 1, 'categoria (Grade de Notícias): anúncio entre notícias uma vez')
before, items = grid_items_before_ad(h)
check(before == 6, f'categoria: anúncio depois da 6ª notícia (antes dele: {before}, total na grade: {items})')
check('class="col-12 sda-ad-item"' in h, 'categoria: item do anúncio ocupa a linha inteira (col-12)')

# Capa com Grade de 9 itens
h = get('/')
before, items = grid_items_before_ad(h)
check(before == 6, f'capa: anúncio depois da 6ª notícia da Grade (antes dele: {before}, total: {items})')
check(h.count('sda-ad-item') == 1, 'capa: só uma vez, mesmo com outras listas na página')

print(f'\n{ok} passaram, {fail} falharam')
sys.exit(1 if fail else 0)
