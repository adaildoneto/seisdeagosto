/**
 * Mega Sena Block - Inicialização Segura
 * Garante que todas as dependências do WordPress estão carregadas
 */

( function() {
    'use strict';
    
    /**
     * Verifica se as dependências do WordPress estão disponíveis
     */
    function checkDependencies() {
        const required = [
            'wp',
            'wp.blocks',
            'wp.element',
            'wp.blockEditor',
            'wp.components',
            'wp.i18n',
            'wp.data'
        ];
        
        const missing = [];
        
        required.forEach( function( dep ) {
            const parts = dep.split( '.' );
            let obj = window;
            
            for ( let i = 0; i < parts.length; i++ ) {
                if ( ! obj[ parts[i] ] ) {
                    missing.push( dep );
                    break;
                }
                obj = obj[ parts[i] ];
            }
        } );
        
        return missing;
    }
    
    /**
     * Inicializa o bloco quando estiver pronto
     */
    function initBlock() {
        const missing = checkDependencies();
        
        if ( missing.length > 0 ) {
            console.warn( '[Mega Sena Block] Aguardando dependências:', missing );
            // Tenta novamente em 100ms
            setTimeout( initBlock, 100 );
            return;
        }
        
        console.log( '[Mega Sena Block] Todas as dependências carregadas. Pronto para registrar.' );
        
        // Dispara evento customizado indicando que pode registrar o bloco
        if ( window.CustomEvent ) {
            const event = new CustomEvent( 'megaSenaBlockReady', {
                detail: { timestamp: Date.now() }
            } );
            window.dispatchEvent( event );
        }
    }
    
    // Inicia verificação quando DOM estiver pronto
    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', initBlock );
    } else {
        initBlock();
    }
    
} )();
