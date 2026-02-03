# Guia Rápido - Bloco Mega Sena

## ✅ Como Usar

### 1. Adicionar o Bloco em uma Página

1. Vá ao editor Gutenberg
2. Clique no botão **+** para adicionar um bloco
3. Procure por **"Resultado Mega Sena"** ou **"Mega Sena"**
4. Clique para adicionar

### 2. Configurar o Bloco

No painel lateral direito, você encontrará:

**Configurações:**
- ✏️ Título do bloco
- ☑️ Mostrar/Ocultar número do concurso
- ☑️ Mostrar/Ocultar data do sorteio
- ☑️ Mostrar/Ocultar valor do prêmio
- ☑️ Mostrar/Ocultar próximo concurso

**Cores:**
- 🎨 Cor de fundo
- 🎨 Cor do texto
- 🎨 Cor das bolas numeradas

## 📄 Criar Página com Todas as Loterias

### Passo a Passo:

1. **WordPress Admin** → **Páginas** → **Adicionar Nova**
2. Digite um título: "Resultados das Loterias"
3. No painel direito, em **Atributos da Página**:
   - Selecione o template: **"Resultados das Loterias"**
4. Clique em **Publicar**

✨ **Pronto!** A página exibirá automaticamente todos os resultados das loterias.

## 🎯 Recursos

### Loterias Disponíveis:
- Mega Sena
- Lotofácil
- Quina
- Lotomania
- Timemania
- Dupla Sena
- Federal
- Loteca
- Dia de Sorte
- Super Sete
- +Milionária

### O que é Exibido:
- ✅ Números sorteados
- ✅ Data e número do concurso
- ✅ Valor do prêmio
- ✅ Premiação por faixa (Sena, Quina, Quadra, etc)
- ✅ Número de ganhadores
- ✅ Informação de acumulado
- ✅ Próximo concurso e valor estimado

## 🔄 Cache e Atualização

- Os resultados são atualizados automaticamente a cada **30 minutos**
- Para limpar o cache manualmente, use a função PHP:

```php
// Limpa cache de uma loteria específica
seisdeagosto_clear_loteria_cache('megasena');

// Limpa cache de todas as loterias
seisdeagosto_clear_loteria_cache();
```

## 💡 Dicas

### Personalização Rápida:

1. **Alterar Cores das Bolas:**
   - Edite o arquivo `blocks/mega-sena/style.css`
   - Procure por `.mega-sena-ball`

2. **Modificar Layout:**
   - Edite o arquivo `blocks/mega-sena/render.php`

3. **Adicionar Mais Informações:**
   - A API retorna muitos dados
   - Consulte a estrutura completa no README.md

## 🐛 Solução de Problemas

### Bloco não aparece no editor?
- Limpe o cache do WordPress
- Verifique se os arquivos foram criados corretamente
- Confirme que o tema está ativo

### Resultado não carrega?
- Verifique conexão com internet
- A API da Caixa pode estar temporariamente fora do ar
- Verifique logs de erro do WordPress

### Cores não mudam?
- Limpe o cache do navegador
- Force refresh: Ctrl + F5 (Windows) ou Cmd + Shift + R (Mac)

## 📚 Documentação Completa

Para mais detalhes técnicos, consulte o arquivo [README.md](README.md)

## 🎨 Exemplo de Uso no Código

```php
// Buscar último resultado da Mega Sena
$resultado = seisdeagosto_get_loteria_result('megasena');

// Buscar concurso específico
$resultado = seisdeagosto_get_loteria_result('megasena', 2654);

// Todas as loterias
$todas = seisdeagosto_get_all_loterias();

// Formatar valores
echo seisdeagosto_format_currency(1000000); // R$ 1.000.000,00
echo seisdeagosto_format_date('2026-02-01T00:00:00'); // 01/02/2026
```

---

**Desenvolvido com** ❤️ **usando Bootstrap e FontAwesome**
