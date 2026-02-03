# Troubleshooting - Bloco Mega Sena

## O bloco não aparece o resultado?

### 1. Verificar se o bloco foi registrado

No WordPress admin, vá em:
- **Aparência > Editor** 
- Clique no **"+"** para adicionar bloco
- Procure por "Mega Sena" ou "Resultado"
- Se não aparecer, o bloco não foi registrado

### 2. Verificar arquivos necessários

Certifique-se que os seguintes arquivos existem na pasta `blocks/mega-sena/`:

```
blocks/mega-sena/
├── block.json          ✓ Configuração do bloco
├── edit.js             ✓ Interface do editor
├── render.php          ✓ Renderização no frontend
├── loteria-api.php     ✓ Integração com API
├── style.css           ✓ Estilos
├── frontend.js         ✓ JavaScript frontend
└── shortcode.php       ✓ Shortcodes (opcional)
```

### 3. Verificar se inc/blocks.php carrega o bloco

Abra `inc/blocks.php` e procure por:

```php
$metadata_blocks = array(
    'mega-sena' => 'seisdeagosto_render_mega_sena_block',
);
```

E mais abaixo:

```php
foreach ( $metadata_blocks as $slug => $callback ) {
    $render_file = $blocks_dir . '/' . $slug . '/render.php';
    if ( file_exists( $render_file ) ) {
        require_once( $render_file );
    }
}
```

### 4. Ativar Debug do WordPress

Adicione ao `wp-config.php`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

Depois verifique o arquivo `wp-content/debug.log` para erros.

### 5. Testar a API manualmente

Acesse no navegador:
```
https://servicebus2.caixa.gov.br/portaldeloterias/api/megasena
```

Se retornar JSON com dados, a API está funcionando.

### 6. Verificar permissões de arquivo

Certifique-se que o servidor pode ler os arquivos:
- **Linux/Mac**: `chmod 644` nos arquivos PHP
- **Windows**: Verificar permissões de leitura

### 7. Limpar cache

```php
// Cole no functions.php temporariamente:
delete_transient( 'loteria_megasena' );
```

Ou use um plugin de cache e limpe tudo.

### 8. Teste com debug inline

Adicione no início de `render.php` (logo após a função):

```php
function seisdeagosto_render_mega_sena_block( $attributes ) {
    error_log( 'MEGA SENA BLOCK: Iniciando renderização' );
    
    // ... resto do código
```

Depois verifique o `debug.log`.

### 9. Verificar se a função existe

Cole no `functions.php` e acesse qualquer página:

```php
add_action( 'init', function() {
    if ( function_exists( 'seisdeagosto_render_mega_sena_block' ) ) {
        error_log( 'Função de render existe!' );
    } else {
        error_log( 'ERRO: Função de render NÃO existe!' );
    }
    
    if ( function_exists( 'seisdeagosto_get_loteria_result' ) ) {
        error_log( 'Função API existe!' );
    } else {
        error_log( 'ERRO: Função API NÃO existe!' );
    }
}, 999 );
```

### 10. Teste a API diretamente

Cole no `functions.php`:

```php
add_action( 'init', function() {
    if ( isset( $_GET['test_mega_sena'] ) ) {
        require_once get_template_directory() . '/blocks/mega-sena/loteria-api.php';
        $result = seisdeagosto_get_loteria_result( 'megasena' );
        echo '<pre>';
        print_r( $result );
        echo '</pre>';
        die();
    }
});
```

Depois acesse: `https://seusite.local/?test_mega_sena`

## Erros Comuns

### "Erro ao conectar com a API"
- Servidor sem acesso externo (firewall)
- Problema com `wp_remote_get()`
- Certificado SSL inválido

**Solução**: Adicione ao `wp-config.php`:
```php
define( 'WP_HTTP_BLOCK_EXTERNAL', false );
```

### "API retornou status 404"
- Nome da modalidade errado
- Concurso inexistente

**Solução**: Verifique o nome correto em:
- megasena
- lotofacil
- quina
- lotomania
- timemania
- duplasena
- diadesorte
- supersete
- federal
- loteca
- maismilionaria

### Bloco aparece vazio
- Erro no JavaScript (console do navegador)
- CSS não carregado
- Bootstrap não carregado

**Solução**:
1. Abra o Console (F12)
2. Veja se há erros JavaScript
3. Verifique se FontAwesome carregou
4. Verifique se Bootstrap carregou

### Datas aparecendo erradas
- Formato de data da API mudou
- Timezone incorreto

**Solução**: Use a função `seisdeagosto_format_date()` que tem fallbacks.

## Checklist Rápido

- [ ] Arquivos na pasta `blocks/mega-sena/` existem
- [ ] `inc/blocks.php` registra o bloco 'mega-sena'
- [ ] API da Caixa está acessível (teste no navegador)
- [ ] Debug ativado (`WP_DEBUG` = true)
- [ ] Cache limpo (transients e cache de página)
- [ ] Permissões de arquivo corretas
- [ ] Bootstrap enfileirado (para accordion/dropdown)
- [ ] FontAwesome enfileirado (para ícones)
- [ ] Tema ativo é o `seisdeagosto`

## Suporte

Se após todos os testes o bloco não funcionar, verifique:

1. **Versão do WordPress**: Requer 6.0+
2. **Versão do PHP**: Requer 7.4+
3. **Tema**: Deve ser o tema `seisdeagosto` ou child theme dele
4. **Conflitos**: Desative outros plugins temporariamente

## Logs úteis

```php
// Ver todos os blocos registrados
add_action( 'init', function() {
    $registry = WP_Block_Type_Registry::get_instance();
    $blocks = $registry->get_all_registered();
    error_log( 'Blocos registrados: ' . print_r( array_keys( $blocks ), true ) );
}, 999 );
```

Se `seisdeagosto/mega-sena` aparecer na lista, o bloco está registrado corretamente!
