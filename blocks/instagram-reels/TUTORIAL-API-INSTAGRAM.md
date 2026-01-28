# Tutorial: Como Usar a API do Instagram para o Bloco Instagram Reels

## 📋 Visão Geral

Este tutorial explica como obter um **Access Token** da API do Instagram Graph para buscar automaticamente seus reels no bloco Instagram Reels Gallery.

---

## 🎯 Pré-requisitos

1. **Conta no Facebook** (mesma vinculada ao Instagram)
2. **Conta Instagram Business ou Creator** (não funciona com conta pessoal)
3. **Página do Facebook** vinculada à conta do Instagram

---

## 📝 Passo 1: Criar um App no Facebook Developers

### 1.1. Acesse o Facebook Developers
- Vá para: https://developers.facebook.com/
- Faça login com sua conta do Facebook

### 1.2. Criar Novo App
1. Clique em **"Meus Apps"** no menu superior
2. Clique em **"Criar App"**
3. Escolha o tipo: **"Empresa"** ou **"Consumidor"**
4. Preencha:
   - **Nome do App**: Ex: "Meu Site Instagram"
   - **E-mail de Contato**: Seu email
   - **Conta Comercial**: (opcional)
5. Clique em **"Criar App"**

### 1.3. Adicionar o Produto Instagram Basic Display
1. No painel do app, role até encontrar **"Instagram Basic Display"**
2. Clique em **"Configurar"**

---

## 📝 Passo 2: Configurar Instagram Basic Display

### 2.1. Configurações Básicas
1. Na página de configuração, clique em **"Configurações Básicas"**
2. Role até a seção **"Instagram App ID"** e copie o ID (você vai precisar)
3. Clique em **"Criar Nova Configuração de App"**

### 2.2. URLs de Redirecionamento OAuth
Adicione as seguintes URLs:
```
https://seusite.com.br/
https://seusite.com.br/wp-admin/
```

### 2.3. Cancelar URL de Autorização
```
https://seusite.com.br/
```

### 2.4. Política de Privacidade e Termos de Serviço
- Adicione URLs para suas páginas de Política de Privacidade e Termos
- Se não tiver, crie páginas básicas no WordPress

### 2.5. Salvar Alterações
Clique em **"Salvar Alterações"** no final da página

---

## 📝 Passo 3: Adicionar Conta de Teste do Instagram

### 3.1. Adicionar Usuário de Teste
1. Vá para **"Funções"** → **"Funções do Instagram"**
2. Clique em **"Adicionar Usuários de Teste do Instagram"**
3. Digite seu nome de usuário do Instagram
4. Clique em **"Enviar"**

### 3.2. Aceitar o Convite
1. Vá para seu perfil do Instagram
2. Acesse **Configurações** → **Apps e Sites**
3. Em **"Convites de Apps de Teste"**, aceite o convite

---

## 📝 Passo 4: Gerar Access Token

### 4.1. Via Graph API Explorer (Método Rápido)

#### Opção A: Instagram Graph API (Recomendado para Business)
1. Acesse: https://developers.facebook.com/tools/explorer/
2. No canto superior direito:
   - Selecione seu App
   - Selecione **"Gerar Token de Acesso"**
3. Escolha **"Obter Token de Acesso do Usuário"**
4. Marque as permissões:
   - `instagram_basic`
   - `instagram_content_publish`
   - `pages_show_list`
   - `pages_read_engagement`
5. Clique em **"Gerar Token de Acesso"**
6. **COPIE O TOKEN** (ele aparece no campo "Token de Acesso")

#### Opção B: Instagram Basic Display API
1. Construa a URL de autorização manualmente:
```
https://api.instagram.com/oauth/authorize?client_id={APP_ID}&redirect_uri={REDIRECT_URI}&scope=user_profile,user_media&response_type=code
```

Substitua:
- `{APP_ID}`: Seu Instagram App ID
- `{REDIRECT_URI}`: Uma das URLs que você configurou (URL encoded)

2. Abra essa URL no navegador
3. Autorize o app
4. Você será redirecionado com um código na URL: `?code=XXXXXXX`
5. Use esse código para trocar por um Access Token (via API)

### 4.2. Trocar Código por Access Token (se usar Opção B)

Use o seguinte request cURL ou Postman:

```bash
curl -X POST \
  https://api.instagram.com/oauth/access_token \
  -F client_id={APP_ID} \
  -F client_secret={APP_SECRET} \
  -F grant_type=authorization_code \
  -F redirect_uri={REDIRECT_URI} \
  -F code={CODE}
```

Resposta:
```json
{
  "access_token": "IGQVJ...",
  "user_id": 123456789
}
```

### 4.3. Converter para Long-Lived Token (60 dias)

```bash
curl -X GET \
  "https://graph.instagram.com/access_token?grant_type=ig_exchange_token&client_secret={APP_SECRET}&access_token={SHORT_TOKEN}"
```

Resposta:
```json
{
  "access_token": "IGQVJ... (novo token)",
  "token_type": "bearer",
  "expires_in": 5184000
}
```

---

## 📝 Passo 5: Usar o Token no Bloco WordPress

### 5.1. Adicionar o Bloco
1. No editor do WordPress, adicione o bloco **"Instagram Reels Gallery"**
2. No painel lateral, vá para **"API do Instagram"**

### 5.2. Configurar Token
1. **Access Token**: Cole o token que você obteve
2. **Nome de usuário do Instagram**: Digite seu @ (sem o @)
3. **Número de Reels**: Escolha quantos reels exibir (1-12)

### 5.3. Publicar
- Salve ou publique a página
- Os reels serão buscados automaticamente da API

---

## 🔄 Renovar Access Token

### Tokens de Curta Duração (1 hora)
- Precisam ser renovados frequentemente
- Use a conversão para Long-Lived Token

### Long-Lived Tokens (60 dias)
- Válidos por 60 dias
- Podem ser renovados antes de expirar

### Renovar Long-Lived Token:
```bash
curl -X GET \
  "https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token={CURRENT_TOKEN}"
```

### Automatizar Renovação no WordPress (Opcional)

Adicione ao `functions.php`:
```php
// Renovar token automaticamente a cada 50 dias
function renovar_instagram_token() {
    $current_token = get_option('instagram_access_token');
    
    if (!$current_token) return;
    
    $url = add_query_arg([
        'grant_type' => 'ig_refresh_token',
        'access_token' => $current_token
    ], 'https://graph.instagram.com/refresh_access_token');
    
    $response = wp_remote_get($url);
    
    if (!is_wp_error($response)) {
        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($body['access_token'])) {
            update_option('instagram_access_token', $body['access_token']);
        }
    }
}

// Agendar renovação
if (!wp_next_scheduled('renovar_instagram_token_event')) {
    wp_schedule_event(time(), 'monthly', 'renovar_instagram_token_event');
}
add_action('renovar_instagram_token_event', 'renovar_instagram_token');
```

---

## 🔍 Testar a API

### Verificar se o Token Funciona:
```bash
curl -X GET \
  "https://graph.instagram.com/me/media?fields=id,caption,media_type,media_url,thumbnail_url,permalink&access_token={YOUR_TOKEN}"
```

Resposta esperada:
```json
{
  "data": [
    {
      "id": "123456",
      "caption": "Meu reel!",
      "media_type": "VIDEO",
      "media_url": "https://...",
      "thumbnail_url": "https://...",
      "permalink": "https://instagram.com/p/..."
    }
  ]
}
```

---

## ❌ Problemas Comuns

### 1. "Invalid OAuth access token"
- **Solução**: O token expirou. Gere um novo token.

### 2. "Instagram user not found"
- **Solução**: Verifique se a conta é Business/Creator e está vinculada à página do Facebook.

### 3. Nenhum reel aparece
- **Solução**: 
  - Verifique se você tem reels publicados
  - O bloco filtra apenas mídia do tipo VIDEO
  - Verifique se o token tem as permissões corretas

### 4. "Application does not have permission"
- **Solução**: Adicione as permissões necessárias no Graph API Explorer.

---

## 🎯 Alternativa: Modo Manual (Sem API)

Se não conseguir configurar a API, use o modo manual:

1. No bloco, vá para **"Reels Manuais"**
2. Clique em **"+ Adicionar Reel"**
3. Para cada reel:
   - **URL da Thumbnail**: Link da imagem de capa
   - **URL do Vídeo**: Link direto do vídeo
   - **Legenda**: Texto do reel
   - **Link do Instagram**: URL do post no Instagram

### Como Obter URLs Manualmente:
1. Abra o reel no Instagram Web (instagram.com)
2. Clique com botão direito na thumbnail → "Copiar endereço da imagem"
3. Para o vídeo, use ferramentas como:
   - SnapInsta (snapinsta.app)
   - DownloadGram (downloadgram.com)

---

## 📚 Recursos Adicionais

- [Instagram Basic Display API Docs](https://developers.facebook.com/docs/instagram-basic-display-api)
- [Instagram Graph API Docs](https://developers.facebook.com/docs/instagram-api)
- [Facebook App Dashboard](https://developers.facebook.com/apps/)

---

## ✅ Checklist Final

- [ ] App criado no Facebook Developers
- [ ] Instagram Basic Display configurado
- [ ] Conta de teste adicionada e aceita
- [ ] Access Token gerado
- [ ] Token convertido para Long-Lived (60 dias)
- [ ] Token adicionado no bloco WordPress
- [ ] Reels aparecendo corretamente no site

---

**🎉 Pronto! Seus reels do Instagram agora aparecem automaticamente no seu site!**
