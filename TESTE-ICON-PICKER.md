# Guia de Teste e Diagnóstico - Icon Picker e Título com Ícone

## ⚠️ MUDANÇAS IMPORTANTES REALIZADAS

### 1. Criado `edit.js` para Registrar o Bloco
O bloco precisa de um script de registro completo. Criei [blocks/titulo-com-icone/edit.js](blocks/titulo-com-icone/edit.js) que:
- Registra o bloco usando `wp.blocks.registerBlockType`
- Define a interface de edição com RichText
- Adiciona todos os controles (cores, tamanhos, alinhamento)
- Mostra preview visual no editor

### 2. Separados Dois Scripts
- **edit.js**: Registra e define a interface do bloco
- **editor.js**: Adiciona o Icon Picker como extensão usando `wp.hooks`

### 3. Atualizado inc/blocks.php
Modificado para carregar AMBOS os scripts na ordem correta:
1. Carrega `edit.js` primeiro (registra o bloco)
2. Carrega `editor.js` depois (adiciona icon picker)

### 4. Adicionado Script de Debug
Criado [debug-titulo-icone.php](debug-titulo-icone.php) que mostra:
- ✅ Status de todas as dependências (wp.blocks, wp.hooks, etc)
- ✅ Se o bloco está registrado
- ✅ Se Font Awesome está carregado
- ✅ Se AJAX está funcionando
- ✅ Avisos visuais no admin
- ✅ Debug no frontend também

## 🔧 Como Testar Agora

### PASSO 1: Limpar Cache Completamente
```bash
# No navegador:
1. Pressione Ctrl + Shift + Delete
2. Marque "Cache" e "Cookies"
3. Clique em "Limpar"
4. OU use Ctrl + Shift + R (hard reload)
```

### PASSO 2: Verificar no Editor do WordPress
1. Vá para **Painel → Posts/Páginas → Adicionar Novo**
2. Clique no "+" para adicionar bloco
3. Procure por "Título com Ícone" na categoria "Seis de Agosto"
4. **DEVE APARECER AGORA** ✅

### PASSO 3: Verificar Avisos de Debug
No topo da página do editor, deve aparecer uma caixa verde com:
```
✅ Bloco "Título com Ícone" está registrado
- Nome: seisdeagosto/titulo-com-icone
- Título: Título com Ícone
- Render callback: ✅ Definido
- Função: u_correio68_render_titulo_com_icone
- Função existe: ✅
```

Se aparecer caixa VERMELHA, há problema no registro.

### PASSO 4: Testar o Icon Picker
1. Adicione o bloco "Título com Ícone"
2. No painel direito, abra "Ícone Font Awesome"
3. Certifique-se que "Mostrar Ícone" está ATIVADO
4. Clique no botão **"Escolher"**
5. Deve abrir modal com 200+ ícones ✅

### PASSO 5: Verificar Console (F12)
Abra o DevTools e vá na aba Console. Procure por:

**Mensagens esperadas:**
```
=== DEBUG TÍTULO COM ÍCONE ===
1. WordPress loaded: ✅
2. wp.blocks: ✅
3. wp.element: ✅
4. wp.hooks: ✅
5. wp.compose: ✅
6. jQuery loaded: ✅ v3.x
7. Font Awesome loaded: ✅
8. seideagostoBlocks: ✅
9. Block registered: ✅
10. Editor script tag: ✅
11. ✅ AJAX working! Icons: 200+
[Título com Ícone] Block registered successfully
=== FIM DEBUG ===
```

### PASSO 6: Verificar Frontend
1. Publique ou atualize a página com o bloco
2. Visualize no frontend
3. Abra o Console (F12) - deve aparecer:
```
=== DEBUG FRONTEND - TÍTULO COM ÍCONE ===
1. Font Awesome loaded: ✅
2. Titulo-com-icone blocks found: 1
   Block 1:
     - Icon class: fa fa-star
     - Icon color: rgb(...)
     - Title: CTA
     - Font size: 28px
     - Line color: rgb(...)
3. Font Awesome stylesheet: ✅
=== FIM DEBUG FRONTEND ===
```

## 🐛 Diagnóstico de Problemas

### Problema: Bloco não aparece no inserter
**Console mostra:**
```javascript
[Título com Ícone] Block already registered, skipping
```

**Solução:** O bloco está sendo registrado duas vezes. Verifique se há outro local registrando o mesmo bloco.

---

### Problema: Modal do Icon Picker não abre
**Verifique no console:**
```javascript
console.log(window.seideagostoBlocks);
```

Se retornar `undefined`, o wp_localize_script não está funcionando.

**Solução:** Certifique-se que [inc/blocks.php](inc/blocks.php#L185-L192) tem o wp_localize_script.

---

### Problema: Frontend não mostra o bloco
**Verifique:**
1. Console frontend mostra "Titulo-com-icone blocks found: 0"?
2. Render callback definido?

**Solução:** 
- Verifique se [blocks/titulo-com-icone/render.php](blocks/titulo-com-icone/render.php) existe
- Verifique se função `u_correio68_render_titulo_com_icone` está definida
- Veja aviso admin no topo da página do editor

---

### Problema: Font Awesome não carrega
**Console mostra:**
```
3. Font Awesome loaded: ❌
```

**Solução:**
1. Verifique se arquivo existe em `assets/vendor/font-awesome-4.7/css/font-awesome.min.css`
2. Veja no source da página se há `<link>` para font-awesome
3. Tente desabilitar cache do navegador

---

## 📁 Arquivos Modificados/Criados

### Criados:
1. ✅ [blocks/titulo-com-icone/edit.js](blocks/titulo-com-icone/edit.js) - Registro do bloco
2. ✅ [debug-titulo-icone.php](debug-titulo-icone.php) - Script de diagnóstico
3. ✅ [test-icon-picker-ajax.html](test-icon-picker-ajax.html) - Teste AJAX standalone

### Modificados:
1. ✅ [inc/blocks.php](inc/blocks.php#L174-L213) - Carrega edit.js + editor.js
2. ✅ [functions.php](functions.php#L1749-L1752) - Inclui debug script
3. ✅ [blocks/titulo-com-icone/editor.js](blocks/titulo-com-icone/editor.js#L55-L61) - Compatibilidade optional chaining

## 🚀 Próximos Passos

1. ✅ **Recarregue a página com Ctrl + Shift + R**
2. ✅ **Verifique os avisos de debug no topo do editor**
3. ✅ **Abra o console (F12) e veja as mensagens**
4. ✅ **Adicione o bloco e teste o icon picker**
5. ✅ **Publique e verifique no frontend**

## 🗑️ Remover Debug (após resolver)

Quando tudo funcionar, remova estas linhas de [functions.php](functions.php#L1749-L1752):

```php
// Debug - Título com Ícone (remova após resolver)
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    require_once "debug-titulo-icone.php";
}
```

E delete o arquivo `debug-titulo-icone.php`.

---

## 💡 Script de Diagnóstico Rápido

Cole no Console (F12) quando estiver no editor:

```javascript
(function() {
    console.log('=== DIAGNÓSTICO RÁPIDO ===');
    console.log('1. WP:', typeof wp !== 'undefined' ? '✅' : '❌');
    console.log('2. jQuery:', typeof jQuery !== 'undefined' ? '✅' : '❌');
    console.log('3. seideagostoBlocks:', window.seideagostoBlocks ? '✅' : '❌');
    
    const block = wp?.blocks?.getBlockType('seisdeagosto/titulo-com-icone');
    console.log('4. Bloco registrado:', block ? '✅' : '❌');
    
    if (window.seideagostoBlocks && typeof jQuery !== 'undefined') {
        jQuery.ajax({
            url: window.seideagostoBlocks.ajaxUrl,
            type: 'POST',
            data: { action: 'get_fontawesome_icons' },
            success: (r) => console.log('5. AJAX:', r.success ? '✅ ' + r.data.icons.length + ' ícones' : '❌'),
            error: () => console.log('5. AJAX: ❌')
        });
    }
})();
```
