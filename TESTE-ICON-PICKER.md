# Guia de Teste e Diagnóstico - Icon Picker no Bloco Título com Ícone

## Mudanças Realizadas

### 1. Adicionadas Dependências ao Editor.js
Adicionei `wp-hooks` e `wp-compose` às dependências do script `titulo-com-icone-editor` em [inc/blocks.php](inc/blocks.php#L180).

### 2. Adicionado wp_localize_script para AJAX
Adicionei configuração AJAX específica para o script do titulo-com-icone com:
- `ajaxUrl`: URL do admin-ajax.php
- `nonce`: Token de segurança

## Como Testar

### Passo 1: Limpar Cache
```bash
# No navegador, pressione:
Ctrl + Shift + R  (Windows/Linux)
Cmd + Shift + R   (Mac)
```

### Passo 2: Testar AJAX Manualmente
1. Abra o arquivo: `test-icon-picker-ajax.html` no navegador
2. URL seria algo como: `http://6barra8.local/wp-content/themes/seisdeagosto/test-icon-picker-ajax.html`
3. Clique no botão "Testar AJAX"
4. Deve mostrar: "✅ Sucesso! Total de ícones: 200+" 

### Passo 3: Testar no Editor do WordPress
1. Acesse o admin do WordPress
2. Edite uma página/post
3. Adicione o bloco "Título com Ícone"
4. No painel lateral direito (Inspector Controls), procure a opção "Ícone Font Awesome"
5. Ative "Mostrar Ícone" se não estiver ativo
6. Clique no botão "Escolher"
7. Deve abrir um modal com os ícones

### Passo 4: Verificar Console do Navegador
Abra o DevTools (F12) e vá na aba Console. Procure por:

**Erros esperados (se houver problema):**
- `seideagostoBlocks is not defined`
- `Cannot read property 'ajaxUrl' of undefined`
- `404 Not Found` em requisição AJAX

**Mensagens esperadas (se estiver funcionando):**
- `Blocos Seis de Agosto registrados: ["seisdeagosto/titulo-com-icone", ...]`
- Nenhum erro relacionado ao titulo-com-icone

## Diagnóstico de Problemas

### Problema: Modal não abre
**Possíveis causas:**
1. Script não carregado
2. Dependências faltando
3. Erro JavaScript bloqueando execução

**Solução:**
Abra o Console (F12) e execute:
```javascript
// Verificar se scripts estão carregados
console.log('wp:', typeof wp);
console.log('wp.hooks:', typeof wp.hooks);
console.log('wp.compose:', typeof wp.compose);
console.log('seideagostoBlocks:', window.seideagostoBlocks);
```

### Problema: Modal abre mas ícones não carregam
**Possíveis causas:**
1. AJAX retornando erro
2. URL incorreta
3. Nonce inválido

**Solução:**
Abra a aba Network (Rede) no DevTools e procure por requisição `admin-ajax.php`.
Clique nela e verifique:
- **Status Code**: Deve ser 200
- **Response**: Deve conter `{"success":true,"data":{"icons":[...]}}`

### Problema: "Escolher" não aparece
**Possível causa:**
O atributo `mostrarIcone` está `false`

**Solução:**
1. No editor, selecione o bloco
2. No painel direito, ative "Mostrar Ícone"
3. O botão "Escolher" deve aparecer

## Arquivos Modificados

1. **[inc/blocks.php](inc/blocks.php#L174-L195)**
   - Adicionadas dependências: `wp-hooks`, `wp-compose`
   - Adicionado `wp_localize_script` para `titulo-com-icone-editor`

2. **[blocks/titulo-com-icone/editor.js](blocks/titulo-com-icone/editor.js)**
   - Usa `wp.hooks.addFilter` para estender o bloco
   - Carrega ícones via AJAX do endpoint `get_fontawesome_icons`
   - Modal totalmente funcional com busca

3. **[inc/icon-picker.php](inc/icon-picker.php)**
   - Endpoint AJAX: `wp_ajax_get_fontawesome_icons`
   - Retorna 200+ ícones Font Awesome 4.7

## Verificação Rápida via Console

Cole este código no Console do navegador (F12) quando estiver no editor:

```javascript
// Teste completo
(function() {
    console.log('=== DIAGNÓSTICO ICON PICKER ===');
    
    // 1. Verificar WordPress API
    console.log('1. WordPress API:', typeof wp !== 'undefined' ? '✅' : '❌');
    
    // 2. Verificar dependências
    console.log('2. wp.hooks:', typeof wp.hooks !== 'undefined' ? '✅' : '❌');
    console.log('3. wp.compose:', typeof wp.compose !== 'undefined' ? '✅' : '❌');
    console.log('4. jQuery:', typeof jQuery !== 'undefined' ? '✅' : '❌');
    
    // 3. Verificar variável AJAX
    console.log('5. seideagostoBlocks:', window.seideagostoBlocks ? '✅' : '❌');
    if (window.seideagostoBlocks) {
        console.log('   - ajaxUrl:', window.seideagostoBlocks.ajaxUrl);
        console.log('   - nonce:', window.seideagostoBlocks.nonce);
    }
    
    // 4. Verificar bloco registrado
    const block = wp.blocks.getBlockType('seisdeagosto/titulo-com-icone');
    console.log('6. Bloco registrado:', block ? '✅' : '❌');
    if (block) {
        console.log('   - Title:', block.title);
        console.log('   - Attributes:', Object.keys(block.attributes));
    }
    
    // 5. Testar AJAX
    if (window.seideagostoBlocks && typeof jQuery !== 'undefined') {
        console.log('7. Testando AJAX...');
        jQuery.ajax({
            url: window.seideagostoBlocks.ajaxUrl,
            type: 'POST',
            data: { action: 'get_fontawesome_icons' },
            success: function(r) {
                if (r.success && r.data && r.data.icons) {
                    console.log('   ✅ AJAX funcionando! Total ícones:', r.data.icons.length);
                } else {
                    console.log('   ⚠️ AJAX resposta inesperada:', r);
                }
            },
            error: function(xhr, status, error) {
                console.log('   ❌ AJAX erro:', status, error);
            }
        });
    }
    
    console.log('=== FIM DIAGNÓSTICO ===');
})();
```

## Próximos Passos

1. ✅ Recarregue a página do editor com **Ctrl + Shift + R**
2. ✅ Teste o botão "Escolher" no bloco Título com Ícone
3. ✅ Se não funcionar, execute o script de diagnóstico acima
4. ✅ Envie o resultado do console para análise
