{{-- resources/views/components/quick-invite-button.blade.php --}}
@props(['user'])

<div x-data="quickInvite({{ $user->id }})" class="inline-block">
    <!-- Botón para enviar invitación -->
    <button @click="showModal = true" 
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
        Enviar Invitación
    </button>

    <!-- Modal de invitación -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="showModal = false"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Invitar a {{ $user->name }}
                    </h3>
                    <button @click="showModal = false" 
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="sendInvitation">
                    <div class="mb-4">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Mensaje personalizado (opcional)
                        </label>
                        <textarea 
                            x-model="message"
                            id="message" 
                            rows="3"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Escribe un mensaje personalizado..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                @click="showModal = false"
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" 
                                :disabled="loading"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50">
                            <span x-show="!loading">Enviar Invitación</span>
                            <span x-show="loading">Enviando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Notificación de éxito -->
    <div x-show="successMessage" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
         style="display: none;">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            ¡Invitación enviada correctamente!
        </div>
    </div>
</div>

<script>
function quickInvite(userId) {
    return {
        showModal: false,
        message: '',
        loading: false,
        successMessage: false,

        async sendInvitation() {
            this.loading = true;
            
            try {
                const response = await fetch('/notifications/friendship-invitation', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        message: this.message.trim() || null
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    this.showModal = false;
                    this.message = '';
                    this.successMessage = true;
                    
                    // Ocultar mensaje de éxito después de 3 segundos
                    setTimeout(() => {
                        this.successMessage = false;
                    }, 3000);

                    // Actualizar el dropdown de notificaciones si existe
                    if (window.refreshNotifications) {
                        window.refreshNotifications();
                    }
                } else {
                    alert('Error: ' + (result.error || 'No se pudo enviar la invitación'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al enviar la invitación');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>