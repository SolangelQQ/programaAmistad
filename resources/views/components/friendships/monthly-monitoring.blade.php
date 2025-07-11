<!-- resources/views/components/monthly-monitoring.blade.php -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Monitoreo Mensual de Amistades</h3>
            <p class="text-sm text-gray-600 mt-1">Reporta el progreso y desafíos observados en las amistades del programa</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ \Carbon\Carbon::now()->locale('es')->translatedFormat('F Y') }}
            </span>
        </div>
    </div>

    <form id="monitoring-form" class="space-y-6">
        @csrf
        
        <!-- Información del Monitor -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="monitor-name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre del Líder de Tutores*
                </label>
                <input type="text" 
                       id="monitor-name" 
                       name="monitor_name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       placeholder="Tu nombre completo"
                       required>
            </div>
            
            <div>
                <label for="monitoring-period" class="block text-sm font-medium text-gray-700 mb-1">
                    Período de Monitoreo*
                </label>
                <select id="monitoring-period" 
                        name="monitoring_period"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        required>
                    <option value="">Seleccionar período</option>
                    <option value="enero">Enero</option>
                    <option value="febrero">Febrero</option>
                    <option value="marzo">Marzo</option>
                    <option value="abril">Abril</option>
                    <option value="mayo">Mayo</option>
                    <option value="junio">Junio</option>
                    <option value="julio">Julio</option>
                    <option value="agosto">Agosto</option>
                    <option value="septiembre">Septiembre</option>
                    <option value="octubre">Octubre</option>
                    <option value="noviembre">Noviembre</option>
                    <option value="diciembre">Diciembre</option>
                </select>
            </div>
        </div>

        <!-- Selección de Amistad -->
        <div>
            <label for="friendship-select" class="block text-sm font-medium text-gray-700 mb-1">
                Amistad Monitoreada*
            </label>
            <select id="friendship-select"
                    name="friendship_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    required>
                <option value="">Seleccionar amistad</option>
                <!-- Las opciones se cargarán dinámicamente -->
            </select>
        </div>

        <!-- Evaluación General -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Evaluación General de la Amistad*
            </label>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-2">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="general_evaluation" value="excelente" class="text-purple-600 focus:ring-purple-500" required>
                    <span class="text-sm text-gray-700">Excelente</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="general_evaluation" value="buena" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Buena</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="general_evaluation" value="regular" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Regular</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="general_evaluation" value="deficiente" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Deficiente</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="general_evaluation" value="critica" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Crítica</span>
                </label>
            </div>
        </div>

        <!-- Frecuencia de Encuentros -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Frecuencia de Encuentros durante el Mes*
            </label>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="meeting_frequency" value="semanal" class="text-purple-600 focus:ring-purple-500" required>
                    <span class="text-sm text-gray-700">Semanal</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="meeting_frequency" value="quincenal" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Quincenal</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="meeting_frequency" value="mensual" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Mensual</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="meeting_frequency" value="irregular" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Irregular</span>
                </label>
            </div>
        </div>

        <!-- Avances Específicos Observados -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Avances Específicos Observados* (Selecciona todos los que apliquen)
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="progress_areas[]" value="comunicacion" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Mejora en comunicación</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="progress_areas[]" value="confianza" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Mayor confianza mutua</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="progress_areas[]" value="independencia" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Mayor independencia</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="progress_areas[]" value="habilidades_sociales" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Habilidades sociales</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="progress_areas[]" value="autoestima" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Aumento de autoestima</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="progress_areas[]" value="participacion" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Mayor participación</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="progress_areas[]" value="integracion" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Mejor integración social</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="progress_areas[]" value="academico" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Mejora académica</span>
                </label>
            </div>
        </div>

        <!-- Desafíos Identificados -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Desafíos Identificados* (Selecciona todos los que apliquen)
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="challenges[]" value="tiempo_limitado" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Tiempo limitado para encuentros</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="challenges[]" value="diferencias_intereses" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Diferencias en intereses</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="challenges[]" value="comunicacion_dificil" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Dificultades de comunicación</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="challenges[]" value="falta_motivacion" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Falta de motivación</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="challenges[]" value="barreras_transporte" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Barreras de transporte</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="challenges[]" value="diferencias_edad" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Diferencias generacionales</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="challenges[]" value="resistencia_cambio" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Resistencia al cambio</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="challenges[]" value="apoyo_familiar" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Falta de apoyo familiar</span>
                </label>
            </div>
        </div>

        <!-- Nivel de Participación -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Nivel de Participación en Actividades*
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-2">Tutor*</label>
                    <select name="tutor_participation" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="">Seleccionar</option>
                        <option value="muy-activo">Muy activo</option>
                        <option value="activo">Activo</option>
                        <option value="moderado">Moderado</option>
                        <option value="pasivo">Pasivo</option>
                        <option value="muy-pasivo">Muy pasivo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-2">Líder de Amistad*</label>
                    <select name="leader_participation" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="">Seleccionar</option>
                        <option value="muy-activo">Muy activo</option>
                        <option value="activo">Activo</option>
                        <option value="moderado">Moderado</option>
                        <option value="pasivo">Pasivo</option>
                        <option value="muy-pasivo">Muy pasivo</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Nivel de Satisfacción -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Nivel de Satisfacción de los Participantes*
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-2">Tutor*</label>
                    <select name="tutor_satisfaction" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="">Seleccionar</option>
                        <option value="muy-satisfecho">Muy satisfecho</option>
                        <option value="satisfecho">Satisfecho</option>
                        <option value="neutral">Neutral</option>
                        <option value="insatisfecho">Insatisfecho</option>
                        <option value="muy-insatisfecho">Muy insatisfecho</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-2">Líder de Amistad*</label>
                    <select name="leader_satisfaction" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="">Seleccionar</option>
                        <option value="muy-satisfecho">Muy satisfecho</option>
                        <option value="satisfecho">Satisfecho</option>
                        <option value="neutral">Neutral</option>
                        <option value="insatisfecho">Insatisfecho</option>
                        <option value="muy-insatisfecho">Muy insatisfecho</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Acciones Sugeridas -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Acciones Sugeridas para el Próximo Período* (Selecciona todas las que apliquen)
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="suggested_actions[]" value="aumentar_frecuencia" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Aumentar frecuencia de encuentros</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="suggested_actions[]" value="diversificar_actividades" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Diversificar actividades</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="suggested_actions[]" value="capacitacion_adicional" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Capacitación adicional</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="suggested_actions[]" value="apoyo_recursos" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Apoyo con recursos</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="suggested_actions[]" value="mediacion_conflictos" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Mediación de conflictos</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="suggested_actions[]" value="involucrar_familia" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Involucrar más a la familia</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="suggested_actions[]" value="seguimiento_especializado" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Seguimiento especializado</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="suggested_actions[]" value="mantener_actual" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Mantener enfoque actual</span>
                </label>
            </div>
        </div>

        <!-- Requiere Atención Especial -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                ¿Esta amistad requiere atención especial o intervención inmediata?*
            </label>
            <div class="flex space-x-6">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="requires_attention" value="si" class="text-purple-600 focus:ring-purple-500" required>
                    <span class="text-sm text-gray-700 pr-4">Sí</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="requires_attention" value="no" class="text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">No</span>
                </label>
            </div>
        </div>

        <!-- Observaciones Específicas (Campo abierto solo para detalles específicos) -->
        <div>
            <label for="specific-observations" class="block text-sm font-medium text-gray-700 mb-1">
                Observaciones Específicas o Situaciones Particulares
            </label>
            <p class="text-xs text-gray-500 mb-2">Solo si hay situaciones específicas que requieren explicación adicional</p>
            <textarea id="specific-observations" 
                      name="specific_observations"
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                      placeholder="Describir solo situaciones específicas que requieren atención especial..."></textarea>
        </div>

        <!-- Botones de Acción -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            
            <div class="flex space-x-3">
                <button type="button" 
                        id="preview-btn"
                        class="inline-flex items-center px-4 py-2 border border-purple-300 shadow-sm text-sm font-medium rounded-md text-purple-700 bg-purple-50 hover:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Vista Previa
                </button>
                
                <button type="submit" 
                        id="submit-monitoring-btn"
                        class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Guardar Monitoreo
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal de Vista Previa -->
<div id="preview-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Vista Previa del Monitoreo</h3>
                <button id="close-preview" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="preview-content" class="space-y-4 max-h-96 overflow-y-auto">
                <!-- El contenido de la vista previa se generará dinámicamente -->
            </div>
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button id="close-preview-btn" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Cerrar
                </button>
                <button id="confirm-submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                    Confirmar y Enviar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadFriendships();
    initializeForm();
});

function loadFriendships() {
    const friendshipSelect = document.getElementById('friendship-select');
    
    // Mostrar loading
    friendshipSelect.innerHTML = '<option value="">Cargando amistades...</option>';
    
    fetch('/api/friendships/list', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            populateFriendshipSelect(data.friendships);
        } else {
            console.error('Error al cargar amistades:', data.message);
            friendshipSelect.innerHTML = '<option value="">Error al cargar amistades</option>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        friendshipSelect.innerHTML = '<option value="">Error de conexión</option>';
    });
}

function populateFriendshipSelect(friendships) {
    const friendshipSelect = document.getElementById('friendship-select');
    
    // Limpiar opciones existentes
    friendshipSelect.innerHTML = '<option value="">Seleccionar amistad</option>';
    
    // Agregar cada amistad como opción
    friendships.forEach(friendship => {
        const option = document.createElement('option');
        option.value = friendship.id;
        
        // Formato: "Buddy Name - PeerBuddy Name (Status)"
        const buddyName = `${friendship.buddy.first_name} ${friendship.buddy.last_name}`;
        const peerBuddyName = `${friendship.peer_buddy.first_name} ${friendship.peer_buddy.last_name}`;
        option.textContent = `${buddyName} - ${peerBuddyName} (${friendship.status})`;
        
        // Agregar datos adicionales como atributos
        option.setAttribute('data-buddy-id', friendship.buddy_id);
        option.setAttribute('data-peer-buddy-id', friendship.peer_buddy_id);
        option.setAttribute('data-status', friendship.status);
        
        friendshipSelect.appendChild(option);
    });
}

function initializeForm() {
    const form = document.getElementById('monitoring-form');
    const previewBtn = document.getElementById('preview-btn');
    const previewModal = document.getElementById('preview-modal');
    const closePreviewBtns = document.querySelectorAll('#close-preview, #close-preview-btn');
    const confirmSubmitBtn = document.getElementById('confirm-submit');
    
    // Manejar vista previa
    previewBtn.addEventListener('click', function() {
        if (validateForm()) {
            generatePreview();
            previewModal.classList.remove('hidden');
        }
    });
    
    // Cerrar modal de vista previa
    closePreviewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            previewModal.classList.add('hidden');
        });
    });
    
    // Confirmar y enviar desde vista previa
    confirmSubmitBtn.addEventListener('click', function() {
        previewModal.classList.add('hidden');
        submitForm();
    });
    
    // Manejar envío directo del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateForm()) {
            submitForm();
        }
    });
    
    // Cerrar modal al hacer clic fuera
    previewModal.addEventListener('click', function(e) {
        if (e.target === previewModal) {
            previewModal.classList.add('hidden');
        }
    });
}

function validateForm() {
    const form = document.getElementById('monitoring-form');
    const requiredFields = [
        'monitor_name',
        'monitoring_period',
        'friendship_id',
        'general_evaluation',
        'meeting_frequency',
        'tutor_participation',
        'leader_participation',
        'tutor_satisfaction',
        'leader_satisfaction',
        'requires_attention'
    ];
    
    let isValid = true;
    let firstError = null;
    
    // Limpiar errores previos
    clearFormErrors();
    
    // Validar campos requeridos
    requiredFields.forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (!field || !field.value.trim()) {
            markFieldAsError(field);
            if (!firstError) firstError = field;
            isValid = false;
        }
    });
    
    if (!isValid && firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstError.focus();
        showErrorMessage('Por favor completa todos los campos requeridos');
    }
    
    return isValid;
}

function clearFormErrors() {
    const errorFields = document.querySelectorAll('.border-red-500');
    errorFields.forEach(field => {
        field.classList.remove('border-red-500');
        field.classList.add('border-gray-300');
    });
}

function markFieldAsError(field) {
    if (field) {
        field.classList.remove('border-gray-300');
        field.classList.add('border-red-500');
    }
}

function generatePreview() {
    const form = document.getElementById('monitoring-form');
    const formData = new FormData(form);
    const previewContent = document.getElementById('preview-content');
    
    // Obtener el nombre de la amistad seleccionada
    const friendshipSelect = document.getElementById('friendship-select');
    const selectedFriendship = friendshipSelect.options[friendshipSelect.selectedIndex];
    
    let html = '<div class="space-y-4 text-sm">';
    
    // Información básica
    html += `<div class="bg-gray-50 p-3 rounded">
        <h4 class="font-medium text-gray-900 mb-2">Información del Monitoreo</h4>
        <p><strong>Monitor:</strong> ${formData.get('monitor_name')}</p>
        <p><strong>Período:</strong> ${capitalizeFirst(formData.get('monitoring_period'))}</p>
        <p><strong>Amistad:</strong> ${selectedFriendship.textContent}</p>
        <p><strong>Evaluación General:</strong> ${capitalizeFirst(formData.get('general_evaluation'))}</p>
    </div>`;
    
    // Frecuencia y participación
    html += `<div class="bg-gray-50 p-3 rounded">
        <h4 class="font-medium text-gray-900 mb-2">Participación y Frecuencia</h4>
        <p><strong>Frecuencia de encuentros:</strong> ${capitalizeFirst(formData.get('meeting_frequency'))}</p>
        <p><strong>Participación Tutor:</strong> ${capitalizeFirst(formData.get('tutor_participation').replace('-', ' '))}</p>
        <p><strong>Participación Líder:</strong> ${capitalizeFirst(formData.get('leader_participation').replace('-', ' '))}</p>
    </div>`;
    
    // Avances observados
    const progressAreas = formData.getAll('progress_areas[]');
    html += `<div class="bg-gray-50 p-3 rounded">
        <h4 class="font-medium text-gray-900 mb-2">Avances Observados</h4>
        <p>${progressAreas.length > 0 ? progressAreas.map(area => formatProgressArea(area)).join(', ') : 'Ningún avance seleccionado'}</p>
    </div>`;
    
    // Desafíos identificados
    const challenges = formData.getAll('challenges[]');
    html += `<div class="bg-gray-50 p-3 rounded">
        <h4 class="font-medium text-gray-900 mb-2">Desafíos Identificados</h4>
        <p>${challenges.length > 0 ? challenges.map(challenge => formatChallenge(challenge)).join(', ') : 'Ningún desafío seleccionado'}</p>
    </div>`;
    
    // Satisfacción
    html += `<div class="bg-gray-50 p-3 rounded">
        <h4 class="font-medium text-gray-900 mb-2">Satisfacción</h4>
        <p><strong>Satisfacción Tutor:</strong> ${capitalizeFirst(formData.get('tutor_satisfaction').replace('-', ' '))}</p>
        <p><strong>Satisfacción Líder:</strong> ${capitalizeFirst(formData.get('leader_satisfaction').replace('-', ' '))}</p>
    </div>`;
    
    // Acciones sugeridas
    const suggestedActions = formData.getAll('suggested_actions[]');
    html += `<div class="bg-gray-50 p-3 rounded">
        <h4 class="font-medium text-gray-900 mb-2">Acciones Sugeridas</h4>
        <p>${suggestedActions.length > 0 ? suggestedActions.map(action => formatSuggestedAction(action)).join(', ') : 'Ninguna acción sugerida'}</p>
    </div>`;
    
    // Atención especial
    html += `<div class="bg-gray-50 p-3 rounded">
        <h4 class="font-medium text-gray-900 mb-2">Atención Especial</h4>
        <p><strong>Requiere atención especial:</strong> ${formData.get('requires_attention') === 'si' ? 'Sí' : 'No'}</p>
    </div>`;
    
    // Observaciones específicas
    if (formData.get('specific_observations') && formData.get('specific_observations').trim()) {
        html += `<div class="bg-gray-50 p-3 rounded">
            <h4 class="font-medium text-gray-900 mb-2">Observaciones Específicas</h4>
            <p>${formData.get('specific_observations')}</p>
        </div>`;
    }
    
    html += '</div>';
    previewContent.innerHTML = html;
}

function submitForm() {
    const form = document.getElementById('monitoring-form');
    const submitBtn = document.getElementById('submit-monitoring-btn');
    const originalText = submitBtn.innerHTML;
    
    // Mostrar estado de carga
    submitBtn.innerHTML = `
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Guardando...
    `;
    submitBtn.disabled = true;
    
    // Preparar datos del formulario
    const formData = new FormData(form);
    
    // Enviar al controlador
    fetch('/monthly-monitoring', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessMessage(data.message || 'Monitoreo guardado exitosamente');
            form.reset();
            // Recargar lista de amistades por si algo cambió
            loadFriendships();
        } else {
            if (data.errors) {
                // Mostrar errores de validación
                handleValidationErrors(data.errors);
                showErrorMessage('Por favor corrige los errores indicados');
            } else {
                showErrorMessage(data.message || 'Error al guardar el monitoreo');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('Error de conexión. Por favor intenta nuevamente.');
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function handleValidationErrors(errors) {
    clearFormErrors();
    
    Object.keys(errors).forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            markFieldAsError(field);
        }
    });
}

function showSuccessMessage(message) {
    showMessage(message, 'success');
}

function showErrorMessage(message) {
    showMessage(message, 'error');
}

function showMessage(message, type = 'success') {
    const messageDiv = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const icon = type === 'success' 
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
    
    messageDiv.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-md shadow-lg z-50 max-w-md`;
    messageDiv.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${icon}
            </svg>
            <span class="text-sm">${message}</span>
        </div>
    `;
    
    document.body.appendChild(messageDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 5000);
    
    // Add click to close
    messageDiv.addEventListener('click', () => {
        messageDiv.remove();
    });
}

// Funciones auxiliares para formatear texto
function capitalizeFirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatProgressArea(area) {
    const areas = {
        'comunicacion': 'Mejora en comunicación',
        'confianza': 'Mayor confianza mutua',
        'independencia': 'Mayor independencia',
        'habilidades_sociales': 'Habilidades sociales',
        'autoestima': 'Aumento de autoestima',
        'participacion': 'Mayor participación',
        'integracion': 'Mejor integración social',
        'academico': 'Mejora académica'
    };
    return areas[area] || area;
}

function formatChallenge(challenge) {
    const challenges = {
        'tiempo_limitado': 'Tiempo limitado para encuentros',
        'diferencias_intereses': 'Diferencias en intereses',
        'comunicacion_dificil': 'Dificultades de comunicación',
        'falta_motivacion': 'Falta de motivación',
        'barreras_transporte': 'Barreras de transporte',
        'diferencias_edad': 'Diferencias generacionales',
        'resistencia_cambio': 'Resistencia al cambio',
        'apoyo_familiar': 'Falta de apoyo familiar'
    };
    return challenges[challenge] || challenge;
}

function formatSuggestedAction(action) {
    const actions = {
        'aumentar_frecuencia': 'Aumentar frecuencia de encuentros',
        'diversificar_actividades': 'Diversificar actividades',
        'capacitacion_adicional': 'Capacitación adicional',
        'apoyo_recursos': 'Apoyo con recursos',
        'mediacion_conflictos': 'Mediación de conflictos',
        'involucrar_familia': 'Involucrar más a la familia',
        'seguimiento_especializado': 'Seguimiento especializado',
        'mantener_actual': 'Mantener enfoque actual'
    };
    return actions[action] || action;
}
</script>