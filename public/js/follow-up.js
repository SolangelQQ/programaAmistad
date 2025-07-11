let currentFriendshipId = null;
let currentFollowUpId = null;

function openFollowUpModal(friendshipId) {
    currentFriendshipId = friendshipId;
    currentFollowUpId = null;
    
    fetch(`/friendships/${friendshipId}/tracking`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            populateFollowUpModal(data);
            document.getElementById('follow-up-modal').classList.remove('hidden');
            setupAttendanceForm(data.friendship);
            switchTab('follow-up'); // Asegurar que el tab correcto esté activo
        })
        .catch(error => {
            console.error('Error loading friendship data:', error);
            alert('Error al cargar los datos del emparejamiento');
        });
}

function closeFollowUpModal() {
    document.getElementById('follow-up-modal').classList.add('hidden');
    document.getElementById('follow-up-form').reset();
    document.getElementById('attendance-form').reset();
    currentFriendshipId = null;
    currentFollowUpId = null;
    
    // Resetear tabs
    switchTab('follow-up');
}

function switchTab(tabName) {
    // Ocultar todos los contenidos
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remover clase active de todos los tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600', 'border-purple-500', 'text-purple-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Mapear nombres de tabs a sus contenidos correspondientes
    let contentId, tabId, activeColor;
    
    switch(tabName) {
        case 'follow-up':
            contentId = 'follow-up-content';
            tabId = 'follow-up-tab';
            activeColor = 'blue';
            break;
        case 'attendance':
            contentId = 'attendance-content';
            tabId = 'attendance-tab';
            activeColor = 'blue';
            break;
        case 'history':
        case 'monitoring':
            contentId = 'monitoring-content';
            tabId = 'history-tab';
            activeColor = 'blue';
            break;
        default:
            console.warn('Tab no reconocido:', tabName);
            return;
    }
    
    // Mostrar contenido activo
    const activeContent = document.getElementById(contentId);
    if (activeContent) {
        activeContent.classList.remove('hidden');
    } else {
        console.error('Contenido no encontrado:', contentId);
    }
    
    // Activar tab
    const activeTab = document.getElementById(tabId);
    if (activeTab) {
        if (activeColor === 'blue') {
            activeTab.classList.add('border-blue-500', 'text-blue-600');
        } else if (activeColor === 'purple') {
            activeTab.classList.add('border-purple-500', 'text-purple-600');
        }
        activeTab.classList.remove('border-transparent', 'text-gray-500');
    } else {
        console.error('Tab no encontrado:', tabId);
    }
    
    // Ejecutar lógica específica para cada tab
    switch(tabName) {
        case 'follow-up':
            // Lógica específica para evaluación
            console.log('Tab de evaluación activado');
            break;
        case 'attendance':
            // Lógica específica para asistencia
            console.log('Tab de asistencia activado');
            break;
        case 'history':
        case 'monitoring':
            // Lógica específica para monitoreo
            console.log('Tab de monitoreo activado');
            loadMonitoringData();
            break;
    }
}

// Función auxiliar para cargar datos del monitoreo
function loadMonitoringData() {
    // Cargar amistades disponibles si no se han cargado
    const friendshipSelect = document.getElementById('friendship-select');
    if (friendshipSelect && friendshipSelect.options.length <= 1) {
        loadFriendships();
    }
    
    // Establecer fecha actual en el período de monitoreo
    const periodSelect = document.getElementById('monitoring-period');
    if (periodSelect) {
        const currentDate = new Date();
        const currentMonth = currentDate.toLocaleString('es-ES', { month: 'long' });
        const currentYear = currentDate.getFullYear();
        const currentPeriod = `${currentMonth}-${currentYear}`;
        
        // Buscar y seleccionar el período actual si existe
        for (let option of periodSelect.options) {
            if (option.value.toLowerCase().includes(currentMonth.toLowerCase())) {
                option.selected = true;
                break;
            }
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Activar el primer tab por defecto
    switchTab('follow-up');
    
    // Agregar event listeners a los tabs
    const tabs = document.querySelectorAll('.tab-button');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.id.replace('-tab', '');
            switchTab(tabName);
        });
    });
});

function populateFollowUpModal(data) {
    const friendship = data.friendship;
    
    // Poblar información del emparejamiento
    document.getElementById('followup-buddy-name').textContent = `${friendship.buddy.first_name} ${friendship.buddy.last_name}`;
    document.getElementById('followup-buddy-disability').textContent = `Discapacidad: ${friendship.buddy.disability || 'N/A'}`;
    document.getElementById('followup-peer-buddy-name').textContent = `${friendship.peer_buddy.first_name} ${friendship.peer_buddy.last_name}`;
    
    // Badge de estado
    const statusBadge = document.getElementById('followup-status');
    statusBadge.textContent = friendship.status;
    const statusClasses = {
        'Emparejado': 'bg-green-100 text-green-800',
        'Inactivo': 'bg-yellow-100 text-yellow-800',
        'Finalizado': 'bg-gray-100 text-gray-800'
    };
    statusBadge.className = `inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClasses[friendship.status] || 'bg-gray-100 text-gray-800'}`;
    
    // Fechas
    const startDate = friendship.start_date ? new Date(friendship.start_date).toLocaleDateString('es-ES') : 'N/A';
    const endDate = friendship.end_date ? new Date(friendship.end_date).toLocaleDateString('es-ES') : 'En curso';
    document.getElementById('followup-dates').textContent = `${startDate} - ${endDate}`;
    
    // Verificar si hay seguimiento existente
    checkExistingFollowUp(friendship.id);
}

function setupAttendanceForm(friendship) {
    // Mostrar nombres en los displays
    document.getElementById('buddy-name-display').textContent = `${friendship.buddy.first_name} ${friendship.buddy.last_name}`;
    document.getElementById('peer-buddy-name-display').textContent = `${friendship.peer_buddy.first_name} ${friendship.peer_buddy.last_name}`;
    
    // Configurar fechas del emparejamiento
    const startDate = friendship.start_date || friendship.created_at;
    const endDate = friendship.end_date || new Date().toISOString().split('T')[0];
    
    document.getElementById('attendance_start_date').value = startDate.split('T')[0];
    document.getElementById('attendance_end_date').value = endDate.split('T')[0];
    document.getElementById('attendance_start_date').min = startDate.split('T')[0];
    
    if (friendship.end_date) {
        document.getElementById('attendance_end_date').max = endDate.split('T')[0];
    }
}

function checkExistingFollowUp(friendshipId) {
    fetch(`/friendships/${friendshipId}/latest-follow-up`)
        .then(response => response.json())
        .then(data => {
            if (data.followUp) {
                currentFollowUpId = data.followUp.id;
                populateFollowUpForm(data.followUp);
                document.getElementById('follow-up-submit-btn').textContent = 'Actualizar Seguimiento';
                document.getElementById('follow-up-method').value = 'PUT';
            } else {
                document.getElementById('follow-up-submit-btn').textContent = 'Guardar Seguimiento';
                document.getElementById('follow-up-method').value = 'POST';
            }
        })
        .catch(error => {
            console.error('Error checking existing follow-up:', error);
        });
}

function populateFollowUpForm(followUp) {
    document.getElementById('buddy_progress').value = followUp.buddy_progress || '';
    document.getElementById('peer_buddy_progress').value = followUp.peer_buddy_progress || '';
    document.getElementById('relationship_quality').value = followUp.relationship_quality || '';
    document.getElementById('goals_achieved').value = followUp.goals_achieved || '';
    document.getElementById('challenges_faced').value = followUp.challenges_faced || '';
    document.getElementById('recommendations').value = followUp.recommendations || '';
    document.getElementById('next_steps').value = followUp.next_steps || '';
    document.getElementById('support_needed').value = followUp.support_needed || '';
    document.getElementById('next_follow_up_date').value = followUp.next_follow_up_date || '';
}

function createLocalDate(dateString) {
    const [year, month, day] = dateString.split('-').map(Number);
    return new Date(year, month - 1, day);
}

function formatDateToString(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function generateAttendanceDays() {
    const startDateStr = document.getElementById('attendance_start_date').value;
    const endDateStr = document.getElementById('attendance_end_date').value;
    
    document.getElementById('attendance_dates_hidden').value = JSON.stringify({
        start_date: startDateStr,
        end_date: endDateStr
    });

    if (!startDateStr || !endDateStr) {
        document.getElementById('attendance-days-container').innerHTML = `
            <div class="text-center py-8 text-gray-500">
                Selecciona las fechas de inicio y fin para generar los días
            </div>
        `;
        return;
    }
    
    const start = createLocalDate(startDateStr);
    const end = createLocalDate(endDateStr);
    const container = document.getElementById('attendance-days-container');
    
    if (start > end) {
        container.innerHTML = `
            <div class="text-center py-8 text-red-500">
                La fecha de inicio debe ser anterior a la fecha de fin
            </div>
        `;
        return;
    }
    
    // Obtener asistencias existentes
    fetch(`/friendships/${currentFriendshipId}/attendance-range?start_date=${startDateStr}&end_date=${endDateStr}`)
        .then(response => response.json())
        .then(data => {
            const existingAttendance = data.attendance || {};
            renderAttendanceDays(start, end, existingAttendance);
        })
        .catch(error => {
            console.error('Error loading existing attendance:', error);
            renderAttendanceDays(start, end, {});
        });
}

function renderAttendanceDays(startDate, endDate, existingAttendance) {
    const container = document.getElementById('attendance-days-container');
    let html = '<div class="space-y-3 max-h-96 overflow-y-auto p-4">';
    
    const currentDate = new Date(startDate);
    let dayCount = 0;
    
    while (currentDate <= endDate && dayCount < 365) {
        const dateStr = formatDateToString(currentDate);
        const dayName = currentDate.toLocaleDateString('es-ES', { weekday: 'long' });
        const dayMonth = currentDate.toLocaleDateString('es-ES', { day: 'numeric', month: 'long' });
        
        const attendance = existingAttendance[dateStr] || {};
        const buddyChecked = attendance.buddy_attended ? 'checked' : '';
        const peerBuddyChecked = attendance.peer_buddy_attended ? 'checked' : '';
        const attendanceId = attendance.id || '';
        const notes = attendance.notes || '';
        
        html += `
            <div class="bg-white border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 capitalize">${dayName}</h4>
                        <p class="text-sm text-gray-500">${dayMonth}, ${currentDate.getFullYear()}</p>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="buddy_${dateStr}" 
                                   name="attendance[${dateStr}][buddy_attended]" 
                                   value="1" 
                                   ${buddyChecked}
                                   class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded \">
                            <label for="buddy_${dateStr}" class="ml-2 text-sm font-medium text-blue-700 pr-3">
                                Buddy
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="peer_buddy_${dateStr}" 
                                   name="attendance[${dateStr}][peer_buddy_attended]" 
                                   value="1" 
                                   ${peerBuddyChecked}
                                   class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="peer_buddy_${dateStr}" class="ml-2 text-sm font-medium text-green-700 pr-3">
                                PeerBuddy
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <button type="button" 
                                    onclick="toggleNotesForDay('${dateStr}')" 
                                    class="flex items-center text-gray-600 hover:text-gray-800 focus:outline-none transition-colors">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V7l-5-5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 3v4a1 1 0 001 1h4"/>
                                </svg>
                                Notas
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Campo para notas (inicialmente oculto) -->
                <div id="notes_${dateStr}" class="mt-3 ${notes ? '' : 'hidden'}">
                    <label for="notes_input_${dateStr}" class="block text-xs font-medium text-gray-700 mb-1">
                        Notas del día
                    </label>
                    <textarea 
                        id="notes_input_${dateStr}"
                        name="attendance[${dateStr}][notes]" 
                        rows="2" 
                        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Observaciones del día...">${notes}</textarea>
                </div>
                
                <!-- Campo oculto para ID de asistencia existente -->
                ${attendanceId ? `<input type="hidden" name="attendance[${dateStr}][id]" value="${attendanceId}">` : ''}
                
                <!-- Campo oculto para la fecha -->
                <input type="hidden" name="attendance[${dateStr}][date]" value="${dateStr}">
            </div>
        `;
        
        currentDate.setDate(currentDate.getDate() + 1);
        dayCount++;
    }
    
    html += '</div>';
    
    if (dayCount === 0) {
        html = `
            <div class="text-center py-8 text-gray-500">
                No hay días en el rango seleccionado
            </div>
        `;
    }
    
    container.innerHTML = html;
}
// Función para mostrar/ocultar notas de un día específico
function toggleNotesForDay(dateStr) {
    const notesDiv = document.getElementById(`notes_${dateStr}`);
    const button = event.target.closest('button');
    
    if (notesDiv.classList.contains('hidden')) {
        notesDiv.classList.remove('hidden');
        button.classList.add('text-blue-600');
        button.classList.remove('text-gray-600');
    } else {
        notesDiv.classList.add('hidden');
        button.classList.remove('text-blue-600');
        button.classList.add('text-gray-600');
    }
}

// Función para seleccionar/deseleccionar todas las asistencias
function toggleAllAttendance(type, checked) {
    const checkboxes = document.querySelectorAll(`input[name*="[${type}_attended]"]`);
    checkboxes.forEach(checkbox => {
        checkbox.checked = checked;
    });
}

// Función para cargar el historial de seguimientos
function loadFollowUpHistory(friendshipId) {
    const container = document.getElementById('followup-history-content');
    
    // Mostrar loading
    container.innerHTML = `
        <div class="text-center py-8">
            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-gray-500 bg-white">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Cargando historial...
            </div>
        </div>
    `;
    
    fetch(`/friendships/${friendshipId}/follow-ups-history`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            renderFollowUpHistory(data.followUps || []);
        })
        .catch(error => {
            console.error('Error loading follow-up history:', error);
            container.innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <p>Error al cargar el historial</p>
                    <button onclick="loadFollowUpHistory(${friendshipId})" class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                        Reintentar
                    </button>
                </div>
            `;
        });
}

// Función para renderizar el historial de seguimientos
function renderFollowUpHistory(followUps) {
    const container = document.getElementById('followup-history-content');
    
    if (followUps.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="mt-2">No hay seguimientos registrados</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    followUps.forEach((followUp, index) => {
        const date = new Date(followUp.created_at).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        html += `
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-medium text-gray-900">Seguimiento #${followUps.length - index}</h4>
                    <span class="text-sm text-gray-500">${date}</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">${followUp.buddy_progress || 'N/A'}</div>
                        <div class="text-xs text-gray-500">Progreso Buddy</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">${followUp.peer_buddy_progress || 'N/A'}</div>
                        <div class="text-xs text-gray-500">Progreso PeerBuddy</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">${followUp.relationship_quality || 'N/A'}</div>
                        <div class="text-xs text-gray-500">Calidad Relación</div>
                    </div>
                </div>
                
                ${followUp.goals_achieved ? `
                    <div class="mb-3">
                        <h5 class="text-sm font-medium text-gray-700 mb-1">Objetivos Alcanzados:</h5>
                        <p class="text-sm text-gray-600">${followUp.goals_achieved}</p>
                    </div>
                ` : ''}
                
                ${followUp.challenges_faced ? `
                    <div class="mb-3">
                        <h5 class="text-sm font-medium text-gray-700 mb-1">Desafíos:</h5>
                        <p class="text-sm text-gray-600">${followUp.challenges_faced}</p>
                    </div>
                ` : ''}
                
                ${followUp.recommendations ? `
                    <div class="mb-3">
                        <h5 class="text-sm font-medium text-gray-700 mb-1">Recomendaciones:</h5>
                        <p class="text-sm text-gray-600">${followUp.recommendations}</p>
                    </div>
                ` : ''}
                
                ${followUp.next_follow_up_date ? `
                    <div class="text-sm text-gray-500">
                        <strong>Próximo seguimiento:</strong> ${new Date(followUp.next_follow_up_date).toLocaleDateString('es-ES')}
                    </div>
                ` : ''}
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Event listeners para el formulario de seguimiento
document.addEventListener('DOMContentLoaded', function() {
    // Formulario de seguimiento
    const followUpForm = document.getElementById('follow-up-form');
    if (followUpForm) {
        followUpForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitFollowUpForm();
        });
    }
    
    // Formulario de asistencia
    const attendanceForm = document.getElementById('attendance-form');
    if (attendanceForm) {
        attendanceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitAttendanceForm();
        });
    }
    
    // Event listeners para fechas de asistencia
    const startDateInput = document.getElementById('attendance_start_date');
    const endDateInput = document.getElementById('attendance_end_date');
    
    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', generateAttendanceDays);
        endDateInput.addEventListener('change', generateAttendanceDays);
    }
});

// Función para enviar el formulario de seguimiento
function submitFollowUpForm() {
    const form = document.getElementById('follow-up-form');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('follow-up-submit-btn');
    
    // Deshabilitar botón mientras se procesa
    submitBtn.disabled = true;
    submitBtn.textContent = 'Guardando...';
    
    // Determinar URL y método
    const method = document.getElementById('follow-up-method').value;
    let url = `/friendships/${currentFriendshipId}/follow-ups`;
    
    if (method === 'PUT' && currentFollowUpId) {
        url = `/friendships/${currentFriendshipId}/follow-ups/${currentFollowUpId}`;
        formData.append('_method', 'PUT');
    }
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Seguimiento guardado exitosamente');
            closeFollowUpModal();
            
            // Recargar la página o actualizar la tabla si es necesario
            if (typeof window.loadFriendships === 'function') {
                window.loadFriendships();
            } else {
                location.reload();
            }
        } else {
            throw new Error(data.message || 'Error al guardar el seguimiento');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Error al guardar el seguimiento');
    })
    .finally(() => {
        // Rehabilitar botón
        submitBtn.disabled = false;
        submitBtn.textContent = method === 'PUT' ? 'Actualizar Seguimiento' : 'Guardar Seguimiento';
    });
}

// Función para enviar el formulario de asistencia
function submitAttendanceForm() {
    const form = document.getElementById('attendance-form');
    const startDate = form.querySelector('#attendance_start_date').value;
    const endDate = form.querySelector('#attendance_end_date').value;
    
    if (!startDate || !endDate) {
        alert('Por favor selecciona ambas fechas (inicio y fin)');
        return;
    }

    const attendanceData = {
        start_date: startDate,
        end_date: endDate,
        attendance: []
    };

    // Recopilar datos de cada día
    const dateInputs = form.querySelectorAll('input[name*="[date]"]');
    
    dateInputs.forEach(dateInput => {
        const dateStr = dateInput.value;
        const dateMatch = dateInput.name.match(/attendance\[(.*?)\]/);
        
        if (dateMatch && dateMatch[1]) {
            const dateKey = dateMatch[1];
            
            const buddyChecked = form.querySelector(`input[name="attendance[${dateKey}][buddy_attended]"]`)?.checked || false;
            const peerChecked = form.querySelector(`input[name="attendance[${dateKey}][peer_buddy_attended]"]`)?.checked || false;
            const notes = form.querySelector(`textarea[name="attendance[${dateKey}][notes]"]`)?.value || '';
            const attendanceId = form.querySelector(`input[name="attendance[${dateKey}][id]"]`)?.value || null;

            const attendanceRecord = {
                date: dateStr,
                buddy_attended: buddyChecked,
                peer_buddy_attended: peerChecked,
                notes: notes.trim()
            };

            // Incluir el ID si existe para indicar que es una actualización
            if (attendanceId && attendanceId !== '') {
                attendanceRecord.id = parseInt(attendanceId);
            }

            attendanceData.attendance.push(attendanceRecord);
        }
    });

    const submitBtn = document.getElementById('attendance-submit-btn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Guardando...';

    fetch(`/friendships/${currentFriendshipId}/attendance`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(attendanceData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(`Asistencia guardada exitosamente. Registros procesados: ${data.processed || 0}`);
            // Recargar los días para mostrar los cambios
            generateAttendanceDays();
            closeFollowUpModal();
        } else {
            throw new Error(data.message || 'Error al guardar');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Mensaje más específico para errores de duplicado
        if (error.message.includes('Duplicate entry')) {
            alert('Error: Ya existe un registro de asistencia para una o más fechas seleccionadas. Por favor verifica los datos.');
        } else {
            alert(error.message || 'Error al guardar la asistencia');
        }
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Guardar Asistencia';
    });
}