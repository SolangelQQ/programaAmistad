<div x-show="showDetailsModal" 
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showDetailsModal = false"></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Detalles del Documento</h3>
                    <button @click="showDetailsModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div x-show="documentDetails" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Título</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="documentDetails?.title"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Categoría</label>
                            <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" 
                                  x-text="documentDetails?.category"></span>
                        </div>
                    </div>

                    <div x-show="documentDetails?.chapter">
                        <label class="block text-sm font-medium text-gray-700">Capítulo</label>
                        <p class="mt-1 text-sm text-gray-900" x-text="documentDetails?.chapter"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descripción</label>
                        <p class="mt-1 text-sm text-gray-900" x-text="documentDetails?.description || 'Sin descripción'"></p>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de archivo</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="documentDetails?.file_type"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tamaño</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="documentDetails?.file_size"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Subido por</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="documentDetails?.uploaded_by"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha de creación</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="documentDetails?.created_at"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Última actualización</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="documentDetails?.updated_at"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                
                <button @click="showDetailsModal = false"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
