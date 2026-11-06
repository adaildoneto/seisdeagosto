# AdSense no Portal do Rosas: plano de ação

Auditoria de **portaldorosas.com.br** em 06/11/2026, feita na estrutura pública do site.
Os números de receita da conta (RPM, CTR, visibilidade) ainda não foram analisados: o
conector do AdSense não estava autenticado. Veja "Próxima etapa" no fim.

## O que a auditoria encontrou

| # | Achado | Impacto |
|---|---|---|
| 1 | **`/ads.txt` responde 404** (nos endereços com e sem `www`) | Alto. O AdSense marca "Ganhos em risco" e parte dos anunciantes não compra o espaço |
| 2 | Tema SeisdeAgosto **1.0.0** em produção, sem as correções de segurança, o módulo de AdSense e a correção do "pulo" da capa no celular | Alto |
| 3 | AdSense só com **Anúncios automáticos**, tag inserida pelo **Site Kit** (`ca-pub-1705380769951157`); nenhum bloco de anúncio fixo | Médio. O Google decide tudo, sem garantia de anúncio nos melhores lugares |
| 4 | Matérias **longas**: de 9 a 34 parágrafos, com citações e intertítulos | Oportunidade: espaço para 2 anúncios no texto |
| 5 | Categorias e capa montadas com o bloco **Grade de Notícias** | O tema 1.2.0 passou a inserir "Entre notícias" nessa grade |
| 6 | Cache **Jetpack Boost** ativo | Limpar o cache depois de publicar o tema |
| 7 | `robots.txt` (Yoast) libera todos os robôs, inclusive o do AdSense | Ok |

## Passo a passo (nesta ordem)

### 1. Publicar o tema (1.2.1 ou mais novo)
Faça o merge no `main` (se o site puxa o tema do GitHub) ou instale o `.zip` em
**Aparência → Temas → Adicionar novo → Enviar tema**. Depois, em **Jetpack → Boost**,
limpe o cache.

**Só isso já resolve a configuração no WordPress.** A partir da 1.2.1, com o campo do
tema vazio, o tema usa o ID que o Site Kit já tem (`ca-pub-1705380769951157`):
- o `/ads.txt` passa a responder com a linha certa;
- entra a meta de verificação;
- o Site Kit continua inserindo a tag, sem duplicar;
- o tema bloqueia a tag do Site Kit para usuários logados, nas Publicações Legais, nas
  páginas 404 e na busca vazia.

**Confira logo depois:**
- `https://portaldorosas.com.br/ads.txt` deve mostrar
  `google.com, pub-1705380769951157, DIRECT, f08c47fec0942fa0`;
- no código-fonte de uma matéria (sem estar logado), `adsbygoogle.js` aparece **uma vez**.

### 2. (Opcional) Ajustes no Personalizador
**Aparência → Personalizar → 📢 AdSense**. Não é preciso colar o ID: o campo vazio usa o
do Site Kit. Os padrões já são os recomendados:

| Campo | Padrão |
|---|---|
| Ativar anúncios | ligado |
| Anúncio da matéria após o parágrafo nº | 3 |
| 2º anúncio nas matérias longas | ligado, após o 10º parágrafo |
| IDs dos blocos de anúncio | vazios até você criá-los no passo 3 |

### 3. Criar os blocos de anúncio no AdSense
**AdSense → Anúncios → Por bloco de anúncios → Criar**. Cinco blocos cobrem as 6 posições:

| Bloco (nome sugerido) | Tipo | Posição no tema |
|---|---|---|
| `rosas-materia` | No artigo | Dentro da matéria (1º e 2º anúncio) |
| `rosas-fim-materia` | De display, responsivo | Fim da matéria |
| `rosas-lista` | De display, responsivo | Entre notícias (categorias, busca, capa) |
| `rosas-topo` | De display, responsivo | Topo |
| `rosas-lateral` | De display, responsivo | Barra lateral (só se ela estiver ativa) |

Cole só o número de cada bloco (o `data-ad-slot`) no campo da posição, em Personalizar → 📢 AdSense. Se quiser
começar com menos, priorize **matéria**, **fim da matéria** e **lista**: é onde fica a
leitura. A posição **Capa** só aparece depois de um bloco "Destaques Home", e a capa
atual começa com o Destaque Misto; deixe-a vazia, porque os Anúncios automáticos cobrem
a capa.

### 4. Anúncios automáticos (painel do AdSense)
**AdSense → Anúncios → Por site → portaldorosas.com.br → Editar**:

- **Anúncios automáticos:** ligados.
- **Carga de anúncios:** comece no meio da escala. Como agora há anúncios fixos nas
  matérias, não precisa do máximo.
- **Âncora:** ligada, **embaixo**. O cabeçalho do tema é fixo no topo, e uma âncora no
  topo ficaria colada nele.
- **Vinheta (tela cheia entre páginas):** ligada, com frequência baixa. Costuma render
  bem em sites de notícia; se a taxa de rejeição subir, desligue.
- **Formatos na página e Anúncios de pesquisa relacionada:** ligados.
- **Áreas excluídas:** use a pré-visualização do painel e exclua o topo da capa
  (Destaque Misto) se aparecer anúncio entre as miniaturas.

### 5. Privacidade e mensagens
**AdSense → Privacidade e mensagens → Europeias**: crie a mensagem de consentimento
padrão do Google. Ela só aparece para visitantes da Europa e do Reino Unido, e sem ela o
AdSense pode limitar os anúncios para esse público.

## Como medir (depois de 14 dias)

Compare os 14 dias seguintes com os 14 anteriores, em **AdSense → Relatórios**:

| Métrica | O que esperar |
|---|---|
| RPM da página | Subir. É o principal indicador |
| Visibilidade (Active View) | Acima de 60% nos blocos fixos |
| CTR | Entre 0,5% e 2% em notícias. Acima disso, verificar cliques acidentais |
| "Ganhos em risco" | Deve sumir (efeito do `ads.txt`) |

Também vale olhar no Search Console / PageSpeed se o CLS da capa no celular ficou abaixo
de 0,1, efeito da correção do carrossel.

## Próxima etapa (com os dados da conta)

Com o conector do AdSense autenticado, dá para ajustar com números:
- o RPM por dispositivo, para decidir se o 2º anúncio na matéria vale no celular e no desktop;
- o desempenho de cada bloco fixo, para tirar o que não rende;
- as páginas mais lidas e o melhor parágrafo para o 1º anúncio;
- o efeito da vinheta e da âncora na receita e nas páginas por sessão.
