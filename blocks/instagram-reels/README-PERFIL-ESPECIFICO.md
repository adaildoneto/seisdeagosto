# Instagram Reels - Como Buscar de Perfil Específico

## 📋 Funcionalidades

O bloco Instagram Reels agora suporta **duas formas de exibição**:

### 1️⃣ **Seus Próprios Posts** (Padrão)
- Deixe o campo "Perfil do Instagram" **vazio**
- Mostra os posts da conta autenticada (seu token)
- Não requer conta Business

### 2️⃣ **Posts de Outro Perfil** 
- Digite o `@usuario` ou `usuario` no campo "Perfil do Instagram"
- Mostra os posts de qualquer perfil público
- ⚠️ **REQUER conta Instagram Business**

---

## 🔑 Requisitos para Buscar de Outro Perfil

### ⚠️ IMPORTANTE: Business Discovery API

Para buscar posts de **outro perfil**, você precisa:

1. **Instagram Business Account** (não funciona com conta pessoal)
2. **Página do Facebook vinculada** ao Instagram Business
3. **Access Token com permissões adequadas**

### Como Configurar Conta Business:

1. **Converter para conta Business:**
   - Abra o app do Instagram
   - Vá em Configurações → Conta
   - Toque em "Mudar para conta profissional"
   - Escolha "Empresa" (não "Criador")

2. **Vincular Página do Facebook:**
   - No Instagram, vá em Configurações → Conta
   - Toque em "Página vinculada"
   - Conecte ou crie uma Página do Facebook

3. **Gerar Token no Facebook Developers:**
   - Acesse [Facebook Developers](https://developers.facebook.com/)
   - Crie um app tipo "Consumidor"
   - Adicione o produto **"Instagram Graph API"** (não apenas "Instagram Basic Display")
   - Em "Ferramentas" → "Explorador da API Graph":
     - Selecione seu app
     - Escolha a Página do Facebook vinculada
     - Selecione permissões: `instagram_basic`, `pages_show_list`, `pages_read_engagement`
     - Gere o Token de Acesso de Usuário

---

## 📖 Como Usar no WordPress

### Configuração no Editor:

1. Adicione o bloco **"Instagram Reels Gallery"**

2. No painel lateral → **"Configurações do Instagram"**:
   - **Access Token**: Cole o token gerado
   - **Perfil do Instagram** (novo campo):
     - **Vazio** = seus posts
     - **@usuario** ou **usuario** = posts de outro perfil

3. Configure exibição:
   - Tipo de mídia (todos/reels/imagens)
   - Número de posts
   - Colunas

---

## 🔍 Exemplos de Uso

### Exemplo 1: Seus Próprios Posts
```
Access Token: [seu_token_aqui]
Perfil do Instagram: [deixe vazio]
```
✅ Mostra seus próprios posts

### Exemplo 2: Perfil Específico
```
Access Token: [seu_token_aqui]
Perfil do Instagram: @natgeo
```
✅ Mostra posts do @natgeo

### Exemplo 3: Sem @ no nome
```
Access Token: [seu_token_aqui]
Perfil do Instagram: natgeo
```
✅ Também funciona (o @ é opcional)

---

## ⚡ Performance e Cache

- **Cache de 1 hora**: Resultados são armazenados para melhorar performance
- **Limite de API**: O Instagram limita requisições, por isso o cache é importante
- **Renovação automática**: Após 1 hora, busca novos posts automaticamente

---

## ❌ Troubleshooting

### Erro: "Unable to fetch media"

**Causa possível:** Access Token sem permissões adequadas

**Solução:**
1. Gere um novo token com permissões corretas
2. Use **Instagram Graph API**, não apenas Basic Display
3. Certifique-se de ter conta Business

### Erro: Business Discovery retorna vazio

**Causa possível:** O perfil alvo não é público ou não existe

**Solução:**
1. Verifique se o @ está correto
2. Confirme que o perfil é **público**
3. Teste com um perfil conhecido (ex: @instagram)

### Token expira em 60 dias

**Solução:**
- Tokens expiram automaticamente
- Use tokens de longa duração (long-lived tokens)
- Configure renovação automática via cron

---

## 🔄 Diferenças da API

### API Normal (`/me/media`):
- Busca posts **da conta autenticada**
- Não precisa de conta Business
- Endpoint: `https://graph.instagram.com/me/media`

### Business Discovery API (`/business_discovery`):
- Busca posts **de qualquer perfil público**
- **REQUER conta Business**
- Endpoint: `https://graph.instagram.com/{ig-user-id}?fields=business_discovery.username({username}){media{...}}`

---

## 📚 Referências

- [Instagram Graph API - Business Discovery](https://developers.facebook.com/docs/instagram-api/guides/business-discovery)
- [Tutorial Completo](https://matteus.dev/contratar/incorporar-posts-do-instagram-no-site-2024/)
- [Facebook Developers](https://developers.facebook.com/)

---

## ✅ Checklist de Implementação

- [x] Campo de username no editor
- [x] Atributo `instagramUsername` no block.json
- [x] Lógica para detectar username vazio/preenchido
- [x] Business Discovery API integrada
- [x] Cache funcionando para ambos os casos
- [x] Mensagens de erro descritivas
- [x] Documentação completa
