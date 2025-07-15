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
    console.log('Friendship system initialized');
    
    // === FUNCIONES PARA MODALES DE EMPAREJAMIENTO ===
    
    // Abrir modal de nuevo emparejamiento
    window.openNewFriendshipModal = function() {
        const modal = document.getElementById('new-friendship-modal');
        if (modal) {
            modal.classList.remove('hidden');
            console.log('Modal de nuevo emparejamiento abierto');
        } else {
            console.error('Modal new-friendship-modal no encontrado');
        }
    };
    
    // Cerrar modal de nuevo emparejamiento
    window.closeNewFriendshipModal = function() {
        const modal = document.getElementById('new-friendship-modal');
        if (modal) {
            modal.classList.add('hidden');
            console.log('Modal de nuevo emparejamiento cerrado');
        }
    };
    
    // Ver detalles del emparejamiento
    window.viewFriendshipDetails = function(friendshipId) {
        console.log('Viendo detalles del emparejamiento:', friendshipId);
        
        fetch(`/friendships/${friendshipId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos del emparejamiento recibidos:', data);
                
                // Actualizar información del emparejamiento
                const setTextContent = (id, value) => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.textContent = value;
                    } else {
                        console.warn(`Elemento ${id} no encontrado`);
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
                    // Limpiar clases anteriores
                    statusBadge.classList.remove('bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800', 'bg-yellow-100', 'text-yellow-800');
                    
                    // Aplicar clases según el status
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
                    console.error('Modal view-friendship-modal no encontrado');
                }
            })
            .catch(error => {
                console.error('Error al cargar los datos del emparejamiento:', error);
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
    
    // Abrir modal de editar emparejamiento
    window.openEditFriendshipModal = function(friendshipId, startDate, endDate, status, notes, buddyLeaderId, peerBuddyLeaderId) {
        const form = document.getElementById('edit-friendship-form');
        if (form) {
            form.action = `/friendships/${friendshipId}`;
        }
        
        const setFieldValue = (id, value) => {
            const field = document.getElementById(id);
            if (field) {
                field.value = value || '';
            } else {
                console.warn(`Campo ${id} no encontrado`);
            }
        };
        
        setFieldValue('edit_start_date', startDate);
        setFieldValue('edit_end_date', endDate);
        setFieldValue('edit_status', status);
        setFieldValue('edit_notes', notes);
        setFieldValue('edit_buddy_leader_id', buddyLeaderId);
        setFieldValue('edit_peer_buddy_leader_id', peerBuddyLeaderId);
        
        const editModal = document.getElementById('edit-friendship-modal');
        if (editModal) {
            editModal.classList.remove('hidden');
        } else {
            console.error('Modal edit-friendship-modal no encontrado');
        }
    };
    
    // Cerrar modal de editar emparejamiento
    window.closeEditFriendshipModal = function() {
        const modal = document.getElementById('edit-friendship-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    };
    
    // Confirmar eliminación de emparejamiento
    window.confirmDelete = function(friendshipId) {
        const form = document.getElementById('delete-friendship-form');
        if (form) {
            form.action = `/friendships/${friendshipId}`;
        }
        
        const deleteModal = document.getElementById('delete-confirmation-modal');
        if (deleteModal) {
            deleteModal.classList.remove('hidden');
        } else {
            console.error('Modal delete-confirmation-modal no encontrado');
        }
    };
    
    // Cerrar modal de eliminar
    window.closeDeleteModal = function() {
        const modal = document.getElementById('delete-confirmation-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    };
    
    // === VALIDACIÓN DEL FORMULARIO DE NUEVO EMPAREJAMIENTO ===
    
    const newFriendshipForm = document.getElementById('new-friendship-form');
    if (newFriendshipForm) {
        newFriendshipForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevenir envío normal
            console.log('📝 Formulario de nuevo emparejamiento enviado');
            
            const buddyId = document.getElementById('buddy_id')?.value;
            const peerBuddyId = document.getElementById('peer_buddy_id')?.value;
            
            console.log('👤 Buddy ID:', buddyId);
            console.log('🤝 PeerBuddy ID:', peerBuddyId);
            
            // Validar que ambos campos estén seleccionados
            if (!buddyId || !peerBuddyId) {
                alert('Por favor selecciona un Buddy y un PeerBuddy');
                return false;
            }
            
            // Validar que no sean el mismo
            if (buddyId === peerBuddyId) {
                alert('No puedes emparejar una persona consigo misma');
                return false;
            }
            
            // Validar fecha de inicio
            const startDate = document.getElementById('start_date')?.value;
            if (!startDate) {
                alert('Por favor selecciona una fecha de inicio');
                return false;
            }
            
            // Recopilar todos los datos del formulario
            const formData = new FormData(newFriendshipForm);
            const formObject = {};
            
            // Convertir FormData a objeto para logging
            for (let [key, value] of formData.entries()) {
                formObject[key] = value;
            }
            
            console.log('📋 Datos del formulario:', formObject);
            
            // Obtener CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('❌ CSRF token no encontrado');
                alert('Error: Token CSRF no encontrado');
                return false;
            }
            
            console.log('🔐 CSRF Token encontrado:', csrfToken.getAttribute('content').substring(0, 10) + '...');
            
            // Configurar headers
            const headers = {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };
            
            console.log('🚀 Enviando solicitud a:', newFriendshipForm.action);
            console.log('📡 Headers:', headers);
            
            // Enviar con fetch
            fetch(newFriendshipForm.action, {
                method: 'POST',
                body: formData,
                headers: headers
            })
            .then(response => {
                console.log('📥 Respuesta recibida. Status:', response.status);
                console.log('📊 Response OK:', response.ok);
                
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('❌ Error en respuesta:', text);
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    });
                }
                
                const contentType = response.headers.get('content-type');
                console.log('📄 Content-Type:', contentType);
                
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    return response.text();
                }
            })
            .then(data => {
                console.log('✅ Respuesta exitosa:', data);
                
                // Cerrar modal
                window.closeNewFriendshipModal();
                
                // Mostrar mensaje de éxito
                if (typeof data === 'object' && data.message) {
                    alert(data.message);
                } else {
                    alert('Emparejamiento creado exitosamente');
                }
                
                // Recargar página o actualizar contenido
                location.reload();
            })
            .catch(error => {
                console.error('💥 Error en la solicitud:', error);
                console.error('❌ Error:', 'Error al crear el emparejamiento');
                console.error('📋 Detalles:', error.message);
                
                // Mostrar error más específico
                if (error.message.includes('Failed to fetch') || error.message.includes('Load failed')) {
                    alert('Error de conexión: Verifica tu conexión a internet y que el servidor esté funcionando');
                } else if (error.message.includes('404')) {
                    alert('Error 404: La ruta no existe. Verifica la URL en el formulario');
                } else if (error.message.includes('422')) {
                    alert('Error de validación: Algunos datos no son válidos');
                } else if (error.message.includes('500')) {
                    alert('Error del servidor: Revisa los logs del servidor');
                } else {
                    alert('Error al crear el emparejamiento: ' + error.message);
                }
            });
        });
    } else {
        console.error('❌ Formulario new-friendship-form no encontrado');
    }
    
    // === FUNCIONES PARA TABS ===
    
    const switchToTab = function(targetSectionId) {
        console.log('Cambiando a tab:', targetSectionId);
        
        const allSections = ['friendships-section', 'activities-section', 'buddies-section'];
        
        // Ocultar todas las secciones
        allSections.forEach(sectionId => {
            const section = document.getElementById(sectionId);
            if (section) {
                section.classList.add('hidden');
            }
        });
        
        // Mostrar la sección objetivo
        const targetSection = document.getElementById(targetSectionId);
        if (targetSection) {
            targetSection.classList.remove('hidden');
            console.log('Sección mostrada:', targetSectionId);
        } else {
            console.error('Sección no encontrada:', targetSectionId);
        }
        
        // Actualizar estilos de tabs
        document.querySelectorAll('.tab-link').forEach(tab => {
            tab.classList.remove('border-indigo-500', 'text-gray-900');
            tab.classList.add('border-transparent', 'text-gray-500');
        });
        
        const activeTab = document.querySelector(`[data-target="${targetSectionId}"]`);
        if (activeTab) {
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.add('border-indigo-500', 'text-gray-900');
        }
    };
    
    // Event listeners para tabs
    document.querySelectorAll('.tab-link').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            console.log('Tab clickeado, objetivo:', targetId);
            switchToTab(targetId);
        });
    });
    
    // Activar tab inicial
    setTimeout(() => {
        switchToTab('friendships-section');
    }, 100);
    
    // === FUNCIONES PARA CERRAR MODALES AL HACER CLICK FUERA ===
    
    // Cerrar modales al hacer click fuera de ellos
    const modalIds = [
        'new-friendship-modal',
        'view-friendship-modal', 
        'edit-friendship-modal',
        'delete-confirmation-modal'
    ];
    
    modalIds.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        }
    });
    
    // === DEBUGGING Y VERIFICACIÓN ===
    
    // Verificar que todos los elementos necesarios existan
    const requiredElements = [
        'new-friendship-modal',
        'new-friendship-form',
        'buddy_id',
        'peer_buddy_id',
        'start_date'
    ];
    
    const optionalElements = [
        'buddy_leader_id',
        'peer_buddy_leader_id', 
        'notes',
        'status',
        'end_date'
    ];
    
    requiredElements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (!element) {
            console.error(`Elemento requerido no encontrado: ${elementId}`);
        } else {
            console.log(`Elemento encontrado: ${elementId}`);
        }
    });
    
    optionalElements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            console.log(`Elemento opcional encontrado: ${elementId}`);
        } else {
            console.warn(`Elemento opcional no encontrado: ${elementId}`);
        }
    });
    
    console.log('Inicialización de emparejamientos completada');
    
    // === FUNCIONES DE DEBUGGING ADICIONALES ===
    
    // Función para mostrar mensajes de error más detallados
    window.showErrorMessage = function(message, details = null) {
        console.error('❌ Error:', message);
        if (details) {
            console.error('📋 Detalles:', details);
        }
    };
    
    // Verificar configuración del servidor
    window.checkServerConfig = function() {
        console.log('🔍 Verificando configuración...');
        console.log('🌐 URL actual:', window.location.href);
        console.log('🔗 Base URL:', window.location.origin);
        
        const form = document.getElementById('new-friendship-form');
        if (form) {
            console.log('📝 Action del formulario:', form.action);
            console.log('🔧 Método del formulario:', form.method);
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            console.log('🔐 CSRF Token presente:', csrfToken.getAttribute('content').substring(0, 20) + '...');
        } else {
            console.error('❌ CSRF Token no encontrado en el HEAD');
        }
    };
    
    // Ejecutar verificación automática
    window.checkServerConfig();
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