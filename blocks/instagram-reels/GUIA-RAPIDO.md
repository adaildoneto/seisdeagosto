# 🎬 Guia Rápido - Instagram Reels Block

## 🚀 Configuração em 30 Segundos

### 1️⃣ Adicionar o Bloco
No editor WordPress:
- Clique no botão **`+`** (Adicionar bloco)
- Digite: **`instagram`**
- Selecione: **"Instagram Reels Gallery"**

Ou use o atalho:
```
/instagram
```

---

### 2️⃣ Configurar o Username

**No painel lateral direito** → **Configurações Gerais**:

```
📝 Título: Siga-nos no Instagram
📄 Descrição: Confira nossos últimos reels
👤 Nome de usuário do Instagram: seisdeagosto
🔢 Número de Reels: 4
```

**⚠️ IMPORTANTE**: 
- Digite apenas o username, **SEM** o @
- Exemplo correto: `seisdeagosto`
- Exemplo errado: `@seisdeagosto`

---

### 3️⃣ Personalizar Layout (Opcional)

**Painel** → **Layout**:

```
📊 Colunas: 4
💬 Exibir legendas: ✅ Sim
🎬 Abrir em modal: ✅ Sim
```

#### Colunas Responsivas:
- **Desktop**: 4 colunas
- **Tablet**: 3 colunas
- **Mobile**: 2 colunas

---

### 4️⃣ Publicar & Testar

1. Clique em **"Publicar"** ou **"Atualizar"**
2. Abra a página no frontend
3. Aguarde o loading spinner
4. Verifique se os reels carregaram
5. Teste o modal de vídeo

---

## 🎯 Exemplos de Uso

### Exemplo 1: Instagram Oficial
```
Username: instagram
Reels: 6
Colunas: 3
Modal: Sim
```

### Exemplo 2: Blog de Viagens
```
Título: Nossos Destinos
Descrição: Explore o mundo conosco
Username: natgeotravel
Reels: 8
Colunas: 4
```

### Exemplo 3: Loja de Roupas
```
Título: Novidades da Semana
Username: zara
Reels: 4
Colunas: 2
Legendas: Não
```

---

## 🔧 Modo Manual (Perfis Privados)

Se o perfil for **privado** ou você quiser **escolher reels específicos**:

### Painel → Reels Manuais (Opcional)

1. Clique em **"+ Adicionar Reel"**

2. Preencha os dados:
   ```
   📷 URL da Thumbnail: https://scontent.cdninstagram.com/...
   🎥 URL do Vídeo: https://scontent.cdninstagram.com/...
   💬 Legenda: Descrição do reel
   🔗 Link do Instagram: https://instagram.com/p/ABC123/
   ```

3. Repita para cada reel

4. Para remover, clique em **"Remover"**

---

## ✅ Checklist de Verificação

Antes de publicar, confirme:

- [ ] Username está correto (sem @)
- [ ] Perfil do Instagram é **público**
- [ ] Número de reels é razoável (4-8 recomendado)
- [ ] Testou o modal de vídeo
- [ ] Verificou responsividade no mobile
- [ ] Não há erros no console do navegador

---

## 🐛 Problemas Comuns & Soluções

### ❌ "Não foi possível carregar os reels"

**Possíveis causas:**
1. Perfil privado → Use modo manual
2. Username errado → Verifique no Instagram
3. Sem vídeos → Perfil não tem reels
4. Rate limit → Aguarde alguns minutos

**Como verificar se o perfil é público:**
```
1. Abra: https://instagram.com/{seu_username}
2. Se pedir login = PRIVADO ❌
3. Se mostrar posts = PÚBLICO ✅
```

---

### ❌ Vídeos não tocam no modal

**Solução:**
- Clique em **"Ver no Instagram"** para abrir no app
- URLs de vídeo do Instagram expiram rapidamente
- Use modo manual com URLs de CDN estáveis

---

### ❌ Layout quebrado no mobile

**Verifique:**
```css
/* Seu tema pode estar sobrescrevendo */
.ig-reels-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
}
```

---

## 🎨 Personalização Rápida

### Alterar cores do botão Instagram

```css
/* Em: Aparência → Personalizar → CSS Adicional */
.ig-reels-follow-btn {
    background: linear-gradient(45deg, #405DE6, #5851DB, #833AB4) !important;
}

.ig-reels-follow-btn:hover {
    transform: scale(1.05) !important;
}
```

### Mudar tamanho da grade

```css
/* Forçar 3 colunas no desktop */
.ig-reels-grid[data-columns="4"] {
    grid-template-columns: repeat(3, 1fr) !important;
}
```

### Ocultar botão "Siga-nos"

```css
.ig-reels-follow-btn {
    display: none !important;
}
```

---

## 📱 Shortcode (Futuro)

> 🚧 Em desenvolvimento - atualmente use apenas via Gutenberg

```php
[instagram_reels user="seisdeagosto" limit="6" columns="3"]
```

---

## 🆘 Suporte

### Testado com:
- ✅ WordPress 5.8+
- ✅ Gutenberg Editor
- ✅ Chrome, Firefox, Safari, Edge
- ✅ Mobile iOS/Android

### Não funciona com:
- ❌ Classic Editor (use Gutenberg)
- ❌ Perfis privados (use modo manual)
- ❌ JavaScript desabilitado

---

## 📊 Configurações Recomendadas

### Landing Page
```
Reels: 4
Colunas: 4
Modal: Sim
Legendas: Sim
```

### Sidebar
```
Reels: 3
Colunas: 1
Modal: Sim
Legendas: Não
```

### Footer
```
Reels: 4
Colunas: 4
Modal: Sim
Legendas: Não
```

### Página Institucional
```
Reels: 6-8
Colunas: 3
Modal: Sim
Legendas: Sim
```

---

## 🎓 Dicas de Boas Práticas

1. **Perfis sugeridos**: Use contas com reels ativos e públicos
2. **Quantidade**: 4-8 reels é o ideal para performance
3. **Colunas**: 3-4 para desktop, nunca mais que 4
4. **Legendas**: Ative se os reels forem informativos
5. **Modal**: Sempre deixe ativo para melhor UX
6. **Atualização**: Reels carregam dinamicamente, sem cache

---

## ✨ Recursos Extras

### Loading Automático
- ⏱️ Spinner animado enquanto carrega
- 📊 Feedback visual claro
- ❌ Mensagens de erro amigáveis

### Modal Profissional
- 🎬 Reprodução automática
- ⌨️ Fechar com `ESC`
- 🔗 Link direto para Instagram
- 📱 Responsivo e touch-friendly

### Performance
- 🚀 Carregamento assíncrono
- 💾 Cache do navegador
- 🖼️ Lazy loading de imagens
- ⚡ Otimizado para Core Web Vitals

---

**Precisa de ajuda?** Verifique o [README.md](./README.md) completo ou [CHANGELOG-V2.md](./CHANGELOG-V2.md) para detalhes técnicos.

---

**Versão**: 2.0.0  
**Atualizado**: Janeiro 2026  
**Status**: ✅ Pronto para uso
