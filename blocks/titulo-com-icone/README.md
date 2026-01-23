# Bloco: Título com Ícone 🎨

Bloco personalizado do WordPress com seletor de ícones Font Awesome integrado.

## 🎯 Funcionalidades

- ✅ Editor visual integrado ao Gutenberg
- ✅ Seletor de ícones Font Awesome via modal
- ✅ Pesquisa em tempo real de ícones
- ✅ Preview instantâneo do ícone selecionado
- ✅ Linha animada no hover
- ✅ Personalizações completas de cores e tamanhos
- ✅ Opções de alinhamento (esquerda, centro, direita)

## 📋 Como Usar

### 1. Adicionar o Bloco

No editor Gutenberg:
1. Clique no botão **+** para adicionar um bloco
2. Procure por "Título com Ícone"
3. Clique para inserir o bloco

### 2. Escolher um Ícone

**Método 1: Via Seletor Visual**
1. No painel lateral direito, vá para a seção **Conteúdo**
2. Clique no botão **Escolher** ao lado do campo de ícone
3. Um modal será aberto com todos os ícones disponíveis
4. Use a barra de pesquisa para filtrar ícones
5. Clique no ícone desejado para selecioná-lo

**Método 2: Digitação Manual**
1. Digite diretamente a classe do ícone no campo (ex: `fa-star`, `fa-heart`)

### 3. Personalizar o Bloco

#### Painel de Conteúdo
- **Título**: Digite o texto do título
- **Mostrar Ícone**: Ativar/desativar exibição do ícone
- **Ícone Font Awesome**: Escolher o ícone via seletor ou digitação
- **Alinhamento**: Esquerda, Centro ou Direita

#### Painel de Estilo
- **Tamanho do Título**: 14px - 72px
- **Tamanho do Ícone**: 14px - 64px (se ícone ativado)
- **Cor do Ícone**: Seletor de cores
- **Espessura da Linha**: 1px - 10px
- **Cor da Linha**: Seletor de cores

## 🎨 Preview do Ícone

O bloco mostra um preview em tempo real:
- O ícone atual é exibido no painel lateral
- Tamanho e cor são aplicados automaticamente
- Nome da classe do ícone é mostrado abaixo do preview

## 🔧 Arquivos do Bloco

```
blocks/titulo-com-icone/
├── block.json          # Configuração do bloco
├── editor.js           # Editor React com Icon Picker
├── editor.css          # Estilos do editor
└── render.php          # Renderização no frontend
```

## 💻 Exemplo de Código Gerado

```html
<div class="titulo-com-icone-wrapper d-flex align-items-start py-3">
    <div class="titulo-com-icone-icon">
        <i class="fa fa-star" style="font-size: 24px; color: #fd7e14;"></i>
    </div>
    <div class="titulo-com-icone-content">
        <div class="titulo-com-icone-line-wrapper">
            <h3 class="titulo-com-icone-titulo m-0" style="font-size: 28px;">
                Seu Título Aqui
            </h3>
            <div class="titulo-com-icone-line" style="height: 3px; background-color: #fd7e14;"></div>
        </div>
    </div>
</div>
```

## 🎯 Ícones Disponíveis

O seletor oferece mais de **200 ícones** organizados por categorias:

- 🌐 Web Application (home, file, clock, download, etc.)
- 👤 User Icons (user, users, user-circle, etc.)
- ➡️ Directional (arrows, chevrons, angles, etc.)
- 📱 Social Media (facebook, twitter, instagram, etc.)
- 📝 Text Editor (font, bold, italic, align, etc.)
- 📰 News/Media (newspaper, microphone, comment, etc.)
- ☁️ Weather (sun, cloud, umbrella, tint, etc.)
- ⭐ Stars (star, star-half, etc.)
- 🛒 Shopping (cart, bag, credit-card, etc.)
- 💰 Currency (dollar, euro, pound, etc.)
- 🚗 Transport (car, plane, bus, etc.)
- 📄 File Types (pdf, word, excel, image, etc.)
- E muito mais...

## 🔍 Pesquisa de Ícones

A pesquisa funciona por:
- Nome da classe (ex: "fa-star")
- Label/descrição (ex: "Star", "Estrela")
- Pesquisa parcial (ex: "car" encontra "cart", "card", etc.)

## ⚙️ Integração AJAX

O bloco usa o sistema de Icon Picker via AJAX:
- Carrega ícones dinamicamente do servidor
- Fallback para ícones básicos se AJAX falhar
- Cache automático (carrega apenas uma vez por sessão)

## 📱 Responsividade

O bloco é totalmente responsivo:
- Modal adapta-se a telas pequenas
- Grid de ícones ajusta automaticamente
- Funciona em desktop, tablet e mobile

## 🎨 Animação

A linha decorativa tem animação no hover:
- Começa invisível (transform: scaleX(0))
- Expande ao passar o mouse
- Transição suave de 0.35s

## 🛠️ Desenvolvimento

Para modificar o bloco:

1. **Editor Visual**: Edite `editor.js`
2. **Estilos do Editor**: Edite `editor.css`
3. **Renderização Frontend**: Edite `render.php`
4. **Configurações**: Edite `block.json`

## 📚 Dependências

- WordPress 5.0+
- Font Awesome 4.7.0
- jQuery
- React (via wp.element)

## 🐛 Troubleshooting

**Ícones não carregam?**
- Verifique se o Font Awesome está carregado
- Confirme que o arquivo `inc/icon-picker.php` está incluído
- Verifique o console do navegador por erros

**Modal não abre?**
- Limpe o cache do navegador
- Verifique se jQuery está carregado
- Confirme que o AJAX está funcionando

**Preview não atualiza?**
- Certifique-se de que a classe do ícone está correta
- Verifique se começa com "fa-"
- Use apenas classes válidas do Font Awesome 4.7
