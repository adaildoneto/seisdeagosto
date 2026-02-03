/**
 * Frontend JavaScript para o bloco Mega Sena
 * Adiciona animações e interatividade
 */

(function() {
    'use strict';

    // Anima as bolas quando entram na viewport
    function animateBalls() {
        const balls = document.querySelectorAll('.mega-sena-ball');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '0';
                        entry.target.style.transform = 'scale(0)';
                        
                        setTimeout(() => {
                            entry.target.style.transition = 'all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'scale(1)';
                        }, 50);
                    }, index * 100);
                    
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.5
        });

        balls.forEach(ball => {
            observer.observe(ball);
        });
    }

    // Adiciona efeito de pulso nas bolas ao passar o mouse
    function addHoverEffects() {
        const balls = document.querySelectorAll('.mega-sena-ball');
        
        balls.forEach(ball => {
            ball.addEventListener('mouseenter', function() {
                this.style.animation = 'pulse 0.5s ease';
            });
            
            ball.addEventListener('animationend', function() {
                this.style.animation = '';
            });
        });
    }

    // Adiciona CSS de animação dinamicamente
    function addAnimationStyles() {
        if (!document.getElementById('mega-sena-animations')) {
            const style = document.createElement('style');
            style.id = 'mega-sena-animations';
            style.textContent = `
                @keyframes pulse {
                    0%, 100% {
                        transform: scale(1);
                    }
                    50% {
                        transform: scale(1.15);
                    }
                }
                
                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                
                .mega-sena-premio {
                    animation: fadeInUp 0.8s ease-out;
                }
            `;
            document.head.appendChild(style);
        }
    }

    // Auto-refresh (opcional - comentado por padrão)
    function setupAutoRefresh() {
        const blocks = document.querySelectorAll('.wp-block-seisdeagosto-mega-sena');
        
        blocks.forEach(block => {
            // Verifica se tem atributo data-auto-refresh
            if (block.dataset.autoRefresh === 'true') {
                // Atualiza a cada 5 minutos (300000ms)
                setInterval(() => {
                    console.log('Auto-refresh habilitado (requer implementação AJAX)');
                    // Aqui você pode adicionar lógica AJAX para atualizar o conteúdo
                }, 300000);
            }
        });
    }

    // Tooltip com informações extras
    function addTooltips() {
        const balls = document.querySelectorAll('.mega-sena-ball');
        
        balls.forEach((ball, index) => {
            ball.setAttribute('title', `Número sorteado: ${ball.textContent}`);
            ball.style.cursor = 'pointer';
        });
    }

    // Inicializa quando o DOM estiver pronto
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                addAnimationStyles();
                animateBalls();
                addHoverEffects();
                addTooltips();
                // setupAutoRefresh(); // Descomente para habilitar auto-refresh
            });
        } else {
            addAnimationStyles();
            animateBalls();
            addHoverEffects();
            addTooltips();
            // setupAutoRefresh(); // Descomente para habilitar auto-refresh
        }
    }

    // Compatibilidade com Gutenberg Editor
    if (typeof wp !== 'undefined' && wp.domReady) {
        wp.domReady(init);
    } else {
        init();
    }

})();
