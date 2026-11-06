#!/bin/bash
# Verificações HTTP do módulo AdSense num WordPress de teste.
#
# Pré-requisitos no site de teste: ID ca-pub-1234567890123456 e IDs nas 6 posições
# (Personalizar → AdSense), uma matéria com 7+ parágrafos (com imagem e intertítulo),
# um edital publicado e um widget na barra lateral direita.
#
# Uso: BASE_URL=http://127.0.0.1:9400 POST_PATH=/2026/11/06/materia-longa-adsense/ \
#      EDITAL_PATH=/edital/edital-teste-adsense/ ADMIN_COOKIES=cookies.txt \
#      bash dev/tests/adsense-http-checks.sh /tmp/pasta-temporaria
S="${1:-$(mktemp -d)}"; B="${BASE_URL:-http://127.0.0.1:9400}"
AN=(-b playground_auto_login_already_happened=1)
POST="$B${POST_PATH:-/2026/11/06/materia-longa-adsense/}"
EDITAL="$B${EDITAL_PATH:-/edital/edital-teste-adsense/}"
pass=0; fail=0
ok(){ if eval "$1"; then echo "PASS $2"; pass=$((pass+1)); else echo "FAIL $2"; fail=$((fail+1)); fi; }
get(){ curl -sL "${AN[@]}" -o "$S/h.html" "$1"; }
SCRIPT='pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1234567890123456'

get "$B/"
ok "[ \$(grep -c '$SCRIPT' $S/h.html) -eq 1 ]" "capa (visitante): script carregado uma única vez"
ok "grep -q 'crossorigin=\"anonymous\"' $S/h.html" "script com crossorigin=anonymous"
ok "grep -q '<meta name=\"google-adsense-account\" content=\"ca-pub-1234567890123456\">' $S/h.html" "meta de verificação"
ok "grep -q 'css/adsense.css' $S/h.html" "CSS dos anúncios carregado"
ok "[ \$(grep -c 'data-sda-position=\"capa\"' $S/h.html) -eq 1 ]" "capa: posição Capa uma vez"
ok "[ \$(grep -c 'data-sda-position=\"topo\"' $S/h.html) -eq 1 ]" "capa: posição Topo uma vez"
ok "python3 -c \"import sys,re;h=open('$S/h.html').read();i=h.find('data-sda-position=\\\"topo\\\"');b=h[:i];sys.exit(0 if i>0 and len(re.findall(r'<header[\\s>]',b))==b.count('</header>') else 1)\"" "Topo fora de qualquer <header> (não fica no cabeçalho fixo)"

get "$POST"
ok "[ \$(grep -c 'data-sda-position=\"materia\"' $S/h.html) -eq 1 ]" "matéria: anúncio dentro da matéria uma vez"
ok "python3 -c \"import sys,re;h=open('$S/h.html').read();i=h.find('data-sda-position=\\\"materia\\\"');a=h.rfind('Quinto parágrafo',0,i);b=h.find('Sexto parágrafo',i);sys.exit(0 if a>0 and b>0 and h.find('Intertítulo',a,i)<0 else 1)\"" "matéria: anúncio entre o 5º e o 6º parágrafo (pulou imagem e título)"
ok "[ \$(grep -c 'data-sda-position=\"fim\"' $S/h.html) -eq 1 ]" "matéria: fim da matéria uma vez"
ok "[ \$(grep -c 'data-sda-position=\"lateral\"' $S/h.html) -eq 1 ]" "matéria: barra lateral uma vez"
ok "grep -q 'data-ad-layout=\"in-article\"' $S/h.html" "matéria: formato No artigo"

get "$B/?tag=acre"
ok "[ \$(grep -c 'sda-ad-item' $S/h.html) -eq 1 ]" "lista (tag): anúncio entre notícias uma vez"
ok "[ \$(python3 -c \"h=open('$S/h.html').read();print(h[:h.find('sda-ad-item')].count('class=\\\"wp-block-post post-'))\") -eq 4 ]" "lista: antes da 5ª notícia"

get "$EDITAL"
ok "! grep -q '$SCRIPT' $S/h.html && ! grep -q 'sda-ad' $S/h.html" "edital: sem script e sem anúncios"
get "$B/nao-existe-xyz/"
ok "! grep -q '$SCRIPT' $S/h.html" "404: sem script"
get "$B/?s=zzzznadaencontrado"
ok "! grep -q '$SCRIPT' $S/h.html" "busca vazia: sem script"
get "$B/?s=teste"
ok "grep -q '$SCRIPT' $S/h.html" "busca com resultados: com script"

curl -sL -b "${ADMIN_COOKIES:-$S/cj}" -o "$S/h.html" "$POST"
ok "! grep -q '$SCRIPT' $S/h.html && ! grep -q 'data-sda-position' $S/h.html" "logado: sem script e sem anúncios"
ok "grep -q 'google-adsense-account' $S/h.html" "logado: meta de verificação continua"

code=$(curl -s "${AN[@]}" -o "$S/ads.txt" -w "%{http_code} %{content_type}" "$B/ads.txt")
ok "[ \"$code\" = '200 text/plain; charset=utf-8' ]" "/ads.txt responde 200 text/plain ($code)"
ok "[ \"\$(cat $S/ads.txt)\" = 'google.com, pub-1234567890123456, DIRECT, f08c47fec0942fa0' ]" "/ads.txt com a linha certa"
curl -s "${AN[@]}" -o "$S/robots.txt" "$B/robots.txt"
ok "grep -q 'User-agent: Mediapartners-Google' $S/robots.txt" "/robots.txt libera o Mediapartners-Google"
ok "grep -q 'Sitemap:' $S/robots.txt || grep -q 'Disallow: /wp-admin/' $S/robots.txt" "/robots.txt mantém as regras do WordPress"

echo; echo "$pass passaram, $fail falharam"
[ "$fail" -eq 0 ]
