/**
 * Auto-migração de blocos deprecados
 * Converte automaticamente u-correio68/titulo-com-icone para seisdeagosto/titulo-com-icone
 */

(function() {
    if (typeof wp === 'undefined' || !wp.blocks) return;

    const { addFilter } = wp.hooks;
    const { createBlock } = wp.blocks;

    /**
     * Auto-migra blocos com namespace antigo para o novo
     */
    addFilter(
        'blocks.registerBlockType',
        'seisdeagosto/auto-migrate-deprecated',
        function(settings, name) {
            // Se for o bloco novo, adiciona suporte para auto-migração
            if (name === 'seisdeagosto/titulo-com-icone') {
                const deprecated = settings.deprecated || [];
                
                // Adiciona versão deprecada que converte automaticamente
                deprecated.push({
                    attributes: settings.attributes,
                    
                    // Salva no formato antigo (para detectar blocos antigos)
                    save: function() {
                        return null; // Server-side rendering
                    },
                    
                    // Migra automaticamente quando detectado
                    migrate: function(attributes) {
                        // Apenas retorna os atributos, a conversão acontece automaticamente
                        return attributes;
                    },
                    
                    // Sempre elegível para migração
                    isEligible: function() {
                        return true;
                    }
                });
                
                settings.deprecated = deprecated;
            }
            
            return settings;
        }
    );

    /**
     * Substitui blocos antigos pelo novo namespace quando carregados no editor
     */
    addFilter(
        'blocks.getBlockType',
        'seisdeagosto/replace-old-namespace',
        function(blockType, blockName) {
            // Se detectar o bloco antigo, redireciona para o novo
            if (blockName === 'u-correio68/titulo-com-icone') {
                const newBlock = wp.blocks.getBlockType('seisdeagosto/titulo-com-icone');
                if (newBlock) {
                    console.log('[Auto-Migration] Redirecting u-correio68/titulo-com-icone to seisdeagosto/titulo-com-icone');
                    return newBlock;
                }
            }
            return blockType;
        }
    );

    /**
     * Hook que detecta e converte blocos antigos quando a página é carregada
     */
    wp.domReady(function() {
        const { select, dispatch } = wp.data;
        
        // Aguarda o editor estar pronto
        setTimeout(function() {
            const blocks = select('core/block-editor').getBlocks();
            let migratedCount = 0;
            
            function migrateBlocks(blocksArray) {
                blocksArray.forEach(function(block) {
                    // Detecta bloco com namespace antigo
                    if (block.name === 'u-correio68/titulo-com-icone') {
                        try {
                            // Cria novo bloco com mesmo conteúdo
                            const newBlock = createBlock(
                                'seisdeagosto/titulo-com-icone',
                                block.attributes,
                                block.innerBlocks
                            );
                            
                            // Substitui o bloco antigo pelo novo
                            dispatch('core/block-editor').replaceBlock(
                                block.clientId,
                                newBlock
                            );
                            
                            migratedCount++;
                            console.log('[Auto-Migration] Converted block:', block.clientId);
                        } catch (e) {
                            console.warn('[Auto-Migration] Failed to migrate block:', e);
                        }
                    }
                    
                    // Recursivo para blocos aninhados
                    if (block.innerBlocks && block.innerBlocks.length > 0) {
                        migrateBlocks(block.innerBlocks);
                    }
                });
            }
            
            migrateBlocks(blocks);
            
            if (migratedCount > 0) {
                console.log('[Auto-Migration] Total blocks migrated: ' + migratedCount);
                
                // Mostra aviso para salvar
                dispatch('core/notices').createNotice(
                    'info',
                    'Detectamos ' + migratedCount + ' bloco(s) "Título com Ícone" com namespace antigo. Foram convertidos automaticamente para o novo formato. Por favor, salve a página para preservar as mudanças.',
                    {
                        isDismissible: true,
                        type: 'snackbar'
                    }
                );
            }
        }, 1000);
    });

    console.log('[Auto-Migration] Deprecation handler loaded');
})();
