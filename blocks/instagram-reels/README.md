#  Bloco Instagram Reels - Guia de Uso

##  ATUALIZAÇÃO IMPORTANTE

O Instagram **removeu o acesso público à API oEmbed** (agora requer OAuth 2.0). Por isso, o bloco funciona de forma simplificada:

-  **Links funcionam**: Clique no reel abre no Instagram
-  **Thumbnails limitadas**: Usa URL padrão do Instagram (pode não carregar sempre)
- ? **Fallback**: Se a imagem não carregar, mostra ícone do Instagram

##  Como Funciona

Este bloco exibe reels do Instagram como uma galeria clicável. Você cola o link do reel e ele cria um cartão visual com link direto para o Instagram.

##  Como Usar

### 1. Adicionar o Bloco
- No editor do WordPress, clique em '+' e procure por 'Instagram Reels'
- Adicione o bloco à página

### 2. Copiar Link do Reel
1. Abra o Instagram e encontre o reel desejado
2. Clique nos **3 pontos ()** no canto superior direito
3. Selecione **'Copiar link'**
4. Você terá algo como: https://www.instagram.com/reel/ABC123/

### 3. Adicionar no Bloco
1. No painel direito do editor, vá em **'Links dos Reels'**
2. Clique em **'+ Adicionar Link de Reel'**
3. Cole o link copiado
4. Repita para adicionar mais reels

### 4. Personalizar (Opcional)
- **Título e Descrição**: Adicione em 'Configurações Gerais'
- **Colunas**: Escolha entre 2, 3 ou 4 colunas

##  Como Funciona Tecnicamente

1. Extrai o **shortcode** da URL do reel (ex: ABC123)
2. Tenta carregar thumbnail via URL padrão do Instagram
3. Se falhar, mostra placeholder com ícone do Instagram
4. O clique sempre leva ao reel original no Instagram

##  Solução de Problemas

### As imagens não aparecem
**É normal!** O Instagram bloqueou o acesso às thumbnails sem autenticação. 

**Soluções:**
1. **Aceite o placeholder**: O link funciona mesmo sem imagem
2. **Screenshot manual**: Tire print do reel e adicione via Media Library
3. **Use outro bloco**: Se precisa imagens perfeitas, considere outros plugins

### Como adicionar thumbnail manual
Se quiser garantir que a imagem apareça:
1. Abra o reel no Instagram
2. Tire um screenshot
3. Faça upload no WordPress (Media Library)
4. *[Futura feature]* Adicione campo de thumbnail customizada

##  Alternativas

Se você precisa de thumbnails funcionando 100%:

1. **Instagram Official Embed**: Use o código de embed do Instagram:
   - Vá no reel  Compartilhar  Incorporar
   - Cole o código em um bloco HTML customizado

2. **Plugin Smash Balloon**: Plugin pago com acesso oficial à API

3. **Screenshots**: Adicione imagens manualmente

##  Arquivos do Bloco

- **block.json**: Configuração do bloco
- **render.php**: Renderização (extração de shortcode)
- **edit.js**: Interface do editor  
- **style.css**: Estilos CSS
