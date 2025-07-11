{{-- programaAmistad/resources/views/components/friendship/follow-up-section.blade.php --}}

<!-- Sección de Seguimiento -->
<div class="border-t border-gray-200 pt-5 mb-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
            <svg class="inline-block w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            {{ __('Estado del Seguimiento') }}
        </h3>
        <div id="follow_up_status_indicator" class="flex items-center">
            <!-- Se llenará dinámicamente -->
        </div>
    </div>
    
    <!-- Contenido del seguimiento -->
    <div id="follow_up_content" class="bg-indigo-50 rounded-lg p-4 shadow-sm">
        <!-- Se llenará dinámicamente -->
    </div>
    
    <!-- Último seguimiento (si existe) -->
    <div id="last_follow_up_section" class="mt-4 hidden">
        <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('Último Seguimiento') }}</h4>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            @include('components.friendships.follow-up-evaluations')
            
            @include('components.friendships.follow-up-details')
            
            @include('components.friendships.follow-up-footer')
        </div>
    </div>
</div>