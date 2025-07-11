{{-- programaAmistad/resources/views/components/friendship/follow-up-evaluations.blade.php --}}

<!-- Sección de evaluaciones -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
        <p class="text-sm font-medium text-gray-600 mb-3 text-center">
            {{ __('Progreso Buddy') }}
        </p>
        <div id="buddy_progress_stars" class="flex justify-center">
            <!-- Contenido se llenará dinámicamente -->
        </div>
    </div>
    
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
        <p class="text-sm font-medium text-gray-600 mb-3 text-center">
            {{ __('Progreso PeerBuddy') }}
        </p>
        <div id="peer_buddy_progress_stars" class="flex justify-center">
            <!-- Contenido se llenará dinámicamente -->
        </div>
    </div>
    
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
        <p class="text-sm font-medium text-gray-600 mb-3 text-center">
            {{ __('Calidad Relación') }}
        </p>
        <div id="relationship_quality_stars" class="flex justify-center">
            <!-- Contenido se llenará dinámicamente -->
        </div>
    </div>
</div>