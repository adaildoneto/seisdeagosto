# 🎬 Como Adicionar Reels Manualmente

## Quando Usar o Modo Manual?

Use quando:
- ❌ O perfil do Instagram é **privado**
- ❌ O carregamento automático não funciona
- ✅ Você quer **escolher reels específicos** para exibir
- ✅ Precisa de **controle total** sobre o conteúdo

---

## 📋 Passo a Passo (5 minutos)

### 1. Encontre o Reel no Instagram

Abra o Instagram e encontre o reel que deseja adicionar.

**Exemplo de URL:**
```
https://www.instagram.com/reel/ABC123xyz/
ou
https://www.instagram.com/p/ABC123xyz/
```

### 2. Copie o Link do Reel

No Instagram (desktop ou mobile):
- Clique nos 3 pontinhos (⋯) do reel
- Selecione "Copiar link"

### 3. Extraia as URLs Necessárias

Você precisa de 2 URLs:
- **Thumbnail** (imagem de preview)
- **Vídeo** (arquivo MP4)

#### Método A: Via Insta Downloader (Mais Fácil)

1. Acesse: https://inflact.com/downloader/instagram/video/
2. Cole o link do reel
3. Clique em "Download"
4. **Thumbnail URL**: Clique com botão direito na imagem → "Copiar endereço da imagem"
5. **Video URL**: Clique com botão direito em "Download" → "Copiar endereço do link"

#### Método B: Via Inspect Element (Mais Técnico)

1. Abra o reel no Instagram
2. Clique com botão direito → "Inspecionar" (F12)
3. Vá para a aba "Network" (Rede)
4. Recarregue a página (F5)
5. Procure por arquivos `.mp4` (vídeo) e `.jpg` (thumbnail)
6. Clique com botão direito → "Copy" → "Copy link address"

### 4. Adicione no WordPress

1. **Abra o bloco** no editor WordPress
2. **Painel lateral direito** → **Reels Manuais (Opcional)**
3. Clique em **"+ Adicionar Reel"**
4. Preencha os campos:

```
┌─────────────────────────────────────────────┐
│ URL da Thumbnail                            │
│ https://scontent.cdninstagram.com/...jpg    │ ← Cole aqui
├─────────────────────────────────────────────┤
│ URL do Vídeo                                │
│ https://scontent.cdninstagram.com/...mp4    │ ← Cole aqui
├─────────────────────────────────────────────┤
│ Legenda                                     │
│ Descrição opcional do reel                  │
├─────────────────────────────────────────────┤
│ Link do Instagram                           │
│ https://instagram.com/p/ABC123/             │ ← Link original
└─────────────────────────────────────────────┘
```

5. Clique em **"Atualizar"**
6. Repita para mais reels

---

## 💡 Exemplo Prático

### Reel Real do @leorosas1365

**Link do Instagram:**
```
https://www.instagram.com/p/DEoSHx1O123/
```

**URLs Extraídas:**
```
Thumbnail:
https://scontent-gru2-1.cdninstagram.com/v/t51.29350-15/470123456_123456789_1234567890123456789_n.jpg

Vídeo:
https://scontent-gru2-1.cdninstagram.com/v/t50.2886-16/470987654_987654321_9876543210987654321_n.mp4

Legenda:
"Confira as novidades do dia!"

Link:
https://instagram.com/p/DEoSHx1O123/
```

---

## 🎨 Configuração Completa no Editor

### Exemplo de Bloco Configurado

```
📋 Configurações Gerais
├─ Título: Nossos Reels
├─ Descrição: Acompanhe nosso conteúdo no Instagram
└─ Nome de usuário: leorosas1365

🎬 Reels Manuais (Opcional)
├─ Reel 1
│  ├─ Thumbnail: https://...jpg
│  ├─ Vídeo: https://...mp4
│  ├─ Legenda: "Notícia importante..."
│  └─ Link: https://instagram.com/p/ABC123/
│
├─ Reel 2
│  ├─ Thumbnail: https://...jpg
│  ├─ Vídeo: https://...mp4
│  ├─ Legenda: "Cobertura ao vivo..."
│  └─ Link: https://instagram.com/p/DEF456/
│
└─ [+ Adicionar Reel]

📊 Layout
├─ Colunas: 4
├─ Exibir legendas: ✓
└─ Abrir em modal: ✓
```

---

## ⚙️ Ferramentas Úteis

### Downloaders Online (Grátis)

1. **Inflact** (Recomendado)
   - https://inflact.com/downloader/instagram/video/
   - ✅ Fácil de usar
   - ✅ URLs permanentes
   - ✅ Sem login

2. **SaveFrom.net**
   - https://pt.savefrom.net/1-instagram-video-downloader-120/
   - ✅ Rápido
   - ✅ Suporta reels e IGTV

3. **SnapInsta**
   - https://snapinsta.app/
   - ✅ Interface simples
   - ✅ Download direto

### Extensões de Navegador

1. **IG Helper** (Chrome/Firefox)
   - Download com 1 clique
   - Extrai URLs automaticamente

2. **Video Downloader Plus** (Chrome)
   - Detecta vídeos automaticamente
   - Mostra URLs de download

---

## 🐛 Problemas Comuns

### ❌ "URL do vídeo não funciona"

**Causa:** URLs do Instagram expiram após algumas horas/dias

**Solução:**
1. Use um downloader para salvar o vídeo localmente
2. Faça upload para sua biblioteca de mídia do WordPress
3. Use a URL do WordPress ao invés da do Instagram

**Exemplo:**
```
Ao invés de:
https://scontent.cdninstagram.com/...mp4

Use:
https://seusite.com.br/wp-content/uploads/2026/01/reel-video.mp4
```

### ❌ "Imagem não carrega"

**Solução:**
1. Download da thumbnail
2. Upload para WordPress (Mídia → Adicionar novo)
3. Copie a URL da biblioteca de mídia

### ❌ "Muitos reels para adicionar manualmente"

**Solução:**
- Adicione apenas os 4-6 melhores reels
- Atualize semanalmente com novos
- Considere criar múltiplos blocos por categoria

---

## 💾 Como Fazer Upload para WordPress

### 1. Baixe o Vídeo/Imagem
```
Downloader → Download → Salvar no PC
```

### 2. Upload no WordPress
```
WordPress → Mídia → Adicionar novo
→ Selecione arquivo → Upload
```

### 3. Copie a URL
```
Biblioteca de Mídia → Clique na mídia
→ Copiar "URL do arquivo"
```

### 4. Cole no Bloco
```
URL do Vídeo: https://seusite.com.br/wp-content/uploads/reel.mp4
```

**Vantagens:**
- ✅ URL nunca expira
- ✅ Carrega mais rápido (seu servidor)
- ✅ Controle total sobre o arquivo
- ✅ Funciona offline do Instagram

---

## 📊 Quantos Reels Adicionar?

### Recomendações por Uso

**Landing Page:**
- 4-6 reels
- Os mais importantes/recentes
- Atualizar mensalmente

**Página Institucional:**
- 6-8 reels
- Mix de conteúdos variados
- Atualizar quinzenalmente

**Sidebar:**
- 2-3 reels
- Apenas destaques
- Atualizar semanalmente

**Footer:**
- 4 reels
- Evergreen content
- Atualizar raramente

---

## ✅ Checklist de Qualidade

Antes de publicar, verifique:

- [ ] Todas as URLs estão funcionando
- [ ] Thumbnails carregam corretamente
- [ ] Vídeos tocam no modal
- [ ] Legendas estão corretas
- [ ] Links do Instagram funcionam
- [ ] Responsividade no mobile está OK
- [ ] Número de reels é adequado (4-8)

---

## 🚀 Automatização Futura (Opcional)

Se você adiciona reels manualmente frequentemente, considere:

### Script de Importação (PHP)

```php
// Cria array de reels para copiar/colar
$reels = [
    [
        'thumbnail_url' => 'https://...',
        'video_url' => 'https://...',
        'caption' => 'Legenda',
        'permalink' => 'https://instagram.com/p/...'
    ],
    // ... mais reels
];

// Copie e cole este array no campo "Reels Data"
```

### Planilha de Organização

| Reel ID | Thumbnail URL | Video URL | Legenda | Status |
|---------|--------------|-----------|---------|--------|
| 001 | https://... | https://... | Texto | ✅ OK |
| 002 | https://... | https://... | Texto | ⏳ Pendente |

---

## 📞 Dúvidas Frequentes

**P: Posso misturar reels automáticos e manuais?**
R: Sim! Se tiver reels manuais, eles serão exibidos. Se não, tenta carregar automaticamente.

**P: Quantos reels posso adicionar?**
R: Até 12, mas recomendamos 4-8 para melhor performance.

**P: Preciso renovar as URLs?**
R: URLs do CDN do Instagram expiram. Recomendamos fazer upload no WordPress.

**P: Posso usar vídeos de outros perfis?**
R: Tecnicamente sim, mas respeite direitos autorais.

---

**Tempo estimado:** 2-3 minutos por reel  
**Dificuldade:** ⭐⭐☆☆☆ (Fácil)  
**Resultado:** 100% de controle sobre o conteúdo exibido
