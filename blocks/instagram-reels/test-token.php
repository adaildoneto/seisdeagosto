<?php
/**
 * Script de teste para validar Access Token do Instagram
 * 
 * USO:
 * 1. Cole seu Access Token abaixo
 * 2. Acesse: seu-site.com/wp-content/themes/seisdeagosto/blocks/instagram-reels/test-token.php
 * 3. Veja os resultados e diagnóstico
 */

// COLE SEU ACCESS TOKEN AQUI:
$access_token = '';

// Username para testar (deixe vazio para testar seus próprios posts)
$test_username = '';

// ====================================
// NÃO EDITE ABAIXO DESTA LINHA
// ====================================

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Instagram Token Tester</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        h1 { color: #333; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        code { background: #e9ecef; padding: 2px 6px; border-radius: 3px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table th, table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background: #f8f9fa; font-weight: bold; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
    </style>
</head>
<body>
    <h1>🔍 Instagram Access Token Tester</h1>
    
    <?php if (empty($access_token)) : ?>
        <div class="warning">
            <strong>⚠️ Token não configurado!</strong>
            <p>Edite este arquivo e cole seu Access Token na variável <code>$access_token</code> no topo do arquivo.</p>
        </div>
    <?php else : ?>
        
        <div class="section">
            <h2>📊 Teste 1: Validação do Token</h2>
            <?php
            // Teste 1: Validar token básico
            $me_url = 'https://graph.instagram.com/me?access_token=' . urlencode($access_token) . '&fields=id,username,account_type';
            $me_response = file_get_contents($me_url);
            $me_data = json_decode($me_response, true);
            
            if (isset($me_data['error'])) :
            ?>
                <div class="error">
                    <strong>❌ Token inválido!</strong>
                    <p><strong>Erro:</strong> <?php echo htmlspecialchars($me_data['error']['message']); ?></p>
                    <p><strong>Código:</strong> <?php echo htmlspecialchars($me_data['error']['code']); ?></p>
                </div>
            <?php else : ?>
                <div class="success">
                    <strong>✅ Token válido!</strong>
                </div>
                <table>
                    <tr>
                        <th>Instagram User ID</th>
                        <td><?php echo htmlspecialchars($me_data['id']); ?></td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td>@<?php echo htmlspecialchars($me_data['username'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Tipo de Conta</th>
                        <td>
                            <?php 
                            $account_type = $me_data['account_type'] ?? 'PERSONAL';
                            if ($account_type === 'BUSINESS') {
                                echo '<span class="badge badge-success">BUSINESS ✓</span>';
                            } else {
                                echo '<span class="badge badge-warning">PERSONAL</span>';
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            <?php endif; ?>
        </div>
        
        <?php if (!isset($me_data['error'])) : ?>
            
            <div class="section">
                <h2>📸 Teste 2: Buscar Seus Posts</h2>
                <?php
                $media_url = 'https://graph.instagram.com/me/media?access_token=' . urlencode($access_token) . '&fields=id,media_type,media_url,thumbnail_url,caption,permalink&limit=5';
                $media_response = file_get_contents($media_url);
                $media_data = json_decode($media_response, true);
                
                if (isset($media_data['error'])) :
                ?>
                    <div class="error">
                        <strong>❌ Erro ao buscar posts!</strong>
                        <p><?php echo htmlspecialchars($media_data['error']['message']); ?></p>
                    </div>
                <?php elseif (empty($media_data['data'])) : ?>
                    <div class="warning">
                        <strong>⚠️ Nenhum post encontrado</strong>
                        <p>Sua conta não possui posts ou eles não são acessíveis via API.</p>
                    </div>
                <?php else : ?>
                    <div class="success">
                        <strong>✅ <?php echo count($media_data['data']); ?> posts encontrados!</strong>
                    </div>
                    <table>
                        <tr>
                            <th>Tipo</th>
                            <th>ID</th>
                            <th>Caption</th>
                            <th>Link</th>
                        </tr>
                        <?php foreach ($media_data['data'] as $item) : ?>
                        <tr>
                            <td>
                                <?php 
                                if ($item['media_type'] === 'VIDEO') {
                                    echo '<span class="badge badge-warning">VIDEO</span>';
                                } else {
                                    echo '<span class="badge badge-success">IMAGE</span>';
                                }
                                ?>
                            </td>
                            <td><code><?php echo substr($item['id'], 0, 20); ?>...</code></td>
                            <td><?php echo htmlspecialchars(substr($item['caption'] ?? '', 0, 50)); ?>...</td>
                            <td><a href="<?php echo htmlspecialchars($item['permalink']); ?>" target="_blank">Ver</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($test_username)) : ?>
                <div class="section">
                    <h2>🔎 Teste 3: Business Discovery (@<?php echo htmlspecialchars($test_username); ?>)</h2>
                    <?php
                    $clean_username = ltrim($test_username, '@');
                    $discovery_url = 'https://graph.instagram.com/' . $me_data['id'] . '?fields=business_discovery.username(' . urlencode($clean_username) . '){media.limit(5){id,media_type,caption,permalink}}&access_token=' . urlencode($access_token);
                    $discovery_response = file_get_contents($discovery_url);
                    $discovery_data = json_decode($discovery_response, true);
                    
                    if (isset($discovery_data['error'])) :
                    ?>
                        <div class="error">
                            <strong>❌ Erro ao buscar perfil!</strong>
                            <p><strong>Erro:</strong> <?php echo htmlspecialchars($discovery_data['error']['message']); ?></p>
                            
                            <?php if ($me_data['account_type'] !== 'BUSINESS') : ?>
                                <div class="warning" style="margin-top: 15px;">
                                    <strong>⚠️ Sua conta não é Business!</strong>
                                    <p>Para usar Business Discovery API e buscar posts de outros perfis, você precisa de uma conta Instagram Business.</p>
                                    <p><strong>Como converter:</strong></p>
                                    <ol>
                                        <li>Abra o Instagram no celular</li>
                                        <li>Configurações → Conta → Mudar para conta profissional</li>
                                        <li>Escolha "Empresa" (não "Criador")</li>
                                        <li>Vincule uma Página do Facebook</li>
                                        <li>Gere um novo token</li>
                                    </ol>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php elseif (empty($discovery_data['business_discovery']['media']['data'])) : ?>
                        <div class="warning">
                            <strong>⚠️ Nenhum post encontrado para @<?php echo htmlspecialchars($clean_username); ?></strong>
                            <p>Verifique se o perfil existe e é público.</p>
                        </div>
                    <?php else : ?>
                        <div class="success">
                            <strong>✅ <?php echo count($discovery_data['business_discovery']['media']['data']); ?> posts encontrados de @<?php echo htmlspecialchars($clean_username); ?>!</strong>
                        </div>
                        <table>
                            <tr>
                                <th>Tipo</th>
                                <th>ID</th>
                                <th>Caption</th>
                                <th>Link</th>
                            </tr>
                            <?php foreach ($discovery_data['business_discovery']['media']['data'] as $item) : ?>
                            <tr>
                                <td>
                                    <?php 
                                    if ($item['media_type'] === 'VIDEO') {
                                        echo '<span class="badge badge-warning">VIDEO</span>';
                                    } else {
                                        echo '<span class="badge badge-success">IMAGE</span>';
                                    }
                                    ?>
                                </td>
                                <td><code><?php echo substr($item['id'], 0, 20); ?>...</code></td>
                                <td><?php echo htmlspecialchars(substr($item['caption'] ?? '', 0, 50)); ?>...</td>
                                <td><a href="<?php echo htmlspecialchars($item['permalink']); ?>" target="_blank">Ver</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="section">
                <h2>📋 Resumo e Recomendações</h2>
                
                <?php if ($me_data['account_type'] !== 'BUSINESS' && !empty($test_username)) : ?>
                    <div class="warning">
                        <strong>⚠️ Conta não é Business</strong>
                        <p>Para buscar posts de outros perfis, você precisa converter sua conta para Instagram Business e vincular uma Página do Facebook.</p>
                    </div>
                <?php endif; ?>
                
                <?php if ($me_data['account_type'] === 'BUSINESS') : ?>
                    <div class="success">
                        <strong>✅ Conta Business detectada!</strong>
                        <p>Você pode usar Business Discovery API para buscar posts de outros perfis.</p>
                    </div>
                <?php endif; ?>
                
                <div class="info">
                    <strong>💡 Como usar no WordPress:</strong>
                    <ul>
                        <li><strong>Para seus posts:</strong> Cole apenas o Access Token, deixe o campo "Perfil" vazio</li>
                        <li><strong>Para outro perfil:</strong> Cole o Access Token + digite @usuario no campo "Perfil" (requer conta Business)</li>
                    </ul>
                </div>
            </div>
            
        <?php endif; ?>
        
    <?php endif; ?>
    
    <div class="section" style="font-size: 12px; color: #666;">
        <p><strong>Debug Info:</strong></p>
        <p>PHP Version: <?php echo phpversion(); ?></p>
        <p>allow_url_fopen: <?php echo ini_get('allow_url_fopen') ? 'Enabled' : 'Disabled'; ?></p>
    </div>
    
</body>
</html>
