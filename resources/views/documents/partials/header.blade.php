<div class=" shadow-sm border-b">
    <div class="mx-auto mt-6" >
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Documentos</h1>
                <p class="text-gray-600">Gestión de documentación de Best Buddies</p>
            </div>
            <div class="flex space-x-3">
                <button @click="showCreateModal = true" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Nuevo Documento</span>
                </button>
            </div>
        </div>
        
        @include('documents.partials.filters')
    </div>
</div>