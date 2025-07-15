@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto" style="max-width: 95%">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white">
                <!-- Tab Navigation -->
                @include('components.tabs.tab-navigation')
                
                <!-- Friendship Management Section -->
                <div id="friendships-section">
                    @include('components.friendships.section')
                </div>

                <!-- Activities Section -->
                <div id="activities-section" class="hidden">
                    @include('components.activities.section')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Existing Modals (New Friendship, Edit Friendship, Delete Confirmation) from previous code -->
<!-- @include('modals.friendships.create') -->

@include('modals.friendships.edit')
@include('modals.friendships.delete')
@include('modals.friendships.view')

@include('buddies.partials.delete-modal')

<!-- Delete Buddy Confirmation Modal -->
@include('modals.buddies.delete')

<!-- Activities Modals -->
@include('modals.activities.create')
@include('modals.friendships.create')


<!-- Include the fixed JS file -->
<!-- <script src="{{ asset('js/friendships.js') }}"></script> -->
<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad para abrir/cerrar modales de FRIENDSHIPS
    
    window.openNewFriendshipModal = function() {
        document.getElementById('new-friendship-modal').classList.remove('hidden');
    };
    
    window.closeNewFriendshipModal = function() {
        document.getElementById('new-friendship-modal').classList.add('hidden');
    };
    
    // Función para ver detalles de un emparejamiento
    window.viewFriendshipDetails = function(friendshipId) {
        fetch(`/friendships/${friendshipId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar los datos del emparejamiento');
                }
                return response.json();
            })
            .then(data => {
                // Información del Friendship
                document.getElementById('view_friendship_id').textContent = data.friendship.id;
                document.getElementById('view_start_date').textContent = data.friendship.start_date;
                document.getElementById('view_end_date').textContent = data.friendship.end_date || 'N/A';
                document.getElementById('view_notes').textContent = data.friendship.notes || 'Sin notas';
                
                // Configurar el badge de estado
                const statusBadge = document.getElementById('view_status_badge');
                statusBadge.textContent = data.friendship.status;
                
                // Aplicar colores según el estado
                if (data.friendship.status === 'Emparejado') {
                    statusBadge.classList.add('bg-green-100', 'text-green-800');
                    statusBadge.classList.remove('bg-red-100', 'text-red-800', 'bg-yellow-100', 'text-yellow-800');
                } else if (data.friendship.status === 'Inactivo') {
                    statusBadge.classList.add('bg-red-100', 'text-red-800');
                    statusBadge.classList.remove('bg-green-100', 'text-green-800', 'bg-yellow-100', 'text-yellow-800');
                } else {
                    statusBadge.classList.add('bg-yellow-100', 'text-yellow-800');
                    statusBadge.classList.remove('bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
                }
                
                // Información del Buddy
                document.getElementById('view_buddy_name').textContent = data.buddy.first_name + ' ' + data.buddy.last_name;
                document.getElementById('view_buddy_disability').textContent = data.buddy.disability || 'N/A';
                document.getElementById('view_buddy_age').textContent = `${data.buddy.age} años`;
                document.getElementById('view_buddy_ci').textContent = data.buddy.ci;
                document.getElementById('view_buddy_phone').textContent = data.buddy.phone;
                document.getElementById('view_buddy_email').textContent = data.buddy.email || 'N/A';
                document.getElementById('view_buddy_address').textContent = data.buddy.address;
                
                // Información del PeerBuddy
                document.getElementById('view_peerbuddy_name').textContent = data.peerBuddy.first_name + ' ' + data.peerBuddy.last_name;
                document.getElementById('view_peerbuddy_age').textContent = `${data.peerBuddy.age} años`;
                document.getElementById('view_peerbuddy_ci').textContent = data.peerBuddy.ci;
                document.getElementById('view_peerbuddy_phone').textContent = data.peerBuddy.phone;
                document.getElementById('view_peerbuddy_email').textContent = data.peerBuddy.email || 'N/A';
                document.getElementById('view_peerbuddy_address').textContent = data.peerBuddy.address;
                
                // Mostrar el modal
                document.getElementById('view-friendship-modal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos. Por favor, inténtalo de nuevo.');
            });
    };
    
    window.closeViewFriendshipModal = function() {
        document.getElementById('view-friendship-modal').classList.add('hidden');
    };
    
    window.openEditFriendshipModal = function(friendshipId, startDate, endDate, status, notes) {
        const form = document.getElementById('edit-friendship-form');
        form.action = `/friendships/${friendshipId}`;
        
        document.getElementById('edit_start_date').value = startDate;
        document.getElementById('edit_end_date').value = endDate || '';
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_notes').value = notes || '';
        
        document.getElementById('edit-friendship-modal').classList.remove('hidden');
    };
    
    window.closeEditFriendshipModal = function() {
        document.getElementById('edit-friendship-modal').classList.add('hidden');
    };
    
    // Funcionalidad de confirmación de eliminación de FRIENDSHIPS
    window.confirmDelete = function(friendshipId) {
        const form = document.getElementById('delete-friendship-form');
        form.action = `/friendships/${friendshipId}`;
        document.getElementById('delete-confirmation-modal').classList.remove('hidden');
    };
    
    window.closeDeleteModal = function() {
        document.getElementById('delete-confirmation-modal').classList.add('hidden');
    };
    
    // Funcionalidad para eliminar buddies
    window.confirmDeleteBuddy = function(buddyId) {
        const form = document.getElementById('delete-buddy-form');
        form.action = `/buddies/${buddyId}`;
        document.getElementById('delete-buddy-modal').classList.remove('hidden');
    };
    
    window.closeDeleteBuddyModal = function() {
        document.getElementById('delete-buddy-modal').classList.add('hidden');
    };
    
    // Funcionalidad del sistema de tabs
    const switchToTab = function(targetSectionId) {
        console.log('Switching to tab:', targetSectionId);
        
        // Lista de todas las secciones posibles
        const allSections = ['friendships-section', 'activities-section', 'buddies-section'];
        
        // Ocultar todas las secciones
        allSections.forEach(sectionId => {
            const section = document.getElementById(sectionId);
            if (section) {
                section.classList.add('hidden');
                console.log('Hiding section:', sectionId);
            }
        });
        
        // Mostrar la sección seleccionada
        const targetSection = document.getElementById(targetSectionId);
        if (targetSection) {
            targetSection.classList.remove('hidden');
            console.log('Showing section:', targetSectionId);
        } else {
            console.error('Section not found:', targetSectionId);
        }
        
        // Actualizar el estado visual de las pestañas
        document.querySelectorAll('.tab-link').forEach(tab => {
            tab.classList.remove('border-indigo-500', 'text-gray-900');
            tab.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Activar la pestaña correspondiente
        const activeTab = document.querySelector(`[data-target="${targetSectionId}"]`);
        if (activeTab) {
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.add('border-indigo-500', 'text-gray-900');
        }
    };
    
    // Event listeners para las pestañas
    document.querySelectorAll('.tab-link').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            console.log('Tab clicked, target:', targetId);
            switchToTab(targetId);
        });
    });
    
    // Inicialización: mostrar la pestaña de amistades por defecto
    setTimeout(() => {
        switchToTab('friendships-section');
    }, 100);
    
    
    // Validación de formularios de FRIENDSHIPS
    const newFriendshipForm = document.getElementById('new-friendship-form');
    if (newFriendshipForm) {
        newFriendshipForm.addEventListener('submit', function(e) {
            const buddyId = document.getElementById('buddy_id').value;
            const peerBuddyId = document.getElementById('peer_buddy_id').value;
            
            if (!buddyId || !peerBuddyId) {
                e.preventDefault();
                alert('Por favor selecciona un Buddy y un PeerBuddy');
                return;
            }
            
            if (buddyId === peerBuddyId) {
                e.preventDefault();
                alert('El Buddy y el PeerBuddy no pueden ser la misma persona');
                return;
            }
        });
    }
});
</script> -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Sistema de emparejamientos inicializado');
    
    // === CONFIGURACIÓN DE DEBUGGING ===
    const DEBUG_MODE = true;
    
    function debugLog(message, data = null) {
        if (DEBUG_MODE) {
            console.log(`🔍 [DEBUG] ${message}`, data || '');
        }
    }
    
    function errorLog(message, error = null) {
        console.error(`❌ [ERROR] ${message}`, error || '');
    }
    
    // === FUNCIONES PARA MODALES DE EMPAREJAMIENTO ===
    
    // Abrir modal de nuevo emparejamiento
    window.openNewFriendshipModal = function() {
        const modal = document.getElementById('new-friendship-modal');
        if (modal) {
            modal.classList.remove('hidden');
            debugLog('Modal de nuevo emparejamiento abierto');
        } else {
            errorLog('Modal new-friendship-modal no encontrado');
        }
    };
    
    // Cerrar modal de nuevo emparejamiento
    window.closeNewFriendshipModal = function() {
        const modal = document.getElementById('new-friendship-modal');
        if (modal) {
            modal.classList.add('hidden');
            debugLog('Modal de nuevo emparejamiento cerrado');
        }
    };
    
    // === FUNCIÓN DE ENVÍO DE FORMULARIO CON DEBUGGING MEJORADO ===
    
    const newFriendshipForm = document.getElementById('new-friendship-form');
    if (newFriendshipForm) {
        debugLog('Formulario de nuevo emparejamiento encontrado');
        
        newFriendshipForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevenir envío normal para debugging
            
            debugLog('📝 Formulario de nuevo emparejamiento enviado');
            
            // Recopilar datos del formulario
            const formData = new FormData(this);
            const buddyId = formData.get('buddy_id');
            const peerBuddyId = formData.get('peer_buddy_id');
            const startDate = formData.get('start_date');
            const endDate = formData.get('end_date');
            const status = formData.get('status');
            const notes = formData.get('notes');
            const buddyLeaderId = formData.get('buddy_leader_id');
            const peerBuddyLeaderId = formData.get('peer_buddy_leader_id');
            
            debugLog('Datos del formulario recopilados:', {
                buddyId,
                peerBuddyId,
                startDate,
                endDate,
                status,
                notes,
                buddyLeaderId,
                peerBuddyLeaderId
            });
            
            // Validaciones
            if (!buddyId || !peerBuddyId) {
                errorLog('Faltan buddy_id o peer_buddy_id');
                alert('Por favor selecciona un Buddy y un PeerBuddy');
                return false;
            }
            
            if (buddyId === peerBuddyId) {
                errorLog('buddy_id y peer_buddy_id son iguales');
                alert('No puedes emparejar una persona consigo misma');
                return false;
            }
            
            if (!startDate) {
                errorLog('Falta start_date');
                alert('Por favor selecciona una fecha de inicio');
                return false;
            }
            
            debugLog('✅ Validaciones pasadas, enviando datos...');
            
            // Verificar CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                errorLog('CSRF token no encontrado');
                alert('Error de seguridad: Token CSRF no encontrado');
                return false;
            }
            
            debugLog('CSRF token encontrado:', csrfToken.getAttribute('content'));
            
            // Preparar headers
            const headers = {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            };
            
            debugLog('Headers preparados:', headers);
            
            // Verificar la URL de acción
            const actionUrl = this.action || '/friendships';
            debugLog('URL de acción:', actionUrl);
            
            // Verificar conectividad básica
            debugLog('🌐 Verificando conectividad...');
            
            // Enviar con fetch para mejor manejo de errores
            fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: headers
            })
            .then(response => {
                debugLog('📡 Respuesta recibida:', {
                    status: response.status,
                    statusText: response.statusText,
                    ok: response.ok,
                    url: response.url
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                // Verificar tipo de contenido
                const contentType = response.headers.get('content-type');
                debugLog('Tipo de contenido:', contentType);
                
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    return response.text();
                }
            })
            .then(data => {
                debugLog('✅ Respuesta exitosa:', data);
                
                if (typeof data === 'object' && data.success) {
                    debugLog('Emparejamiento creado exitosamente');
                    window.closeNewFriendshipModal();
                    
                    // Mostrar mensaje de éxito
                    alert('Emparejamiento creado exitosamente');
                    
                    // Recargar la página o actualizar la tabla
                    location.reload();
                } else if (typeof data === 'string' && data.includes('success')) {
                    debugLog('Respuesta HTML recibida - probablemente exitosa');
                    window.closeNewFriendshipModal();
                    location.reload();
                } else {
                    errorLog('Respuesta inesperada:', data);
                    alert('Error: Respuesta inesperada del servidor');
                }
            })
            .catch(error => {
                errorLog('💥 Error en la solicitud:', error);
                
                // Diagnóstico detallado del error
                if (error.name === 'TypeError' && error.message.includes('Load failed')) {
                    errorLog('🔍 Diagnóstico: Error de conectividad');
                    alert('❌ Error de conexión:\n\n' +
                          '• Verifica tu conexión a internet\n' +
                          '• Comprueba que el servidor Laravel esté funcionando\n' +
                          '• Revisa si hay errores en el servidor (logs de Laravel)\n' +
                          '• Verifica la URL de la ruta en web.php');
                } else if (error.message.includes('CSRF')) {
                    errorLog('🔍 Diagnóstico: Error de CSRF');
                    alert('❌ Error de seguridad CSRF:\n\n' +
                          '• Recarga la página\n' +
                          '• Verifica que el token CSRF esté configurado correctamente');
                } else if (error.message.includes('404')) {
                    errorLog('🔍 Diagnóstico: Ruta no encontrada');
                    alert('❌ Error 404:\n\n' +
                          '• Verifica que la ruta POST /friendships exista en web.php\n' +
                          '• Comprueba el controlador FriendshipController');
                } else if (error.message.includes('500')) {
                    errorLog('🔍 Diagnóstico: Error del servidor');
                    alert('❌ Error del servidor:\n\n' +
                          '• Revisa los logs de Laravel\n' +
                          '• Verifica la base de datos\n' +
                          '• Comprueba el controlador');
                } else {
                    errorLog('🔍 Error desconocido:', error.message);
                    alert('❌ Error desconocido:\n\n' + error.message);
                }
            });
        });
    } else {
        errorLog('❌ Formulario new-friendship-form no encontrado');
    }
    
    // === FUNCIONES ADICIONALES (Ver detalles, editar, eliminar) ===
    
    // Ver detalles del emparejamiento
    window.viewFriendshipDetails = function(friendshipId) {
        debugLog('🔍 Viendo detalles del emparejamiento:', friendshipId);
        
        fetch(`/friendships/${friendshipId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            debugLog('📡 Respuesta de detalles:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            debugLog('✅ Datos del emparejamiento:', data);
            
            // Actualizar modal con datos
            const setTextContent = (id, value) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = value;
                } else {
                    debugLog(`⚠️ Elemento ${id} no encontrado`);
                }
            };
            
            setTextContent('view_friendship_id', data.friendship.id);
            setTextContent('view_start_date', data.friendship.start_date);
            setTextContent('view_end_date', data.friendship.end_date || 'N/A');
            setTextContent('view_notes', data.friendship.notes || 'Sin notas');
            
            // Actualizar badge de status
            const statusBadge = document.getElementById('view_status_badge');
            if (statusBadge) {
                statusBadge.textContent = data.friendship.status;
                statusBadge.classList.remove('bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800', 'bg-yellow-100', 'text-yellow-800');
                
                if (data.friendship.status === 'Emparejado') {
                    statusBadge.classList.add('bg-green-100', 'text-green-800');
                } else if (data.friendship.status === 'Inactivo') {
                    statusBadge.classList.add('bg-red-100', 'text-red-800');
                } else {
                    statusBadge.classList.add('bg-yellow-100', 'text-yellow-800');
                }
            }
            
            // Actualizar información del buddy
            setTextContent('view_buddy_name', `${data.buddy.first_name} ${data.buddy.last_name}`);
            setTextContent('view_buddy_disability', data.buddy.disability || 'N/A');
            setTextContent('view_buddy_age', `${data.buddy.age} años`);
            setTextContent('view_buddy_ci', data.buddy.ci);
            setTextContent('view_buddy_phone', data.buddy.phone);
            setTextContent('view_buddy_email', data.buddy.email || 'N/A');
            setTextContent('view_buddy_address', data.buddy.address);
            
            // Actualizar información del peerbuddy
            setTextContent('view_peerbuddy_name', `${data.peerBuddy.first_name} ${data.peerBuddy.last_name}`);
            setTextContent('view_peerbuddy_age', `${data.peerBuddy.age} años`);
            setTextContent('view_peerbuddy_ci', data.peerBuddy.ci);
            setTextContent('view_peerbuddy_phone', data.peerBuddy.phone);
            setTextContent('view_peerbuddy_email', data.peerBuddy.email || 'N/A');
            setTextContent('view_peerbuddy_address', data.peerBuddy.address);
            
            // Mostrar modal
            const viewModal = document.getElementById('view-friendship-modal');
            if (viewModal) {
                viewModal.classList.remove('hidden');
            } else {
                errorLog('Modal view-friendship-modal no encontrado');
            }
        })
        .catch(error => {
            errorLog('💥 Error al cargar detalles:', error);
            alert('Error al cargar los datos del emparejamiento');
        });
    };
    
    // Cerrar modal de ver emparejamiento
    window.closeViewFriendshipModal = function() {
        const modal = document.getElementById('view-friendship-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    };
    
    // === VERIFICACIÓN DE ELEMENTOS REQUERIDOS ===
    
    const requiredElements = [
        'new-friendship-modal',
        'new-friendship-form'
    ];
    
    const formFields = [
        'buddy_id',
        'peer_buddy_id',
        'start_date',
        'end_date',
        'status',
        'notes',
        'buddy_leader_id',
        'peer_buddy_leader_id'
    ];
    
    debugLog('🔍 Verificando elementos requeridos...');
    requiredElements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (!element) {
            errorLog(`❌ Elemento requerido no encontrado: ${elementId}`);
        } else {
            debugLog(`✅ Elemento encontrado: ${elementId}`);
        }
    });
    
    debugLog('🔍 Verificando campos del formulario...');
    formFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            debugLog(`✅ Campo encontrado: ${fieldId}`);
        } else {
            debugLog(`⚠️ Campo no encontrado: ${fieldId}`);
        }
    });
    
    // === VERIFICACIÓN DE CONFIGURACIÓN ===
    
    // Verificar CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        debugLog('✅ CSRF token encontrado');
    } else {
        errorLog('❌ CSRF token no encontrado - agrega <meta name="csrf-token" content="{{ csrf_token() }}"> en el head');
    }
    
    // Verificar conectividad básica
    debugLog('🌐 Verificando conectividad básica...');
    fetch('/csrf-token', { method: 'GET' })
        .then(response => {
            if (response.ok) {
                debugLog('✅ Conectividad básica OK');
            } else {
                debugLog('⚠️ Conectividad básica: respuesta no OK');
            }
        })
        .catch(error => {
            errorLog('❌ Sin conectividad básica:', error.message);
        });
    
    debugLog('🏁 Inicialización completada');
});
</script>
<!-- <script src="{{ asset('js/buddies.js') }}"></script> -->
 <!-- <script>
    // Función para ver detalles 
function viewBuddyDetails(buddyId) {
    
    fetch(`/buddies/${buddyId}/details`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                populateDetailsModal(data.buddy);
                document.getElementById('view-details-modal').classList.remove('hidden');
                document.body.classList.add('overflow-hidden'); // No scroll del fondo
            } else {
                alert('Error al cargar los detalles: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error fetching details:', error);
            alert('Error al cargar los detalles. Por favor revisa la consola para más información.');
        });
}



function populateDetailsModal(buddy) {
    console.log('Populating modal with buddy data:', buddy); // Debug log
    
    // Verificar que los elementos existen antes de usarlos
    const elements = {
        avatar: document.getElementById('detail-avatar'),
        name: document.getElementById('detail-name'),
        type: document.getElementById('detail-type'),
        ci: document.getElementById('detail-ci'),
        age: document.getElementById('detail-age'),
        phone: document.getElementById('detail-phone'),
        email: document.getElementById('detail-email'),
        createdAt: document.getElementById('detail-created-at'),
        updatedAt: document.getElementById('detail-updated-at'),
        disabilityInfo: document.getElementById('disability-info'),
        experienceInfo: document.getElementById('experience-info'),
        disability: document.getElementById('detail-disability'),
        experience: document.getElementById('detail-experience'),
        friendshipInfo: document.getElementById('friendship-info')
    };
    
    // Verificar elementos faltantes
    for (const [key, element] of Object.entries(elements)) {
        if (!element) {
            console.warn(`Element not found: detail-${key}`);
        }
    }
    
    // Información básica
    if (elements.avatar) {
        elements.avatar.src = `https://i.pravatar.cc/300?u=${buddy.email || buddy.id}`;
        elements.avatar.alt = buddy.full_name;
    }
    if (elements.name) elements.name.textContent = buddy.full_name || 'Sin nombre';
    if (elements.type) elements.type.textContent = buddy.type === 'buddy' ? 'Buddy' : 'PeerBuddy';
    
    // Información personal
    if (elements.ci) elements.ci.textContent = buddy.ci || 'No especificado';
    if (elements.age) elements.age.textContent = `${buddy.age || 0} años`;
    if (elements.phone) elements.phone.textContent = buddy.phone || 'No especificado';
    if (elements.email) elements.email.textContent = buddy.email || 'No especificado';
    
    // Información específica del tipo
    if (elements.disabilityInfo && elements.experienceInfo) {
        if (buddy.type === 'buddy') {
            elements.disabilityInfo.classList.remove('hidden');
            elements.experienceInfo.classList.add('hidden');
            if (elements.disability) elements.disability.textContent = buddy.disability || 'No especificado';
        } else {
            elements.disabilityInfo.classList.add('hidden');
            elements.experienceInfo.classList.remove('hidden');
            if (elements.experience) elements.experience.textContent = buddy.experience || 'No especificado';
        }
    }
    
    // Fechas
    if (elements.createdAt) elements.createdAt.textContent = formatDate(buddy.created_at);
    if (elements.updatedAt) elements.updatedAt.textContent = formatDate(buddy.updated_at);
    
    // Información de emparejamiento
    if (elements.friendshipInfo) {
        if (buddy.active_friendship) {
            const partnership = buddy.active_friendship;
            const partnerName = buddy.type === 'buddy' ? partnership.peer_buddy.full_name : partnership.buddy.full_name;
            const partnerType = buddy.type === 'buddy' ? 'PeerBuddy' : 'Buddy';
            
            elements.friendshipInfo.innerHTML = `
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Emparejado con: ${partnerName}</p>
                        <p class="text-xs text-gray-500">${partnerType}</p>
                        <p class="text-xs text-gray-500">Desde: ${formatDate(partnership.start_date)}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(partnership.status)}">
                        ${partnership.status}
                    </span>
                </div>
            `;
        } else {
            elements.friendshipInfo.innerHTML = `
                <p class="text-sm text-gray-600">Sin emparejamiento activo</p>
            `;
        }
    }
}

function closeViewDetailsModal() {
    document.getElementById('view-details-modal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Función para editar 
function editBuddy(buddyId) {
    fetch(`/buddies/${buddyId}/edit`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                populateEditModal(data.buddy);
                document.getElementById('edit-buddy-form').action = `/buddies/${buddyId}`;
                document.getElementById('edit-buddy-modal').classList.remove('hidden');
                document.body.classList.add('overflow-hidden'); // Previene scroll del fondo
            } else {
                alert('Error al cargar los datos para editar: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error fetching edit data:', error);
            alert('Error al cargar los datos para editar. Por favor revisa la consola para más información.');
        });
}

function populateEditModal(buddy) {
    const fields = {
        'edit-first-name': buddy.first_name || '',
        'edit-last-name': buddy.last_name || '',
        'edit-ci': buddy.ci || '',
        'edit-age': buddy.age || '',
        'edit-phone': buddy.phone || '',
        'edit-email': buddy.email || '',
        'edit-type': buddy.type || 'buddy'
    };
    
    // Llenar campos básicos
    for (const [fieldId, value] of Object.entries(fields)) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.value = value;
        } else {
            console.warn(`Field not found: ${fieldId}`);
        }
    }
    
    // Campos específicos del tipo
    if (buddy.type === 'buddy') {
        const disabilityField = document.getElementById('edit-disability');
        if (disabilityField) disabilityField.value = buddy.disability || '';
    } else {
        const experienceField = document.getElementById('edit-experience');
        if (experienceField) experienceField.value = buddy.experience || '';
    }
    
    // Mostrar campos correctos según el tipo
    toggleEditTypeFields();
}

function toggleEditTypeFields() {
    const typeField = document.getElementById('edit-type');
    if (!typeField) {
        console.warn('Type field not found');
        return;
    }
    
    const type = typeField.value;
    const buddyFields = document.getElementById('edit-buddy-fields');
    const peerBuddyFields = document.getElementById('edit-peer-buddy-fields');
    const disabilityField = document.getElementById('edit-disability');
    const experienceField = document.getElementById('edit-experience');
    
    if (type === 'buddy') {
        if (buddyFields) buddyFields.classList.remove('hidden');
        if (peerBuddyFields) peerBuddyFields.classList.add('hidden');
        if (disabilityField) disabilityField.required = true;
        if (experienceField) experienceField.required = false;
    } else {
        if (buddyFields) buddyFields.classList.add('hidden');
        if (peerBuddyFields) peerBuddyFields.classList.remove('hidden');
        if (disabilityField) disabilityField.required = false;
        if (experienceField) experienceField.required = true;
    }
}

function closeEditBuddyModal() {
    document.getElementById('edit-buddy-modal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function confirmDeleteBuddy(buddyId) {
    const form = document.getElementById('delete-buddy-form');
    const modal = document.getElementById('delete-buddy-modal');
    
    if (form) {
        form.action = '/buddies/' + buddyId;
    }
    if (modal) {
        modal.classList.remove('hidden');
    } else {
        // Si no hay modal, usar confirm nativo
        if (confirm('¿Está seguro que desea eliminar este buddy?')) {
            // Crear formulario temporal para eliminar
            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = `/buddies/${buddyId}`;
            
            // Agregar token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                tempForm.appendChild(csrfInput);
            }
            
            // Agregar método DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            tempForm.appendChild(methodInput);
            
            document.body.appendChild(tempForm);
            tempForm.submit();
        }
    }
}

function closeDeleteBuddyModal() {
    const modal = document.getElementById('delete-buddy-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function formatDate(dateString) {
    if (!dateString) return 'No especificado';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (error) {
        console.warn('Error formatting date:', dateString);
        return dateString;
    }
}

function getStatusColor(status) {
    const colors = {
        'Emparejado': 'bg-green-100 text-green-800',
        'Inactivo': 'bg-red-100 text-red-800',
        'Pendiente': 'bg-yellow-100 text-yellow-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Buddies.js loaded successfully'); // Debug log
    
    // Cerrar modales al hacer clic fuera de ellos
    ['delete-buddy-modal', 'view-details-modal', 'edit-buddy-modal'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        }
    });
    
    // Manejar envío del formulario de edición
    const editForm = document.getElementById('edit-buddy-form');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            // Agregar header para AJAX
            const headers = {
                'X-Requested-With': 'XMLHttpRequest'
            };
            
            // Agregar CSRF token si existe
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
            }
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: headers
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeEditBuddyModal();
                    // Mostrar mensaje de éxito
                    alert(data.message || 'Buddy actualizado exitosamente');
                    location.reload(); // Recargar la página para ver los cambios
                } else {
                    if (data.errors) {
                        let errorMessage = 'Errores de validación:\n';
                        for (const [field, errors] of Object.entries(data.errors)) {
                            errorMessage += `- ${field}: ${errors.join(', ')}\n`;
                        }
                        alert(errorMessage);
                    } else {
                        alert(data.message || 'Error al actualizar los datos');
                    }
                }
            })
            .catch(error => {
                console.error('Error updating buddy:', error);
                alert('Error al actualizar los datos. Revisa la consola para más información.');
            });
        });
    }
    
    // Event listener para el cambio de tipo en el modal de edición
    const editTypeField = document.getElementById('edit-type');
    if (editTypeField) {
        editTypeField.addEventListener('change', toggleEditTypeFields);
    }
});

window.viewBuddyDetails = viewBuddyDetails;
window.editBuddy = editBuddy;
window.confirmDeleteBuddy = confirmDeleteBuddy;
window.closeViewDetailsModal = closeViewDetailsModal;
window.closeEditBuddyModal = closeEditBuddyModal;
window.closeDeleteBuddyModal = closeDeleteBuddyModal;
window.toggleEditTypeFields = toggleEditTypeFields;
 </script> -->
@endsection