{{-- programaAmistad/resources/views/components/friendship/friendship-details.blade.php --}}

<!-- Información del Emparejamiento -->
<div class="border-t border-gray-200 pt-5 mb-5">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('Información del Emparejamiento') }}
            </h3>
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <p class="text-gray-600 font-medium">{{ __('ID:') }}</p>
                        <p id="view_friendship_id" class="text-gray-800"></p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">{{ __('Fecha de Inicio:') }}</p>
                        <p id="view_start_date" class="text-gray-800"></p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">{{ __('Fecha de Fin:') }}</p>
                        <p id="view_end_date" class="text-gray-800"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-gray-600 font-medium">{{ __('Estado:') }}</p>
                        <div class="mt-1">
                            <span id="view_status_badge" class="px-3 py-1 text-sm font-semibold rounded-full"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Notas') }}</h3>
            <div id="view_notes" class="bg-gray-50 rounded-lg p-4 shadow-sm h-32 overflow-y-auto text-gray-700"></div>
        </div>
    </div>
</div>