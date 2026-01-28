# 🚀 Instagram Reels Block - Método GraphQL

## 📋 Visão Geral

O bloco Instagram Reels foi atualizado para suportar um **novo método simplificado** baseado na API GraphQL do Instagram, inspirado no repositório [ahmedrangel/instagram-media-scraper](https://github.com/ahmedrangel/instagram-media-scraper).

### ✨ Principais Vantagens

- ✅ **Sem necessidade de Access Token** - não precisa criar app no Facebook Developers
- ✅ **Sem necessidade de Cookie** - método totalmente público
- ✅ **Simples de usar** - basta colar as URLs dos posts
- ✅ **Compatível com posts públicos** - funciona com qualquer post público do Instagram
- ✅ **Cache automático** - armazena dados por 1 hora para melhor performance

---

## 🎯 Como Usar

### Método 1: GraphQL (Recomendado) ⭐

Este é o método mais simples e não requer autenticação.

#### Passo a Passo:

1. **Acesse o Instagram** e encontre os posts/reels que deseja exibir
2. **Copie as URLs** dos posts (exemplo: `https://www.instagram.com/reel/ABC123/`)
3. **No editor do WordPress**, adicione o bloco "Instagram Reels Gallery"
4. **No painel lateral direito**, encontre "Configurações do Instagram"
5. **Cole as URLs** no campo "URLs dos Posts do Instagram" (uma por linha)
6. **Pronto!** Os posts serão exibidos no frontend

#### Exemplo de URLs aceitas:

```
https://www.instagram.com/reel/ABC123/
https://www.instagram.com/p/DEF456/
https://www.instagram.com/reels/GHI789/
```

#### Tipos de URL suportados:

- ✅ Posts regulares: `instagram.com/p/{id}`
- ✅ Reels: `instagram.com/reel/{id}` ou `instagram.com/reels/{id}`
- ✅ Com ou sem nome de usuário: `instagram.com/{username}/p/{id}`

---

### Método 2: API Oficial (Alternativo)

Este método continua funcionando para casos específicos que requerem:
- Buscar automaticamente posts de um perfil
- Acessar dados de conta Business
- Usar Business Discovery API

Para usar este método, siga o [tutorial oficial](https://matteus.dev/contratar/incorporar-posts-do-instagram-no-site-2024/).

---

## 🔧 Arquitetura Técnica

### Arquivos Principais

1. **`instagram-graphql-scraper.php`** - Implementação do scraper GraphQL
   - Função `seisdeagosto_get_instagram_id_from_url()` - Extrai ID do post da URL
   - Função `seisdeagosto_get_instagram_graphql_data()` - Busca dados via GraphQL
   - Função `seisdeagosto_get_instagram_multiple_posts()` - Processa múltiplas URLs
   - Função `seisdeagosto_clear_instagram_cache()` - Limpa cache de posts

2. **`render.php`** - Renderização do bloco
   - Prioridade 1: Usa GraphQL se URLs fornecidas
   - Prioridade 2: Fallback para API oficial se token fornecido

3. **`block.json`** - Definição do bloco
   - Atributo `instagramUrls` - Armazena as URLs dos posts

4. **`edit.js`** - Interface do editor
   - Campo textarea para inserir URLs
   - Interface visual com instruções claras

### Como funciona o GraphQL Scraper

O método GraphQL faz uma requisição POST para:
```
https://www.instagram.com/api/graphql
```

Com os seguintes parâmetros:
- `variables`: `{"shortcode": "ABC123"}`
- `doc_id`: `10015901848480474`
- `lsd`: `AVqbxe3J_YA`

E headers:
- `User-Agent`: User agent do navegador
- `X-IG-App-ID`: ID da aplicação Instagram
- `X-FB-LSD`: Token LSD do Facebook
- `X-ASBD-ID`: ID ASBD
- `Sec-Fetch-Site`: `same-origin`

### Estrutura de Dados Retornada

```php
array(
    'id' => 'ABC123',
    'type' => 'VIDEO', // ou 'IMAGE'
    'url' => 'https://...', // URL do vídeo ou imagem
    'thumbnail' => 'https://...', // Thumbnail
    'caption' => 'Legenda do post',
    'permalink' => 'https://instagram.com/p/ABC123/',
    'timestamp' => '2025-01-27T...',
    'like_count' => 1234,
    'comments_count' => 56,
    'media_product_type' => 'REELS',
    'owner' => 'username',
    'video_view_count' => 5678,
    'dimensions' => array('width' => 640, 'height' => 1137),
)
```

---

## 🎨 Personalização

### Opções de Exibição

No painel lateral do bloco, você pode configurar:

- **Título** - Título da seção (ex: "Siga-nos no Instagram")
- **Descrição** - Descrição da seção
- **Número de Posts** - Quantidade de posts a exibir (3-12)
- **Colunas** - Layout em grid (2-4 colunas)
- **Mostrar Legendas** - Exibir ou ocultar legendas dos posts
- **Tipo de Mídia** - Filtrar por tipo (Todos, Vídeos, Imagens)

---

## 💾 Sistema de Cache

### Como funciona

- Cache armazenado em **WordPress Transients**
- Duração: **1 hora (3600 segundos)**
- Chave do cache: `ig_graphql_{md5(post_id)}`

### Limpar cache manualmente

Use a função:
```php
seisdeagosto_clear_instagram_cache('https://www.instagram.com/reel/ABC123/');
```

Ou limpe todos os transients do Instagram:
```php
delete_transient('ig_graphql_*');
```

---

## 🐛 Troubleshooting

### "Não foi possível carregar os posts do Instagram"

**Possíveis causas:**

1. ✅ **URLs incorretas** - Verifique se as URLs estão corretas e públicas
2. ✅ **Posts privados** - O método só funciona com posts públicos
3. ✅ **Conta privada** - O perfil precisa ser público
4. ✅ **Rate limiting** - Instagram pode bloquear muitas requisições
5. ✅ **Problemas de rede** - Verifique conectividade do servidor

**Soluções:**

1. Verifique os logs de erro do WordPress
2. Ative `WP_DEBUG` para ver detalhes das requisições
3. Teste uma única URL primeiro
4. Limpe o cache e tente novamente
5. Use o método alternativo (API oficial) se necessário

### Debug

Ative debug no `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Os logs aparecerão em `wp-content/debug.log` com prefixo `[Instagram GraphQL]`.

---

## 🔒 Considerações de Segurança

- ✅ Todas as URLs são sanitizadas
- ✅ Cache implementado para reduzir requisições
- ✅ Validação de formato de URL
- ✅ Escape de dados na renderização
- ✅ Timeout de 15 segundos nas requisições

---

## 📊 Comparação: GraphQL vs API Oficial

| Característica | GraphQL (Novo) | API Oficial |
|----------------|----------------|-------------|
| Requer Access Token | ❌ Não | ✅ Sim |
| Configuração | ⭐ Simples | 🔧 Complexa |
| Busca automática de perfil | ❌ Não | ✅ Sim |
| Posts públicos | ✅ Sim | ✅ Sim |
| Cache | ✅ 1 hora | ✅ 1 hora |
| Manutenção | ⚠️ Pode mudar | ✅ Estável |
| Limite de requisições | ⚠️ Não documentado | ✅ Documentado |

### Quando usar cada método:

**Use GraphQL quando:**
- Quiser exibir posts específicos
- Não quiser configurar API do Facebook
- Tiver URLs dos posts que deseja exibir

**Use API Oficial quando:**
- Precisar buscar automaticamente posts de um perfil
- Quiser dados de Analytics
- Precisar de garantia de estabilidade
- Trabalhar com conta Business/Creator

---

## 🔄 Migração

### De API Oficial para GraphQL

1. Copie as URLs dos posts que já estão sendo exibidos
2. Cole as URLs no campo "URLs dos Posts do Instagram"
3. Remova o Access Token (opcional, pode manter como fallback)

### Manter ambos os métodos

É possível manter ambos configurados:
- Se houver URLs, usa GraphQL
- Se não houver URLs mas houver token, usa API oficial
- Isso oferece flexibilidade e fallback automático

---

## 📚 Recursos Adicionais

- [Repositório Original](https://github.com/ahmedrangel/instagram-media-scraper)
- [Tutorial API Oficial](https://matteus.dev/contratar/incorporar-posts-do-instagram-no-site-2024/)
- [Instagram Graph API Docs](https://developers.facebook.com/docs/instagram-api)

---

## 📝 Changelog

### v2.0 - Janeiro 2025
- ✨ Adicionado método GraphQL (sem necessidade de token)
- 🔄 Mantida compatibilidade com método anterior
- 📚 Documentação completa
- 🎨 Interface melhorada no editor

---

## 🤝 Contribuições

Baseado no excelente trabalho de [Ahmed Rangel](https://github.com/ahmedrangel) com o projeto [instagram-media-scraper](https://github.com/ahmedrangel/instagram-media-scraper).

Licença: MIT
