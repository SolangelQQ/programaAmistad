{{-- programaAmistad/resources/views/components/friendship/attendance-section.blade.php --}}

<!-- Sección de Asistencia -->
<div class="border-t border-gray-200 pt-5 mb-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
            {{ __('Registros de Asistencia') }}
        </h3>
        <div id="attendance_status_indicator" class="flex items-center">
            <!-- Se llenará dinámicamente -->
        </div>
    </div>
    
    <div id="attendance_content" class="bg-purple-50 rounded-lg p-4 shadow-sm">
        <!-- Se llenará dinámicamente -->
    </div>
    
    <div id="attendance_table_section" class="mt-4 hidden">
        @include('components.friendships.attendance-table')
        
        <div id="attendance_summary" class="mt-4 text-sm text-gray-600">
            <!-- Resumen se llenará dinámicamente -->
        </div>
    </div>
</div>