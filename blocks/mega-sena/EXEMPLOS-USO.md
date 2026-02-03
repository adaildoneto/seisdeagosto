# 📖 Exemplos de Uso - Loterias Caixa

## 1. Usando o Bloco Gutenberg

### Exemplo Básico
1. No editor, adicione o bloco "Resultado Mega Sena"
2. Pronto! O resultado será exibido automaticamente

### Personalização no Editor
```
Configurações disponíveis:
✓ Título: "Confira o Resultado"
✓ Mostrar concurso: Sim
✓ Mostrar data: Sim  
✓ Mostrar prêmio: Sim
✓ Mostrar próximo: Sim

Cores:
✓ Fundo: #ffffff
✓ Texto: #333333
✓ Bolas: #209869
```

## 2. Usando Shortcodes

### 2.1 Shortcode Simples

```php
// Mega Sena com configurações padrão
[loteria modalidade="megasena"]
```

### 2.2 Shortcode com Opções

```php
// Lotofácil sem prêmio, bolas pequenas
[loteria modalidade="lotofacil" mostrar_premio="false" tamanho="pequeno"]

// Quina com cor customizada
[loteria modalidade="quina" cor_bola="#260085"]

// Concurso específico
[loteria modalidade="megasena" concurso="2500"]
```

### 2.3 Lista de Todas as Loterias

```php
// Grid com 3 colunas
[loterias_lista colunas="3"]

// Grid com 2 colunas
[loterias_lista colunas="2"]

// Apenas algumas loterias
[loterias_lista modalidades="megasena,lotofacil,quina"]
```

### 2.4 Tamanhos Disponíveis

```php
[loteria modalidade="megasena" tamanho="mini"]      // 35px
[loteria modalidade="megasena" tamanho="pequeno"]   // 45px
[loteria modalidade="megasena" tamanho="normal"]    // 55px
[loteria modalidade="megasena" tamanho="grande"]    // 70px
```

## 3. Usando PHP no Template

### 3.1 Buscar Resultado

```php
<?php
// Último resultado da Mega Sena
$resultado = seisdeagosto_get_loteria_result('megasena');

// Concurso específico
$resultado = seisdeagosto_get_loteria_result('megasena', 2654);

// Verificar se deu erro
if (isset($resultado['error'])) {
    echo 'Erro: ' . $resultado['error'];
} else {
    echo 'Concurso: ' . $resultado['numero'];
}
?>
```

### 3.2 Exibir Números

```php
<?php
$resultado = seisdeagosto_get_loteria_result('megasena');
$numeros = $resultado['listaDezenas'];

foreach ($numeros as $numero) {
    echo '<span class="numero">' . str_pad($numero, 2, '0', STR_PAD_LEFT) . '</span>';
}
?>
```

### 3.3 Exibir Prêmio

```php
<?php
$resultado = seisdeagosto_get_loteria_result('megasena');

foreach ($resultado['listaRateioPremio'] as $rateio) {
    if ($rateio['faixa'] == 1) { // Faixa principal
        echo 'Prêmio: ' . seisdeagosto_format_currency($rateio['valorPremio']);
        echo '<br>Ganhadores: ' . $rateio['numeroDeGanhadores'];
    }
}
?>
```

### 3.4 Todas as Loterias

```php
<?php
$todas = seisdeagosto_get_all_loterias();

foreach ($todas as $loteria) {
    echo '<h3>' . $loteria['nome'] . '</h3>';
    echo 'Concurso: ' . $loteria['dados']['numero'];
    
    // Números
    foreach ($loteria['dados']['listaDezenas'] as $numero) {
        echo $numero . ' ';
    }
}
?>
```

## 4. Widget na Sidebar

### Adicionar Widget

1. Vá em **Aparência** > **Widgets**
2. Encontre o widget **"Resultado Loteria"**
3. Arraste para a sidebar desejada
4. Configure:
   - Título: "Mega Sena"
   - Modalidade: Mega Sena
5. Salve

## 5. Exemplos Avançados

### 5.1 Custom Loop com Resultados

```php
<?php
// Template customizado
function exibir_resultado_personalizado() {
    $resultado = seisdeagosto_get_loteria_result('megasena');
    ?>
    <div class="meu-resultado-custom">
        <h2>Mega Sena <?php echo $resultado['numero']; ?></h2>
        
        <div class="numeros-custom">
            <?php foreach ($resultado['listaDezenas'] as $numero) : ?>
                <div class="bola-custom">
                    <?php echo str_pad($numero, 2, '0', STR_PAD_LEFT); ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (!empty($resultado['valorAcumuladoProximoConcurso'])) : ?>
            <div class="acumulado">
                <strong>Acumulou!</strong>
                Próximo concurso: 
                <?php echo seisdeagosto_format_currency($resultado['valorEstimadoProximoConcurso']); ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}
?>
```

### 5.2 Comparar Jogo

```php
<?php
function verificar_meu_jogo($meus_numeros) {
    $resultado = seisdeagosto_get_loteria_result('megasena');
    $sorteados = $resultado['listaDezenas'];
    
    $acertos = array_intersect($meus_numeros, $sorteados);
    $qtd_acertos = count($acertos);
    
    echo "Você acertou {$qtd_acertos} números!<br>";
    
    if ($qtd_acertos >= 4) {
        echo "Parabéns! Você é um ganhador!";
    }
    
    return $qtd_acertos;
}

// Uso
$meu_jogo = ['05', '12', '23', '34', '45', '56'];
verificar_meu_jogo($meu_jogo);
?>
```

### 5.3 Estatísticas

```php
<?php
function numeros_mais_sorteados($modalidade, $ultimos_concursos = 10) {
    $estatisticas = array();
    
    for ($i = 0; $i < $ultimos_concursos; $i++) {
        $resultado = seisdeagosto_get_loteria_result($modalidade);
        // Buscar concurso anterior
        $concurso_anterior = $resultado['numero'] - $i;
        $resultado_anterior = seisdeagosto_get_loteria_result($modalidade, $concurso_anterior);
        
        if ($resultado_anterior && !isset($resultado_anterior['error'])) {
            foreach ($resultado_anterior['listaDezenas'] as $numero) {
                if (!isset($estatisticas[$numero])) {
                    $estatisticas[$numero] = 0;
                }
                $estatisticas[$numero]++;
            }
        }
    }
    
    arsort($estatisticas);
    return array_slice($estatisticas, 0, 10);
}

// Exibir top 10
$top10 = numeros_mais_sorteados('megasena', 50);
foreach ($top10 as $numero => $vezes) {
    echo "Número {$numero}: sorteado {$vezes} vezes<br>";
}
?>
```

## 6. AJAX para Atualização Dinâmica

### JavaScript

```javascript
// Atualizar resultado sem recarregar página
jQuery(document).ready(function($) {
    $('#atualizar-resultado').click(function() {
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'atualizar_loteria',
                modalidade: 'megasena'
            },
            success: function(response) {
                $('#resultado-container').html(response);
            }
        });
    });
});
```

### PHP (functions.php ou plugin)

```php
<?php
add_action('wp_ajax_atualizar_loteria', 'ajax_atualizar_loteria');
add_action('wp_ajax_nopriv_atualizar_loteria', 'ajax_atualizar_loteria');

function ajax_atualizar_loteria() {
    $modalidade = isset($_POST['modalidade']) ? sanitize_text_field($_POST['modalidade']) : 'megasena';
    
    // Limpa cache para forçar atualização
    seisdeagosto_clear_loteria_cache($modalidade);
    
    // Busca novo resultado
    $resultado = seisdeagosto_get_loteria_result($modalidade);
    
    // Renderiza e retorna HTML
    echo seisdeagosto_loteria_shortcode(array('modalidade' => $modalidade));
    
    wp_die();
}
?>
```

## 7. Integração com WooCommerce

### Produto com Número da Sorte

```php
<?php
// Adicionar número da sorte ao produto
add_action('woocommerce_order_item_meta_end', function($item_id, $item, $order) {
    // Gera número aleatório
    $numero_sorte = str_pad(rand(1, 60), 2, '0', STR_PAD_LEFT);
    echo '<p><strong>Número da Sorte:</strong> ' . $numero_sorte . '</p>';
}, 10, 3);
?>
```

## 8. Notificações

### Email quando Houver Ganhador

```php
<?php
function verificar_e_notificar_ganhador() {
    $resultado = seisdeagosto_get_loteria_result('megasena');
    
    foreach ($resultado['listaRateioPremio'] as $rateio) {
        if ($rateio['faixa'] == 1 && $rateio['numeroDeGanhadores'] > 0) {
            // Há ganhador na Sena!
            $to = get_option('admin_email');
            $subject = 'Mega Sena teve ganhador!';
            $message = sprintf(
                'O concurso %d teve %d ganhador(es) com prêmio de %s',
                $resultado['numero'],
                $rateio['numeroDeGanhadores'],
                seisdeagosto_format_currency($rateio['valorPremio'])
            );
            
            wp_mail($to, $subject, $message);
        }
    }
}

// Agendar para verificar diariamente
if (!wp_next_scheduled('verificar_loteria_ganhador')) {
    wp_schedule_event(time(), 'daily', 'verificar_loteria_ganhador');
}
add_action('verificar_loteria_ganhador', 'verificar_e_notificar_ganhador');
?>
```

## 9. Cache Manual

### Limpar Cache

```php
<?php
// Limpar cache de uma loteria
seisdeagosto_clear_loteria_cache('megasena');

// Limpar cache de todas
seisdeagosto_clear_loteria_cache();

// Via WP-CLI (se disponível)
// wp transient delete loteria_megasena
?>
```

## 10. Debugging

### Ver Dados Completos da API

```php
<?php
$resultado = seisdeagosto_get_loteria_result('megasena');
echo '<pre>';
print_r($resultado);
echo '</pre>';
?>
```

### Logs

```php
<?php
// Habilitar logs no wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

// Ver logs em: wp-content/debug.log
?>
```

---

## 📚 Referências Rápidas

### Modalidades Disponíveis
- `megasena` - Mega Sena
- `lotofacil` - Lotofácil
- `quina` - Quina
- `lotomania` - Lotomania
- `timemania` - Timemania
- `duplasena` - Dupla Sena
- `federal` - Federal
- `loteca` - Loteca
- `diadesorte` - Dia de Sorte
- `supersete` - Super Sete
- `maismilionaria` - +Milionária

### Funções Helper
- `seisdeagosto_get_loteria_result()`
- `seisdeagosto_get_all_loterias()`
- `seisdeagosto_clear_loteria_cache()`
- `seisdeagosto_format_currency()`
- `seisdeagosto_format_date()`

### Shortcodes
- `[loteria modalidade="megasena"]`
- `[loterias_lista colunas="3"]`

---

**💡 Dica:** Combine diferentes métodos para criar experiências únicas!
