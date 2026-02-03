# Bloco Resultado Mega Sena

Bloco WordPress Gutenberg para exibir o resultado do último sorteio da Mega Sena e outras loterias da Caixa Econômica Federal.

## Características

- ✅ **API Oficial da Caixa**: Consulta dados diretamente da API oficial
- ✅ **Cache Inteligente**: Resultados armazenados em cache por 30 minutos
- ✅ **Design Minimalista**: Interface limpa usando Bootstrap e FontAwesome
- ✅ **Responsivo**: Adaptável a todos os dispositivos
- ✅ **Personalizável**: Opções de cores, exibição e configurações
- ✅ **Multi-modalidade**: Suporta todas as loterias da Caixa

## Loterias Suportadas

- Mega Sena
- Lotofácil
- Quina
- Lotomania
- Timemania
- Dupla Sena
- Federal
- Loteca
- Dia de Sorte
- Super Sete
- +Milionária

## Uso do Bloco

### 1. Adicionar o Bloco

No editor Gutenberg, procure por "Resultado Mega Sena" e adicione à página.

### 2. Configurações Disponíveis

**Painel de Configurações:**
- **Título**: Personalize o título do bloco
- **Mostrar número do concurso**: Exibe/oculta o número do concurso
- **Mostrar data do sorteio**: Exibe/oculta a data do sorteio
- **Mostrar prêmio**: Exibe/oculta o valor do prêmio
- **Mostrar próximo concurso**: Exibe/oculta informações do próximo sorteio
- **Atualização automática**: Habilita/desabilita cache automático

**Painel de Cores:**
- **Cor de fundo**: Personalize a cor de fundo do bloco
- **Cor do texto**: Personalize a cor dos textos
- **Cor das bolas**: Personalize a cor das bolas dos números

## Página de Todas as Loterias

O tema inclui um template especial para exibir todas as loterias:

### Como Criar a Página

1. No WordPress, vá em **Páginas** > **Adicionar Nova**
2. Digite o título (ex: "Resultados das Loterias")
3. No painel direito, em **Atributos da Página**, selecione o template **"Resultados das Loterias"**
4. Publique a página

A página exibirá automaticamente:
- Todos os resultados das loterias da Caixa
- Números sorteados de cada modalidade
- Premiação completa por faixa
- Informação de acumulado (quando aplicável)
- Design minimalista e responsivo

## API da Caixa

### Endpoint

```
https://servicebus2.caixa.gov.br/portaldeloterias/api/{modalidade}/{concurso?}
```

### Exemplos de Uso

```php
// Último resultado da Mega Sena
$resultado = seisdeagosto_get_loteria_result('megasena');

// Resultado específico do concurso 2500
$resultado = seisdeagosto_get_loteria_result('megasena', 2500);

// Todas as loterias
$todas = seisdeagosto_get_all_loterias();
```

### Estrutura de Resposta

```json
{
  "numero": 2654,
  "dataApuracao": "2026-02-01T00:00:00",
  "listaDezenas": ["05", "12", "23", "34", "45", "56"],
  "valorEstimadoProximoConcurso": 50000000.00,
  "listaRateioPremio": [
    {
      "faixa": 1,
      "descricaoFaixa": "Sena",
      "numeroDeGanhadores": 2,
      "valorPremio": 25000000.00
    }
  ]
}
```

## Funções Úteis

### seisdeagosto_get_loteria_result()

Busca o resultado de uma loteria específica.

```php
$resultado = seisdeagosto_get_loteria_result( 'megasena', $concurso_opcional );
```

### seisdeagosto_get_all_loterias()

Busca todos os resultados de todas as loterias.

```php
$todas = seisdeagosto_get_all_loterias();
```

### seisdeagosto_clear_loteria_cache()

Limpa o cache de uma modalidade específica ou de todas.

```php
// Limpa cache da Mega Sena
seisdeagosto_clear_loteria_cache( 'megasena' );

// Limpa todos os caches
seisdeagosto_clear_loteria_cache();
```

### seisdeagosto_format_currency()

Formata valores monetários.

```php
$valor_formatado = seisdeagosto_format_currency( 1000000.50 );
// Retorna: "R$ 1.000.000,50"
```

### seisdeagosto_format_date()

Formata datas no padrão brasileiro.

```php
$data_formatada = seisdeagosto_format_date( '2026-02-01T00:00:00' );
// Retorna: "01/02/2026"
```

## Cache

O sistema implementa cache automático usando WordPress Transients:

- **Duração**: 30 minutos
- **Chave**: `loteria_{modalidade}` ou `loteria_{modalidade}_{concurso}`
- **Limpeza**: Automática após expiração ou manual via função

## Personalização

### Modificar Cores das Bolas

Edite o arquivo [style.css](style.css):

```css
.mega-sena-ball {
    background-color: #209869; /* Verde da Mega Sena */
}
```

### Adicionar Nova Modalidade

1. Adicione à lista em [loteria-api.php](loteria-api.php):

```php
$modalidades = array(
    // ... outras modalidades
    'nova-loteria' => 'Nome da Loteria',
);
```

2. Adicione cor em [page-loterias.php](../../../page-loterias.php):

```css
.loteria-ball.novaloteria { background-color: #CODIGO_COR; }
```

## Requisitos

- WordPress 5.8+
- PHP 7.4+
- Bootstrap (já incluído no tema)
- FontAwesome (já incluído no tema)

## Suporte e Documentação

- [Documentação da API da Caixa](https://servicebus2.caixa.gov.br/portaldeloterias/api/home/)
- [WordPress Block API](https://developer.wordpress.org/block-editor/)

## Changelog

### v1.0.0 (2026-02-02)
- Lançamento inicial
- Bloco Mega Sena
- Template de página para todas as loterias
- API handler com cache
- Design minimalista com Bootstrap e FontAwesome
