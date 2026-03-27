# 🌳 Seis de Agosto

<div align="center">

**Tema WordPress personalizado para portais de notícias**

[![WordPress](https://img.shields.io/badge/WordPress-5.0+-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)](https://php.net/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.0-purple.svg)](https://getbootstrap.com/)

</div>

---

## 📖 Sobre o Nome

**Seis de Agosto** é uma homenagem à **Revolução Acreana**, movimento histórico que culminou em 6 de agosto de 1902, quando o Acre conquistou sua autonomia. Este tema foi desenvolvido com orgulho por um desenvolvedor acreano, celebrando a história e a cultura do Acre.

> 🌟 *"A memória de um povo se perpetua em cada linha de código."*

---

## 🎯 Visão Geral

Tema WordPress moderno e robusto, especialmente desenvolvido para portais de notícias e sites jornalísticos:

- 🧩 **Blocos Gutenberg dinâmicos** focados em notícias e destaques
- 🔧 **Construtor de páginas** de Arquivo/Busca com conteúdo em blocos (seleção via Customizer)
- 🎨 **Estilos baseados em variáveis** de tema e painel de tipografia unificada
- 🔄 **Deduplicação inteligente** de posts: blocos evitam repetir matérias já exibidas na mesma página (PG_Helper)
- 📱 **Design responsivo** com Bootstrap 5
- ⚡ **Performance otimizada** com cache e lazy loading

---

## 🧩 Blocos Gutenberg

Todos os blocos do tema aparecem no inseridor dentro da seção **Seis de Agosto**.

### 📰 Blocos de Conteúdo

- **🎯 Destaques Home** (1 Grande + 2 Pequenos)
  - Hero section com um post grande e dois menores
  - Aceita filtro por categoria
  - Imagens otimizadas com gradiente overlay

- **📊 Grid de Notícias**
  - Grade responsiva de cards com imagem e título
  - Opções: categoria, quantidade, offset, colunas (2-6) e tipografia
  - Suporta **paginação opcional** e respeita deduplicação entre blocos
  - Adapta-se automaticamente em páginas de arquivo/busca

- **🏷️ Destaque Categoria** (1 Grande + 3 Lista)
  - Primeiro post com imagem destacada + lista textual
  - Tipografia configurável
  - Ideal para seções de categoria específica

- **🎨 Destaque Misto** (2 Grandes + Lista + 1 Coluna)
  - Layout híbrido: dois destaques principais e lista de itens
  - Respeita categoria selecionada
  - Perfeito para páginas iniciais

### 👥 Blocos de Colunistas

- **✍️ Grid de Colunistas / Item de Colunista**
  - Layout em grade de 4 colunas
  - Cada item exibe: nome, título e último post da categoria definida
  - Suporte a imagem de perfil e biografia

### 📈 Blocos de Engajamento

- **🔥 Top Mais Lidas** (Top N)
  - Lista ordenada por views (custom meta)
  - Fallback automático por número de comentários
  - Período configurável (hoje, semana, mês, ano)

### 🌦️ Blocos Utilitários

- **☁️ Clima** (Open-Meteo API)
  - Exibe clima atual por cidade ou coordenadas
  - Animações de chuva e vento aprimoradas
  - Cache de curta duração para performance
  - Previsão em tempo real

- **💱 Monitor de Câmbio**
  - Taxas BRL vs USD/BOB/PEN com spread
  - Mostra compra e venda
  - Carousel com dots (setas ocultas)
  - Atualização automática via cron
  - Fallback entre múltiplos provedores de API

- **🖼️ Galeria em Slider (Slick)**
  - Slider de imagens configurável
  - Suporte a autoplay, fade, vertical e RTL

- **📦 Área de Widgets (Sidebar)**
  - Renderiza uma área de widgets selecionada no editor

### 📦 Blocos Metadata (theme/blocks)

- **destaque-grande** - Post em destaque tamanho grande
- **destaque-pequeno** - Post em destaque compacto
- **lista-noticias** - Lista simples de notícias
- **titulo-com-icone** - Título com ícone e linha animada

---

## 🎨 Customizer (inc/customizer.php)

### Opções de Personalização

- **🎨 Cores**
  - Cor primária do tema
  - Cor dos badges
  - Fundo de destaques
  - Fundo colunistas
  - Cores de header e footer

- **📝 Tipografia**
  - Tamanhos e peso dos títulos
  - Classes: `.TituloGrande`, `.TituloGrande2`, `.title-post`
  - Controle fino de font-size e font-weight

- **📐 Layout**
  - Altura da imagem destacada
  - Espaçamento entre cards
  - **HR/Separator minimalista** (linha fina 1px)
  
- **📖 Toolbar de Leitura** (single post)
  - Botões A-/A/A+ para ajuste de fonte
  - Seguem variáveis de cor do tema
  - Acessibilidade aprimorada

- **📂 Modelos de Arquivo**
  - Escolha páginas (Page) para renderizar conteúdo de:
    - Categorias
    - Tags
    - Busca
  - Permite usar blocos Gutenberg em páginas de arquivo

- **📰 Posts e Badges**
  - Chamada nativa via excerpt (fallback automático)
  - Badge opcional com categoria e cor primária do tema

- **👁️ Live Preview**
  - Atualiza estilos em tempo real
  - Powered by `js/customizer-preview.js`

---

## 🔍 Arquivos/Busca com Blocos

### Como Configurar

1. **Criar Página** com os blocos desejados
   - Exemplo: Grid de Notícias com categoria "Todas"
   
2. **No Customizer**, selecione essa Página para:
   - 📁 Categorias
   - 🏷️ Tags
   - 🔎 Busca

3. **Resultado**: As páginas de arquivo/busca renderizam o conteúdo dessa Página
   - O Grid de Notícias segue o contexto automaticamente
   - Se paginação habilitada, os links usam a paginação do arquivo/busca

---

## 📱 Áreas de Widgets

O tema oferece **13 áreas de widgets** estrategicamente posicionadas:

| # | Nome | ID |
|---|------|-----|
| 1 | 📋 Navbar Lateral | `navbarlateral` |
| 2 | 📰 Banner abaixo do post | `banner_post` |
| 3 | 📊 Banner Vertical | `banneraleac-vertical` |
| 4 | 🌡️ Temperatura | `temperatura` |
| 5 | ✍️ Banner Colunistas | `banner_colunistas` |
| 6 | 🏛️ Banner ALEAC | `bannneraleac` |
| 7 | 💬 Grupo WhatsApp | `whatsappcorreio68` |
| 8 | 👥 Colunistas 68 | `colunistas` |
| 9 | 🐂 Na Rota do Boi | `narotadoboi` |
| 10 | ➡️ Right Sidebar | `right-sidebar` |
| 11 | 📢 Banner Footer | `bannerfooter` |
| 12 | 🎯 Banners do Cabral | `cabralize` |
| 13 | ⬅️ Left Sidebar | `left-sidebar` |

---

## 🖼️ Tamanhos de Imagem

```php
'destaque' => 300x180 (crop)
'destatquegrande' => 730x410 (crop)
```

Otimizados para performance e qualidade visual em diferentes dispositivos.

---

## 💻 Notas de Desenvolvimento

### Tecnologias e Padrões

- **Text Domain**: `u_correio68`
- **Base**: Bootstrap e Starter Theme (ST2/UnderStrap)
- **Namespaces dos blocos**: 
  - `seisdeagosto/*` (principal)
  - `u-correio68/*` (metadata blocks)
  - `correio68/*` (compatibilidade de renderização)
  - `seideagosto/*` (alias legado temporário para migração segura)

### Recursos Avançados

- ✅ Deduplicação automática de posts via `PG_Helper`
- ✅ Sistema de cache inteligente
- ✅ Lazy loading de imagens
- ✅ API REST customizada para clima e câmbio
- ✅ Cron jobs para atualização de dados externos
- ✅ Suporte a meta fields (ACF)
- ✅ Customizer com live preview
- ✅ Internacionalização (i18n) ready

### Performance

- 🚀 Transients para cache de API
- 🚀 Minificação de assets
- 🚀 Lazy loading nativo
- 🚀 Otimização de queries com `post__not_in`

---

## 👨‍💻 Autor

<div align="center">

**Adaildo Neto**

Desenvolvedor Acreano 🌳

📧 [adaildo.neto@gmail.com](mailto:adaildo.neto@gmail.com)

*Desenvolvido com ❤️ no Acre, Brasil*

</div>

---

## 📜 Licença

Este tema é proprietário e foi desenvolvido especificamente para uso em projetos jornalísticos.

---

<div align="center">

**🌳 Seis de Agosto** - *Honrando a Revolução Acreana desde 1902*

</div>
