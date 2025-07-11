document.addEventListener('DOMContentLoaded', function() {
    if (typeof Alpine !== 'undefined') {
        Alpine.data('dropdownMenu', (userId) => ({
            open: false,
            init() {
                // Aseguramos que el menú comience cerrado
                this.open = false;
                
                this.$watch('open', value => {
                    console.log(`Menu ${userId}: ${value ? 'abierto' : 'cerrado'}`);
                    
                    if (value) {
                        // Cuando se abre el menú, calculamos su posición
                        this.$nextTick(() => {
                            const button = this.$el.querySelector('button');
                            const menuContainer = document.getElementById(`dropdown-container-${userId}`);
                            
                            if (button && menuContainer) {
                                const buttonRect = button.getBoundingClientRect();
                                // Posicionamiento del menú con posición fixed
                                menuContainer.style.top = `${buttonRect.bottom + 5}px`;
                                menuContainer.style.left = `${buttonRect.left - 100}px`;
                                console.log(`Posicionando menú en: ${buttonRect.bottom + 5}px, ${buttonRect.left - 100}px`);
                            } else {
                                console.error(`No se encontró el botón o el contenedor del menú para el usuario ${userId}`);
                            }
                        });
                    }
                });
            },
            toggle() {
                this.open = !this.open;
                console.log(`Toggle menu ${userId}: ${this.open}`);
            }
        }));
    } else {
        console.error('Alpine.js no está cargado');
    }
});