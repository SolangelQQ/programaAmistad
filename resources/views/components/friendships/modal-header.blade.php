{{-- programaAmistad/resources/views/components/friendship/modal-header.blade.php --}}

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-gray-900">
        {{ __('Detalles del Emparejamiento') }}
    </h2>
    <button type="button" 
            onclick="closeViewFriendshipModal()" 
            class="text-gray-400 hover:text-gray-500 transition-colors"
            aria-label="{{ __('Cerrar modal') }}">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>