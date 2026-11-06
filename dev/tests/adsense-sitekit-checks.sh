#!/bin/bash
# Verificações do tema com o ID do AdSense só no Site Kit (estado do Portal do Rosas):
# campo do tema vazio, Site Kit com o módulo AdSense ativo, clientID e inserção de código ligada.
#
# Pré-requisitos no site de teste: o estado acima, slots preenchidos nas posições do tema,
# a reportagem longa em POST_PATH, um edital em EDITAL_PATH e o Site Kit (ou um simulado
# que respeite o filtro googlesitekit_adsense_tag_blocked).
#
# Uso: BASE_URL=http://127.0.0.1:9400 ADMIN_COOKIES=cookies.txt bash dev/tests/adsense-sitekit-checks.sh /tmp/pasta
S="${1:-$(mktemp -d)}"; B="${BASE_URL:-http://127.0.0.1:9400}"
AN=(-b playground_auto_login_already_happened=1)
POST="$B${POST_PATH:-/2026/11/06/reportagem-longa/}"
EDITAL="$B${EDITAL_PATH:-/edital/edital-teste-adsense/}"
ID=ca-pub-1705380769951157
pass=0; fail=0
ok(){ if eval "$1"; then echo "PASS $2"; pass=$((pass+1)); else echo "FAIL $2"; fail=$((fail+1)); fi; }
get(){ curl -sL "${AN[@]}" -o "$S/h.html" "$1"; }
tags(){ grep -o 'adsbygoogle.js?client=[^"&]*' "$S/h.html" | wc -l; }

code=$(curl -s "${AN[@]}" -o "$S/ads.txt" -w "%{http_code}" "$B/ads.txt")
ok "[ '$code' = 200 ] && [ \"\$(cat $S/ads.txt)\" = 'google.com, pub-1705380769951157, DIRECT, f08c47fec0942fa0' ]" "ads.txt gerado com o ID do Site Kit, sem configurar o tema (HTTP $code)"

get "$POST"
ok "[ \$(tags) -eq 1 ]" "matéria (visitante): exatamente 1 tag do AdSense ($(tags))"
ok "grep -q 'host=ca-host-pub-2644536267352236' $S/h.html" "matéria: a tag é a do Site Kit (o tema não duplicou)"
ok "grep -q '<meta name=\"google-adsense-account\" content=\"$ID\">' $S/h.html" "matéria: meta de verificação com o ID do Site Kit"
ok "grep -q 'data-ad-client=\"$ID\"' $S/h.html && grep -q 'data-sda-position=\"materia\"' $S/h.html" "matéria: posições fixas do tema usam o ID do Site Kit"

curl -sL -b "${ADMIN_COOKIES:-$S/cj}" -o "$S/h.html" "$POST"
ok "[ \$(tags) -eq 0 ] && ! grep -q 'data-sda-position' $S/h.html" "logado: nenhuma tag (nem do Site Kit) e nenhum anúncio"

get "$EDITAL"
ok "[ \$(tags) -eq 0 ]" "edital: a tag do Site Kit é bloqueada"
get "$B/nao-existe-xyz/"
ok "[ \$(tags) -eq 0 ]" "404: a tag do Site Kit é bloqueada"
get "$B/?s=zzzznadaencontrado"
ok "[ \$(tags) -eq 0 ]" "busca vazia: a tag do Site Kit é bloqueada"
get "$B/"
ok "[ \$(tags) -eq 1 ]" "capa (visitante): 1 tag"

echo; echo "$pass passaram, $fail falharam"
[ "$fail" -eq 0 ]
