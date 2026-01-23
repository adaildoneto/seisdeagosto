# Conformidade dos Blocos com a Documentação WordPress

## ✅ Melhorias Implementadas

### 1. **Uso de block.json (Método Recomendado)**
Conforme a [documentação oficial do WordPress](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/), o método recomendado para registrar blocos é usando `block.json`.

**Antes:**
```php
register_block_type( 'seideagosto/news-grid', array(
    'category' => 'seisdeagosto',
    'editor_script' => 'seideagosto-blocks',
    'render_callback' => 'u_correio68_render_news_grid',
    'attributes' => array(...)
) );
```

**Depois:**
```php
register_block_type( 
    get_template_directory() . '/blocks/news-grid/block.json',
    array(
        'render_callback' => 'u_correio68_render_news_grid',
    )
);
```

### 2. **Metadados Completos em block.json**

Todos os blocos agora incluem:

- ✅ `$schema` - Validação automática do JSON
- ✅ `apiVersion: 3` - Versão mais recente da API de blocos
- ✅ `version` - Versionamento do bloco
- ✅ `title` - Título traduzível
- ✅ `category` - Categoria para organização
- ✅ `icon` - Ícone Dashicon para identificação visual
- ✅ `description` - Descrição do bloco
- ✅ `keywords` - Palavras-chave para busca
- ✅ `textdomain` - Domínio de tradução
- ✅ `attributes` - Schema completo de atributos com tipos e defaults
- ✅ `example` - Preview no editor
- ✅ `supports` - Recursos modernos (align, spacing, color, typography)

### 3. **Validação de Tipos de Atributos**

Todos os atributos seguem as especificações da [documentação de atributos](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-attributes/):

```json
{
  "categoryIds": {
    "type": "array",
    "default": [],
    "items": {
      "type": "number"
    }
  },
  "columns": {
    "type": "number",
    "default": 3,
    "enum": [1, 2, 3, 4, 6]
  },
  "layoutType": {
    "type": "string",
    "default": "default",
    "enum": ["default", "grid", "list"]
  }
}
```

### 4. **Suporte a Recursos Modernos**

Cada bloco agora declara explicitamente os recursos suportados:

```json
{
  "supports": {
    "html": false,
    "align": ["wide", "full"],
    "spacing": {
      "margin": true,
      "padding": true,
      "blockGap": true
    },
    "color": {
      "text": true,
      "background": true,
      "link": true
    },
    "typography": {
      "fontSize": true,
      "lineHeight": true
    }
  }
}
```

### 5. **Preview no Editor (example)**

Todos os blocos incluem configuração de exemplo para visualização:

```json
{
  "example": {
    "attributes": {
      "numberOfPosts": 6,
      "columns": 3
    }
  }
}
```

## 📁 Blocos Migrados para block.json

1. **seideagosto/destaques-home** - `/blocks/destaques-home/block.json`
2. **seideagosto/news-grid** - `/blocks/news-grid/block.json`
3. **seideagosto/category-highlight** - `/blocks/category-highlight/block.json`
4. **seideagosto/destaque-misto** - `/blocks/destaque-misto/block.json`
5. **seideagosto/top-most-read** - `/blocks/top-most-read/block.json`

## 🎯 Benefícios da Migração

### Performance
- ✅ Metadados são lidos uma vez e cacheados
- ✅ Redução de código PHP duplicado
- ✅ Validação automática de atributos

### Manutenibilidade
- ✅ Configuração centralizada em JSON
- ✅ Fácil versionamento
- ✅ Menos propensão a erros

### Recursos do Editor
- ✅ Preview automático de blocos
- ✅ Melhor suporte a alinhamentos
- ✅ Espaçamento e cores integrados
- ✅ Tipografia responsiva

### Desenvolvimento
- ✅ Validação de schema automática
- ✅ Autocomplete em editores compatíveis
- ✅ Documentação inline

## 📋 Checklist de Conformidade

### ✅ Registro de Blocos
- [x] Usa `block.json` ao invés de arrays PHP
- [x] Define `apiVersion: 3`
- [x] Inclui `$schema` para validação

### ✅ Metadados
- [x] `title` descritivo e traduzível
- [x] `description` clara
- [x] `icon` apropriado (Dashicons)
- [x] `category` personalizada registrada
- [x] `keywords` para busca

### ✅ Atributos
- [x] Todos com `type` definido
- [x] `default` values apropriados
- [x] Validação com `enum` quando aplicável
- [x] Arrays com `items` schema
- [x] Nomes em camelCase

### ✅ Features Modernas
- [x] `supports` declarado
- [x] `example` para preview
- [x] `textdomain` para i18n
- [x] `version` para versionamento

### ✅ Best Practices
- [x] Namespace consistente (`seideagosto/`)
- [x] Atributos com valores padrão
- [x] Separação de concerns (JSON vs PHP)
- [x] Documentação inline

## 🔄 Próximos Passos

Para total conformidade com WordPress, considere:

1. **Internacionalização (i18n)**
   - Adicionar traduções usando `textdomain`
   - Usar funções `__()`, `_e()`, `_n()`

2. **Block Variations**
   - Criar variações de blocos para casos de uso comuns
   - Exemplo: "Grid 2 colunas", "Grid 3 colunas", etc.

3. **Block Patterns**
   - Criar padrões pré-configurados
   - Combinar blocos em layouts completos

4. **Block Transforms**
   - Permitir conversão entre blocos relacionados
   - Ex: News Grid ↔ Category Highlight

5. **Lazy Loading**
   - Implementar `editorScript: "file:./index.js"`
   - Carregar JS apenas quando necessário

## 📚 Referências da Documentação WordPress

- [Block Registration](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/)
- [Block Metadata (block.json)](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/)
- [Block Attributes](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-attributes/)
- [Block Supports](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/)
- [Best Practices](https://developer.wordpress.org/block-editor/getting-started/fundamentals/block-json/)

## ✅ Status: CONFORME

Todos os blocos principais agora seguem as melhores práticas recomendadas pela documentação oficial do WordPress.
