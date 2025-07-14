{{-- programaAmistad/resources/views/modals/friendships/view.blade.php --}}

<!-- Modal para ver detalles del Emparejamiento -->
<div id="view-friendship-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-6xl max-h-[90vh] overflow-y-auto">
        <div id="modal-content">
            
            @include('components.friendships.modal-header')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                @include('components.friendships.buddy-info-card')
                @include('components.friendships.peerbuddy-info-card')
            </div>
            
            @include('components.friendships.leaders-info')
            
            @include('components.friendships.friendship-details')
            
            @include('components.friendships.follow-up-section')
            
            @include('components.friendships.attendance-section')
            
            @include('components.friendships.modal-actions')
            
        </div>
    </div>
</div>

<!-- <script src="{{ asset('js/friendshipModal.js') }}"></script> -->
<script>
    src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"
// Variable para almacenar el contenido original del modal
let originalModalContent = null;

// Función principal para mostrar detalles del emparejamiento
async function showFriendshipDetails(friendshipId) {
    const modal = document.getElementById('view-friendship-modal');
    const modalContent = document.getElementById('modal-content');
    
    try {
        // Mostrar loading
        modal.classList.remove('hidden');
        modalContent.innerHTML = `
            <div class="flex justify-center items-center p-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-2">Cargando detalles del emparejamiento...</span>
            </div>
        `;

        // Hacer la petición
        const response = await fetch(`/friendships/${friendshipId}/show`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error('Error al cargar los datos');

        const data = await response.json();
        
        // Restaurar contenido original
        modalContent.innerHTML = originalModalContent;
        
        // Poblar los datos
        populateModalData(data);
        handleFollowUpDisplay(data);
        handleAttendanceDisplay(data);

    } catch (error) {
        modalContent.innerHTML = `
            <div class="p-6 text-center">
                <div class="text-red-600 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Error al cargar los datos</h3>
                <p class="text-gray-600 mb-4">${error.message}</p>
                <button onclick="closeViewFriendshipModal()" 
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Cerrar
                </button>
            </div>
        `;
    }
}

function populateModalData(data) {
    try {
        const buddyData = data.buddy || {};
        document.getElementById('view_buddy_name').textContent = 
            `${buddyData.first_name || ''} ${buddyData.last_name || ''}`.trim() || 'N/A';
        document.getElementById('view_buddy_disability').textContent = 
            buddyData.disability || 'No especificada';
        document.getElementById('view_buddy_age').textContent = 
            buddyData.age ? `${buddyData.age} años` : 'N/A';
        document.getElementById('view_buddy_ci').textContent = 
            buddyData.ci || 'N/A';
        document.getElementById('view_buddy_phone').textContent = 
            buddyData.phone || 'N/A';
        document.getElementById('view_buddy_email').textContent = 
            buddyData.email || 'No especificado';
        document.getElementById('view_buddy_address').textContent = 
            buddyData.address || 'N/A';
        
        const peerBuddyData = data.peerBuddy || {};
        document.getElementById('view_peerbuddy_name').textContent = 
            `${peerBuddyData.first_name || ''} ${peerBuddyData.last_name || ''}`.trim() || 'N/A';
        document.getElementById('view_peerbuddy_age').textContent = 
            peerBuddyData.age ? `${peerBuddyData.age} años` : 'N/A';
        document.getElementById('view_peerbuddy_ci').textContent = 
            peerBuddyData.ci || 'N/A';
        document.getElementById('view_peerbuddy_phone').textContent = 
            peerBuddyData.phone || 'N/A';
        document.getElementById('view_peerbuddy_email').textContent = 
            peerBuddyData.email || 'No especificado';
        document.getElementById('view_peerbuddy_address').textContent = 
            peerBuddyData.address || 'N/A';
        
        // Información de líderes
        const buddyLeader = data.buddyLeader || {};
        const peerBuddyLeader = data.peerBuddyLeader || {};
        
        document.getElementById('view_buddy_leader_name').textContent = 
            buddyLeader.name || 'No asignado';
        document.getElementById('view_buddy_leader_email').textContent = 
            buddyLeader.email || 'N/A';
        document.getElementById('view_peerbuddy_leader_name').textContent = 
            peerBuddyLeader.name || 'No asignado';
        document.getElementById('view_peerbuddy_leader_email').textContent = 
            peerBuddyLeader.email || 'N/A';
        
        // Información del emparejamiento
        const friendshipData = data.friendship || {};
        document.getElementById('view_friendship_id').textContent = 
            friendshipData.id || 'N/A';
        document.getElementById('view_start_date').textContent = 
            friendshipData.start_date ? new Date(friendshipData.start_date).toLocaleDateString('es-ES') : 'N/A';
        document.getElementById('view_end_date').textContent = 
            friendshipData.end_date ? new Date(friendshipData.end_date).toLocaleDateString('es-ES') : 'No definida';
        document.getElementById('view_notes').textContent = 
            friendshipData.notes || 'Sin notas adicionales';
        
        // Status badge
        const statusBadge = document.getElementById('view_status_badge');
        if (statusBadge) {
            statusBadge.textContent = friendshipData.status || 'N/A';
            statusBadge.className = `px-3 py-1 text-sm font-semibold rounded-full ${getStatusBadgeClass(friendshipData.status)}`;
        }

        // Manejo de asistencia
        if (data.attendanceRecords && data.attendanceRecords.length > 0) {
            displayAttendanceRecords(data.attendanceRecords);
        } else {
            const attendanceSection = document.getElementById('attendance-section');
            if (attendanceSection) {
                attendanceSection.classList.add('hidden');
            }
        }
    } catch (error) {
        console.error('Error al poblar los datos:', error);
    }
}

function getStatusBadgeClass(status) {
    switch (status) {
        case 'Emparejado':
            return 'bg-green-100 text-green-800';
        case 'Inactivo':
            return 'bg-yellow-100 text-yellow-800';
        case 'Finalizado':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function closeViewFriendshipModal() {
    document.getElementById('view-friendship-modal').classList.add('hidden');
}

// Función mejorada para manejar la visualización de asistencias
function generateAttendanceTable(records) {
    if (!records || records.length === 0) {
        return '<p class="text-gray-500">No hay registros de asistencia recientes</p>';
    }

    return `
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buddy</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PeerBuddy</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notas</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    ${records.map(record => `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                ${new Date(record.date).toLocaleDateString('es-ES')}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                ${record.buddy_attended ? '✅ Asistió' : '❌ Faltó'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                ${record.peer_buddy_attended ? '✅ Asistió' : '❌ Faltó'}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                ${record.notes || 'Sin notas'}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

// Función alternativa con día de la semana
function formatDateWithDay(date) {
    try {
        if (typeof date === 'string') {
            date = new Date(date);
        }
        
        if (!(date instanceof Date) || isNaN(date.getTime())) {
            return 'Fecha inválida';
        }
        
        const dayName = date.toLocaleDateString('es-ES', { weekday: 'long' });
        const dateString = date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
        return `${dayName}, ${dateString}`;
        
    } catch (error) {
        return 'Error en fecha';
    }
}

// Si quieres también corregir la función formatDate original que está causando el problema:
function formatDate(date) {
    return formatDateCorrect(date);
}

// Función adicional para mostrar solo el día y mes (útil para rangos del mismo año)
function formatDateShort(date) {
    try {
        if (typeof date === 'string') {
            date = new Date(date);
        }
        
        if (!(date instanceof Date) || isNaN(date.getTime())) {
            return 'Fecha inválida';
        }
        
        // Formato: "13 jun"
        return date.toLocaleDateString('es-ES', {
            day: 'numeric',
            month: 'short'
        });
        
    } catch (error) {
        return 'Error';
    }
}


// Función mejorada para manejar la visualización de asistencias - ACTUALIZADA
function handleAttendanceDisplay(data) {
    const statusIndicator = document.getElementById('attendance_status_indicator');
    const content = document.getElementById('attendance_content');
    const tableSection = document.getElementById('attendance_table_section');
    const tableBody = document.getElementById('attendance_table_body');
    const summary = document.getElementById('attendance_summary');

    // Verificar si hay registros de asistencia
    if (!data.attendanceRecords || data.attendanceRecords.length === 0) {
        showNoAttendanceMessage(statusIndicator, content, tableSection);
        return;
    }

    // Obtener SOLO los registros de la última actualización
    const lastUpdateTimestamp = data.attendanceStats.last_update;
    
    // Filtrar registros que coincidan EXACTAMENTE con el último timestamp
    const currentRecords = data.attendanceRecords.filter(record => {
        const recordTimestamp = record.updated_at;
        return recordTimestamp === lastUpdateTimestamp;
    });

    if (currentRecords.length === 0) {
        showNoAttendanceMessage(statusIndicator, content, tableSection);
        return;
    }

    // Calcular estadísticas SOLO de los registros actuales
    const currentStats = {
        total: currentRecords.length,
        buddy_attended: currentRecords.filter(r => r.buddy_attended).length,
        peer_attended: currentRecords.filter(r => r.peer_buddy_attended).length,
        both_attended: currentRecords.filter(r => r.buddy_attended && r.peer_buddy_attended).length,
        last_update: lastUpdateTimestamp
    };

    // Mostrar indicador de estado
    statusIndicator.innerHTML = `
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            ${currentStats.total} Registro${currentStats.total !== 1 ? 's' : ''} de Asistencia
        </span>
    `;

    // Mostrar estadísticas actuales - CORREGIDO: usar formatDateRangeCorrect
    content.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="text-center bg-blue-50 rounded-lg p-3">
                <div class="text-2xl font-bold text-blue-800">
                    ${currentStats.total > 0 ? Math.round((currentStats.buddy_attended / currentStats.total) * 100) : 0}%
                </div>
                <div class="text-sm text-blue-600">Asistencia Buddy</div>
                <div class="text-xs text-gray-500">
                    ${currentStats.buddy_attended}/${currentStats.total} sesiones
                </div>
            </div>
            <div class="text-center bg-green-50 rounded-lg p-3">
                <div class="text-2xl font-bold text-green-800">
                    ${currentStats.total > 0 ? Math.round((currentStats.peer_attended / currentStats.total) * 100) : 0}%
                </div>
                <div class="text-sm text-green-600">Asistencia PeerBuddy</div>
                <div class="text-xs text-gray-500">
                    ${currentStats.peer_attended}/${currentStats.total} sesiones
                </div>
            </div>
            <div class="text-center bg-purple-50 rounded-lg p-3">
                <div class="text-2xl font-bold text-purple-800">
                    ${currentStats.total > 0 ? Math.round((currentStats.both_attended / currentStats.total) * 100) : 0}%
                </div>
                <div class="text-sm text-purple-600">Ambos Presentes</div>
                <div class="text-xs text-gray-500">
                    ${currentStats.both_attended}/${currentStats.total} sesiones
                </div>
            </div>
        </div>
        <div class="text-center text-sm text-gray-600">
            <div class="bg-blue-50 rounded-lg p-2 inline-block">
                📅 Período registrado: ${formatDateRangeCorrect(currentRecords)}
            </div>
            <div class="text-xs text-gray-500 mt-1">
                Última actualización: ${formatDateTimeCorrect(currentStats.last_update)}
            </div>
        </div>
    `;

    // Ordenar registros por fecha (más reciente primero)
    const sortedRecords = currentRecords.sort((a, b) => new Date(b.date) - new Date(a.date));

    // Llenar tabla con registros ordenados - CORREGIDO: usar formatDateCorrect
    tableBody.innerHTML = sortedRecords.map(record => {
        // CORREGIDO: usar las funciones correctas de formato
        const formattedDate = formatDateCorrect(record.date);
        const weekday = formatWeekdayCorrect(record.date);
        
        return `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="flex flex-col">
                        <span class="font-medium">
                            ${formattedDate}
                        </span>
                        <span class="text-xs text-gray-500">
                            ${weekday}
                        </span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${record.buddy_attended ? 
                        '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">✅ Asistió</span>' : 
                        '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">❌ Faltó</span>'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${record.peer_buddy_attended ? 
                        '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">✅ Asistió</span>' : 
                        '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">❌ Faltó</span>'}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    ${record.notes || '<span class="text-gray-400 italic">Sin notas</span>'}
                </td>
            </tr>
        `;
    }).join('');

    // Mostrar resumen
    summary.innerHTML = `
        <div class="bg-gray-50 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Resumen del Período Actual</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <p class="flex justify-between">
                        <span class="font-medium text-blue-700">• Buddy:</span> 
                        <span>${currentStats.buddy_attended}/${currentStats.total} asistencias</span>
                    </p>
                    <p class="flex justify-between">
                        <span class="font-medium text-green-700">• PeerBuddy:</span> 
                        <span>${currentStats.peer_attended}/${currentStats.total} asistencias</span>
                    </p>
                </div>
                <div class="space-y-2">
                    <p class="flex justify-between">
                        <span class="font-medium text-purple-700">• Ambos presentes:</span> 
                        <span>${currentStats.both_attended} sesiones</span>
                    </p>
                    <p class="flex justify-between">
                        <span class="font-medium text-gray-700">• Total sesiones:</span> 
                        <span>${currentStats.total}</span>
                    </p>
                </div>
            </div>
        </div>
    `;

    tableSection.classList.remove('hidden');
}

// FUNCIONES AUXILIARES CORREGIDAS:

// Función corregida para formatear fechas individuales
function formatDateCorrect(dateString) {
    try {
        const date = new Date(dateString);
        
        if (isNaN(date.getTime())) {
            return 'Fecha inválida';
        }

        return date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
    } catch (error) {
        return 'Error en fecha';
    }
}

// Función corregida para día de la semana
function formatWeekdayCorrect(dateString) {
    try {
        const date = new Date(dateString);
        
        if (isNaN(date.getTime())) {
            return 'Día inválido';
        }
        return date.toLocaleDateString('es-ES', { weekday: 'long' });
        
    } catch (error) {
        return 'Error';
    }
}

// Función corregida para rango de fechas
function formatDateRangeCorrect(records) {
    if (!records || records.length === 0) return 'Sin registros';
    
    try {
        const validDates = records
            .map(r => {
                try {
                    const date = new Date(r.date);
                    return isNaN(date.getTime()) ? null : date;
                } catch (error) {
                    return null;
                }
            })
            .filter(date => date !== null)
            .sort((a, b) => a - b);

        if (validDates.length === 0) {
            return 'Fechas inválidas';
        }

        const startDate = validDates[0];
        const endDate = validDates[validDates.length - 1];

        if (validDates.length === 1) {
            return formatDateCorrect(startDate);
        }

        return `${formatDateCorrect(startDate)} - ${formatDateCorrect(endDate)}`;
        
    } catch (error) {
        return 'Error en rango de fechas';
    }
}

// Función corregida para fecha y hora
function formatDateTimeCorrect(dateTimeString) {
    try {
        const date = new Date(dateTimeString);
        
        if (isNaN(date.getTime())) {
            return 'Fecha/hora inválida';
        }

        return date.toLocaleString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
    } catch (error) {
        return 'Error en fecha/hora';
    }
}
// Función para mostrar mensaje cuando no hay asistencias
function showNoAttendanceMessage(statusIndicator, content, tableSection) {
    statusIndicator.innerHTML = `
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            Sin Asistencias Registradas
        </span>
    `;
    
    content.innerHTML = `
            <div class="text-center py-8">
                <svg class="mx-auto h-16 w-16 text-purple-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-lg font-medium text-purple-800 mb-2">No se han registrado asistencias</h3>
                <p class="text-sm text-purple-600 mb-4">Este emparejamiento aún no tiene registros de asistencia</p>
                <div class="bg-purple-100 rounded-lg p-4 text-left max-w-md mx-auto">
                    <h4 class="font-medium text-purple-800 mb-2">¿Cómo registrar asistencias?</h4>
                    <ul class="text-sm text-purple-700 space-y-1">
                        <li>• Accede a la sección de Asistencias</li>
                        <li>• Selecciona la fecha de la sesión</li>
                        <li>• Marca la asistencia de cada participante</li>
                        <li>• Agrega notas si es necesario</li>
                    </ul>
                </div>
            </div>
        `;
    tableSection.classList.add('hidden');
}

// Función para abrir el modal de asistencia
function openAttendanceModal(friendshipId, date = null) {
    // Si se proporciona una fecha, verificar si ya existe un registro
    if (date) {
        fetch(`/friendships/${friendshipId}/check-attendance?date=${date}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    document.getElementById('save-attendance-btn').textContent = 'Actualizar Asistencia';
                    fillAttendanceForm(data.attendance);
                } else {
                    document.getElementById('save-attendance-btn').textContent = 'Guardar Asistencia';
                    resetAttendanceForm();
                }
            });
    }

    document.getElementById('attendance-modal').classList.remove('hidden');
}

// Función para guardar/actualizar asistencia
function saveAttendance(friendshipId) {
    const form = document.getElementById('attendance-form');
    const formData = new FormData(form);
    const btn = document.getElementById('save-attendance-btn');
    const originalBtnText = btn.textContent;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';

    fetch(`/friendships/${friendshipId}/attendance`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessAlert(data.message);
            updateLastAttendanceView(data.attendance);
            closeAttendanceModal();
        } else {
            showErrorAlert(data.message);
        }
    })
    .catch(error => {
        showErrorAlert('Error en la conexión: ' + error.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = originalBtnText;
    });
}

// Función para actualizar la vista con el último registro
function updateLastAttendanceView(attendance) {
    const lastAttendanceContainer = document.getElementById('last-attendance-container');
    
    lastAttendanceContainer.innerHTML = `
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600">Fecha</p>
                <p class="font-bold">${new Date(attendance.date).toLocaleDateString('es-ES')}</p>
            </div>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600">Buddy</p>
                ${attendance.buddy_attended ? 
                    '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Asistió</span>' : 
                    '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Faltó</span>'}
            </div>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600">PeerBuddy</p>
                ${attendance.peer_buddy_attended ? 
                    '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Asistió</span>' : 
                    '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Faltó</span>'}
            </div>
        </div>
        ${attendance.notes ? `
            <div class="bg-gray-50 p-3 rounded-lg">
                <p class="text-sm font-medium text-gray-600 mb-1">Notas</p>
                <p class="text-sm text-gray-700">${attendance.notes}</p>
            </div>
        ` : ''}
    `;
}

// Función para mostrar mensajes de error mejorados
function showErrorAlert(message) {
    const alertDiv = document.getElementById('error-alert');
    alertDiv.innerHTML = `
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
            <div class="flex justify-between">
                <div>
                    <p class="font-bold">Error</p>
                    <p>${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-700">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
}
// Función para mostrar la tabla de asistencias
function showAttendanceTable(records) {
    const attendanceBody = document.getElementById('attendance_table_body');
    if (!attendanceBody) {
        console.error('Elemento attendance_table_body no encontrado');
        return;
    }
    
    // Limpiar tabla
    attendanceBody.innerHTML = '';
    
    // Ordenar por fecha (más reciente primero)
    const sortedRecords = [...records].sort((a, b) => new Date(b.date) - new Date(a.date));
    
    sortedRecords.forEach(record => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        
        // Celda de fecha
        const dateCell = document.createElement('td');
        dateCell.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-900';
        const date = new Date(record.date);
        dateCell.innerHTML = `
            <div class="flex flex-col">
                <span class="font-medium">${date.toLocaleDateString('es-ES')}</span>
                <span class="text-xs text-gray-500">${date.toLocaleDateString('es-ES', {weekday: 'long'})}</span>
            </div>
        `;
        
        // Celdas de asistencia
        const buddyCell = createAttendanceCell(record.buddy_attended);
        const peerCell = createAttendanceCell(record.peer_buddy_attended);
        
        // Celda de notas
        const notesCell = document.createElement('td');
        notesCell.className = 'px-6 py-4 text-sm text-gray-500 max-w-xs';
        notesCell.textContent = record.notes || 'Sin notas';
        
        // Agregar celdas a la fila
        row.appendChild(dateCell);
        row.appendChild(buddyCell);
        row.appendChild(peerCell);
        row.appendChild(notesCell);
        
        attendanceBody.appendChild(row);
    });
}

function createAttendanceCell(attended) {
    const cell = document.createElement('td');
    cell.className = 'px-6 py-4 whitespace-nowrap';
    
    if (attended) {
        cell.innerHTML = `
            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Asistió
            </span>
        `;
    } else {
        cell.innerHTML = `
            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                Faltó
            </span>
        `;
    }
    
    return cell;
}

let showingAllRecords = false;
function showAllAttendanceRecords() {
    // almacenar los registros originales en una variable global
    if (window.currentAttendanceRecords) {
        showingAllRecords = true;
        showAttendanceTable(window.currentAttendanceRecords, false);

        const infoRow = document.createElement('tr');
        infoRow.innerHTML = `
            <td colspan="4" class="px-6 py-3 text-center text-sm text-gray-500 bg-gray-50">
                <span class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Mostrando todos los ${window.currentAttendanceRecords.length} registros
                    <button onclick="showLatestAttendanceRecords()" class="ml-2 text-blue-600 hover:text-blue-800 underline">
                        Mostrar solo los últimos
                    </button>
                </span>
            </td>
        `;
        document.getElementById('attendance_table_body').appendChild(infoRow);
    }
}

function showLatestAttendanceRecords() {
    if (window.currentAttendanceRecords) {
        showingAllRecords = false;
        showAttendanceTable(window.currentAttendanceRecords, true, 2);
    }
}

//mostrar asistencias
function displayAttendanceRecords(records) {
    const container = document.getElementById('attendance-records-container');
    container.innerHTML = ''; // Limpiar contenedor
    
    // Crear tabla de asistencias
    const table = document.createElement('div');
    table.className = 'overflow-x-auto bg-white rounded-lg shadow mt-4';
    
    let html = `
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buddy</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PeerBuddy</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notas</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
    `;
    
    records.forEach(record => {
        html += `
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${new Date(record.date).toLocaleDateString('es-ES')}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${record.buddy_attended ? 
                        '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Asistió</span>' : 
                        '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Faltó</span>'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${record.peer_buddy_attended ? 
                        '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Asistió</span>' : 
                        '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Faltó</span>'}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    ${record.notes || '-'}
                </td>
            </tr>
        `;
    });
    
    html += `</tbody></table>`;
    table.innerHTML = html;
    container.appendChild(table);
    
    // Mostrar estadísticas
    const statsContainer = document.getElementById('attendance-stats');
    const buddyAttended = records.filter(r => r.buddy_attended).length;
    const peerAttended = records.filter(r => r.peer_buddy_attended).length;
    const bothAttended = records.filter(r => r.buddy_attended && r.peer_buddy_attended).length;
    
    statsContainer.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm font-medium text-blue-800">Buddy</p>
                <p class="text-2xl font-bold">${buddyAttended}/${records.length} (${Math.round(buddyAttended/records.length*100)}%)</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm font-medium text-green-800">PeerBuddy</p>
                <p class="text-2xl font-bold">${peerAttended}/${records.length} (${Math.round(peerAttended/records.length*100)}%)</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <p class="text-sm font-medium text-purple-800">Ambos</p>
                <p class="text-2xl font-bold">${bothAttended}/${records.length} (${Math.round(bothAttended/records.length*100)}%)</p>
            </div>
        </div>
    `;
}

</script>