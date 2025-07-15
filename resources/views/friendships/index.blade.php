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

<!-- Create Friendship Modal -->
<div id="new-friendship-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-medium text-gray-900">Nuevo Emparejamiento</h2>
            <button type="button" onclick="closeNewFriendshipModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="new-friendship-form" method="POST" action="{{ route('friendships.store') }}">
            @csrf
            
            <!-- Sección de Participantes -->
            <div class="mb-6">
                <h3 class="text-md font-medium text-gray-800 mb-4 border-b pb-2">Participantes del Emparejamiento</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Buddy Selection -->
                    <div>
                        <label for="buddy_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Buddy <span class="text-red-500">*</span>
                        </label>
                        <select id="buddy_id" name="buddy_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Seleccionar Buddy</option>
                            @foreach($availableBuddies as $buddy)
                                <option value="{{ $buddy->id }}">{{ $buddy->full_name }} ({{ $buddy->disability }})</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Persona con discapacidad</p>
                    </div>
                    
                    <!-- PeerBuddy Selection -->
                    <div>
                        <label for="peer_buddy_id" class="block text-sm font-medium text-gray-700 mb-2">
                            PeerBuddy <span class="text-red-500">*</span>
                        </label>
                        <select id="peer_buddy_id" name="peer_buddy_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Seleccionar PeerBuddy</option>
                            @foreach($availablePeerBuddies as $peerBuddy)
                                <option value="{{ $peerBuddy->id }}">{{ $peerBuddy->full_name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Persona sin discapacidad</p>
                    </div>
                </div>
            </div>

            <!-- Sección de Líderes -->
            <div class="mb-6">
                <h3 class="text-md font-medium text-gray-800 mb-4 border-b pb-2">Líderes Asignados</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Buddy Leader Selection - CORREGIDO -->
                    <div>
                        <label for="buddy_leader_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Líder de Buddy <span class="text-red-500">*</span>
                        </label>
                        <select id="buddy_leader_id" name="buddy_leader_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Seleccionar Líder de Buddy</option>
                            @if(isset($availableBuddyLeaders) && count($availableBuddyLeaders) > 0)
                                @foreach($availableBuddyLeaders as $buddyLeader)
                                    <option value="{{ $buddyLeader->id }}">{{ $buddyLeader->name }}</option>
                                @endforeach
                            @else
                                <option value="" disabled>No hay líderes de buddy disponibles</option>
                            @endif
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Responsable de manejar la comunicación con todos los buddies y evaluar su experiencia en el programa</p>
                    </div>
                    
                    <!-- PeerBuddy Leader Selection - CORREGIDO -->
                    <div>
                        <label for="peer_buddy_leader_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Líder de PeerBuddy <span class="text-red-500">*</span>
                        </label>
                        <select id="peer_buddy_leader_id" name="peer_buddy_leader_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Seleccionar Líder de PeerBuddy</option>
                            @if(isset($availablePeerBuddyLeaders) && count($availablePeerBuddyLeaders) > 0)
                                @foreach($availablePeerBuddyLeaders as $peerBuddyLeader)
                                    <option value="{{ $peerBuddyLeader->id }}">{{ $peerBuddyLeader->name }}</option>
                                @endforeach
                            @else
                                <option value="" disabled>No hay líderes de peer buddy disponibles</option>
                            @endif
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Responsable de coordinación y control de cumplimiento de las actividades que realizan los peerbuddies</p>
                    </div>
                </div>
            </div>

            <!-- Sección de Detalles del Emparejamiento -->
            <div class="mb-6">
                <h3 class="text-md font-medium text-gray-800 mb-4 border-b pb-2">Detalles del Emparejamiento</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Inicio <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="start_date" name="start_date" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="Emparejado">Emparejado</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notas</label>
                <textarea id="notes" name="notes" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Observaciones adicionales sobre el emparejamiento..."></textarea>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeNewFriendshipModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Crear Emparejamiento
                </button>
            </div>
        </form>
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
<!-- @include('modals.friendships.create') -->

@endsection
<!-- Include the fixed JS file -->
<!-- <script src="{{ asset('js/friendships.js') }}"></script> -->

<!-- <script src="{{ asset('js/buddies.js') }}"></script> -->


<script>
let activityCalendar;

document.addEventListener('DOMContentLoaded', function() {
    // ===== INICIALIZACIÓN DEL CALENDARIO DE ACTIVIDADES =====
    if (document.getElementById('calendar-grid')) {
        activityCalendar = new ActivityCalendar();
        
        // Exponer funciones del calendario al ámbito global
        window.openNewActivityModal = () => activityCalendar.openNewActivityModal();
        window.closeNewActivityModal = () => activityCalendar.closeNewActivityModal();
        window.editActivity = (id) => activityCalendar.editActivity(id);
        window.managePhotos = (id) => activityCalendar.managePhotos(id);
        window.cancelActivity = (id) => activityCalendar.cancelActivity(id);
        window.deleteActivity = (id) => activityCalendar.deleteActivity(id); 
        window.closeEditActivityModal = () => activityCalendar.closeEditActivityModal();
        window.viewActivityDetails = (id) => viewActivityDetails(id);
        window.closeActivityDetailsModal = () => closeActivityDetailsModal();
        window.showImageModal = (url) => showImageModal(url);
        window.closeImageModal = () => closeImageModal();
        
        // Manejar notificaciones del calendario
        const notification = activityCalendar.getCookie('notification');
        if (notification) {
            try {
                const {message, type} = JSON.parse(notification);
                activityCalendar.displayNotification(message, type);
                activityCalendar.setCookie('notification', '', -1);
            } catch (e) {
                console.error('Error parsing notification cookie:', e);
            }
        }
    }

    // ===== FUNCIONES DE BUDDIES =====
    window.viewBuddyDetails = viewBuddyDetails;
    window.editBuddy = editBuddy;
    window.confirmDeleteBuddy = confirmDeleteBuddy;
    window.closeViewDetailsModal = closeViewDetailsModal;
    window.closeEditBuddyModal = closeEditBuddyModal;
    window.closeDeleteBuddyModal = closeDeleteBuddyModal;
    window.toggleEditTypeFields = toggleEditTypeFields;

    // ===== FUNCIONES DE FRIENDSHIPS =====
    window.openNewFriendshipModal = function() {
        document.getElementById('new-friendship-modal').classList.remove('hidden');
    };

    window.closeNewFriendshipModal = function() {
        document.getElementById('new-friendship-modal').classList.add('hidden');
    };

    window.viewFriendshipDetails = function(friendshipId) {
        fetch(`/friendships/${friendshipId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar los datos del emparejamiento');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('view_friendship_id').textContent = data.friendship.id;
                document.getElementById('view_start_date').textContent = data.friendship.start_date;
                document.getElementById('view_end_date').textContent = data.friendship.end_date || 'N/A';
                document.getElementById('view_notes').textContent = data.friendship.notes || 'Sin notas';
                
                const statusBadge = document.getElementById('view_status_badge');
                statusBadge.textContent = data.friendship.status;
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

                document.getElementById('view_buddy_name').textContent = data.buddy.first_name + ' ' + data.buddy.last_name;
                document.getElementById('view_buddy_disability').textContent = data.buddy.disability || 'N/A';
                document.getElementById('view_buddy_age').textContent = `${data.buddy.age} años`;
                document.getElementById('view_buddy_ci').textContent = data.buddy.ci;
                document.getElementById('view_buddy_phone').textContent = data.buddy.phone;
                document.getElementById('view_buddy_email').textContent = data.buddy.email || 'N/A';
                document.getElementById('view_buddy_address').textContent = data.buddy.address;
               
                document.getElementById('view_peerbuddy_name').textContent = data.peerBuddy.first_name + ' ' + data.peerBuddy.last_name;
                document.getElementById('view_peerbuddy_age').textContent = `${data.peerBuddy.age} años`;
                document.getElementById('view_peerbuddy_ci').textContent = data.peerBuddy.ci;
                document.getElementById('view_peerbuddy_phone').textContent = data.peerBuddy.phone;
                document.getElementById('view_peerbuddy_email').textContent = data.peerBuddy.email || 'N/A';
                document.getElementById('view_peerbuddy_address').textContent = data.peerBuddy.address;
                
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
   
    window.confirmDelete = function(friendshipId) {
        const form = document.getElementById('delete-friendship-form');
        form.action = `/friendships/${friendshipId}`;
        document.getElementById('delete-confirmation-modal').classList.remove('hidden');
    };
   
    window.closeDeleteModal = function() {
        document.getElementById('delete-confirmation-modal').classList.add('hidden');
    };

    window.confirmDeleteBuddy = function(buddyId) {
        const form = document.getElementById('delete-buddy-form');
        form.action = `/buddies/${buddyId}`;
        document.getElementById('delete-buddy-modal').classList.remove('hidden');
    };
   
    window.closeDeleteBuddyModal = function() {
        document.getElementById('delete-buddy-modal').classList.add('hidden');
    };

    // ===== MANEJO DE TABS =====
    const switchToTab = function(targetSectionId) {
        const allSections = ['friendships-section', 'activities-section', 'buddies-section'];
       
        allSections.forEach(sectionId => {
            const section = document.getElementById(sectionId);
            if (section) {
                section.classList.add('hidden');
                console.log('Hiding section:', sectionId);
            }
        });

        const targetSection = document.getElementById(targetSectionId);
        if (targetSection) {
            targetSection.classList.remove('hidden');
        } else {
            console.error('Section not found:', targetSectionId);
        }

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

    document.querySelectorAll('.tab-link').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            switchToTab(targetId);
        });
    });

    setTimeout(() => {
        switchToTab('friendships-section');
    }, 100);

    // ===== VALIDACIÓN DE FORMULARIO DE FRIENDSHIP =====
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

    // ===== MANEJO DE MODALES PARA BUDDIES =====
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

    // ===== MANEJO DEL FORMULARIO DE EDICIÓN DE BUDDIES =====
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

    const editTypeField = document.getElementById('edit-type');
    if (editTypeField) {
        editTypeField.addEventListener('change', toggleEditTypeFields);
    }
});


// ===== FUNCIONES DE BUDDIES =====
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
                document.body.classList.add('overflow-hidden');
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
    console.log('Populating modal with buddy data:', buddy);
    
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
                document.body.classList.add('overflow-hidden');
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


// ===== FUNCIONES PARA DETALLES DE ACTIVIDADES =====
async function viewActivityDetails(activityId) {
    try {
        if (typeof showLoading === 'function') {
            showLoading();
        }
        const response = await fetch(`/activities/${activityId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (!response.ok) {
            throw new Error('Error al cargar los detalles de la actividad');
        }
        
        const activity = await response.json();
        showActivityDetailsModal(activity);
        
    } catch (error) {
        if (typeof showAlert === 'function') {
            showAlert('Error al cargar los detalles de la actividad', 'error');
        } else {
            alert('Error al cargar los detalles de la actividad');
        }
    } finally {
        if (typeof hideLoading === 'function') {
            hideLoading();
        }
    }
}

function showActivityDetailsModal(activity) {
    const typeLabels = {
        'recreational': 'Recreativa',
        'educational': 'Educativa',
        'cultural': 'Cultural',
        'sports': 'Deportiva',
        'social': 'Social'
    };
    
    const statusLabels = {
        'scheduled': 'Programada',
        'in_progress': 'En progreso',
        'completed': 'Completada',
        'cancelled': 'Cancelada'
    };
    
    const statusColors = {
        'scheduled': 'bg-blue-100 text-blue-800',
        'in_progress': 'bg-yellow-100 text-yellow-800',
        'completed': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800'
    };

    const formattedDate = new Date(activity.date).toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    const formattedStartTime = activity.start_time ? 
        new Date(`2000-01-01 ${activity.start_time}`).toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit'
        }) : '';
    
    const formattedEndTime = activity.end_time ? 
        new Date(`2000-01-01 ${activity.end_time}`).toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit'
        }) : '';
    
    const modalHtml = `
        <div id="activity-details-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between pb-4 border-b">
                        <h3 class="text-lg font-medium text-gray-900">Detalles de la Actividad</h3>
                        <button onclick="closeActivityDetailsModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="mt-4 space-y-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold text-gray-900">${activity.title}</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-2 ${statusColors[activity.status] || 'bg-gray-100 text-gray-800'}">
                                    ${statusLabels[activity.status] || activity.status}
                                </span>
                            </div>
                        </div>
                        
                        ${activity.description ? `
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-1">Descripción</h5>
                                <p class="text-gray-600">${activity.description}</p>
                            </div>
                        ` : ''}
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-1">Fecha</h5>
                                <p class="text-gray-600 capitalize">${formattedDate}</p>
                            </div>
                            
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-1">Horario</h5>
                                <p class="text-gray-600">
                                    ${formattedStartTime}${formattedEndTime ? ` - ${formattedEndTime}` : ''}
                                </p>
                            </div>
                            
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-1">Ubicación</h5>
                                <p class="text-gray-600">${activity.location}</p>
                            </div>
                            
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-1">Tipo</h5>
                                <p class="text-gray-600">${typeLabels[activity.type] || activity.type}</p>
                            </div>
                        </div>
                        
                        ${activity.photo_urls && activity.photo_urls.length > 0 ? `
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-2">Fotos</h5>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    ${activity.photo_urls.map(photo => `
                                        <img src="${photo.url}" alt="Foto de actividad" 
                                             class="w-full h-24 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity"
                                             onclick="showImageModal('${photo.url}')">
                                    `).join('')}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                    
                    <div class="flex items-center justify-between pt-4 mt-6 border-t">
                        <button onclick="closeActivityDetailsModal()" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function closeActivityDetailsModal() {
    const modal = document.getElementById('activity-details-modal');
    if (modal) {
        modal.remove();
    }
}

function showImageModal(imageUrl) {
    const imageModalHtml = `
        <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-60" onclick="closeImageModal()">
            <div class="max-w-4xl max-h-full p-4">
                <img src="${imageUrl}" alt="Imagen ampliada" class="max-w-full max-h-full object-contain">
                <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', imageModalHtml);
}

function closeImageModal() {
    const modal = document.getElementById('image-modal');
    if (modal) {
        modal.remove();
    }
}

// ===== CLASE ACTIVITYCALENDAR =====
class ActivityCalendar {
    constructor() {
        this.currentMonth = new Date().getMonth();
        this.currentYear = new Date().getFullYear();
        this.selectedDate = null;
        this.activities = {};
        this.initPhotoIntegration();
        
        this.init();
        window.activityCalendar = this;
    }
    
    init() {
        this.setupEventListeners();
        this.loadCalendar();
        this.loadActivities();
        this.loadUpcomingActivities();
    }
    
    setupEventListeners() {
        document.getElementById('prev-month')?.addEventListener('click', () => {
            this.currentMonth--;
            if (this.currentMonth < 0) {
                this.currentMonth = 11;
                this.currentYear--;
            }
            this.loadCalendar();
            this.loadActivities();
            this.notifySidebarMonthChanged();
        });
        
        document.getElementById('next-month')?.addEventListener('click', () => {
            this.currentMonth++;
            if (this.currentMonth > 11) {
                this.currentMonth = 0;
                this.currentYear++;
            }
            this.loadCalendar();
            this.loadActivities();
            this.notifySidebarMonthChanged();
        });
        
        const newActivityForm = document.getElementById('new-activity-form');
        if (newActivityForm) {
            newActivityForm.addEventListener('submit', (e) => this.handleCreateActivity(e));
        }
        
        const editActivityForm = document.getElementById('edit-activity-form');
        if (editActivityForm) {
            editActivityForm.addEventListener('submit', (e) => this.handleEditActivity(e));
        }

        document.getElementById('quick-add-activity')?.addEventListener('click', () => {
            if (this.selectedDate) {
                document.getElementById('activity_date').value = this.selectedDate;
            }
            this.openNewActivityModal();
        });  
    }

    notifySidebarMonthChanged() {
        document.dispatchEvent(new CustomEvent('calendar-month-changed', {
            detail: {
                month: this.currentMonth + 1,
                year: this.currentYear
            }
        }));
        if (window.sidebarInstance && typeof window.sidebarInstance.loadActivities === 'function') {
            window.sidebarInstance.loadActivities();
        }
    }
    
    loadCalendar() {
        const monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        
        document.getElementById('current-month').textContent = 
            `${monthNames[this.currentMonth]} ${this.currentYear}`;
        
        const firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
        const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
        const daysInPrevMonth = new Date(this.currentYear, this.currentMonth, 0).getDate();
        
        const calendarGrid = document.getElementById('calendar-grid');
        calendarGrid.innerHTML = '';

        for (let i = firstDay - 1; i >= 0; i--) {
            const dayElement = this.createDayElement(daysInPrevMonth - i, true);
            calendarGrid.appendChild(dayElement);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = this.createDayElement(day, false);
            calendarGrid.appendChild(dayElement);
        }

        const totalCells = calendarGrid.children.length;
        const remainingCells = 42 - totalCells;
        for (let day = 1; day <= remainingCells; day++) {
            const dayElement = this.createDayElement(day, true);
            calendarGrid.appendChild(dayElement);
        }
    }
    
    createDayElement(day, isOtherMonth) {
        const dayElement = document.createElement('div');
        dayElement.className = `p-2 text-center cursor-pointer rounded hover:bg-gray-100 ${
            isOtherMonth ? 'text-gray-400' : 'text-gray-900'
        }`;
        dayElement.textContent = day;
        
        if (!isOtherMonth) {
            const dateStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            dayElement.setAttribute('data-date', dateStr);
            const today = new Date();
            if (this.currentYear === today.getFullYear() && 
                this.currentMonth === today.getMonth() && 
                day === today.getDate()) {
                dayElement.classList.add('bg-blue-100', 'font-semibold');
            }
            dayElement.addEventListener('click', () => this.selectDate(dateStr, dayElement));
        }
        
        return dayElement;
    }
    
    selectDate(dateStr, dayElement) {
        document.querySelectorAll('[data-date]').forEach(el => {
            el.classList.remove('bg-blue-500', 'text-white');
        });
        dayElement.classList.add('bg-blue-500', 'text-white');
        this.selectedDate = dateStr;
        
        this.loadDayActivities(dateStr);
    }

    loadActivities(filters = {}) {
        let url = `/api/activities/calendar?month=${this.currentMonth + 1}&year=${this.currentYear}`;
        
        if (filters.type) {
            url += `&type=${filters.type}`;
        }
        if (filters.status) {
            url += `&status=${filters.status}`;
        }
        if (filters.dateStart) {
            url += `&dateStart=${filters.dateStart}`;
        }
        if (filters.dateEnd) {
            url += `&dateEnd=${filters.dateEnd}`;
        }
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                this.activities = data;
                this.markActivityDays();
            })
            .catch(error => console.error('Error loading activities:', error));
    }
    
    markActivityDays() {
        document.querySelectorAll('[data-date]').forEach(el => {
            el.classList.remove('has-activities');
            const existingDot = el.querySelector('.activity-dot');
            if (existingDot) existingDot.remove();
        });

        const typeColors = {
            'recreational': 'bg-blue-500',
            'educational': 'bg-green-500', 
            'cultural': 'bg-purple-500',
            'sports': 'bg-orange-500',
            'social': 'bg-pink-500'
        };
        
        Object.keys(this.activities).forEach(day => {
            const dateStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const dayElement = document.querySelector(`[data-date="${dateStr}"]`);
            if (dayElement) {
                dayElement.classList.add('has-activities');
                
                const dayData = this.activities[day];
                const types = dayData.types || [];
                const dotContainer = document.createElement('div');
                dotContainer.className = 'flex justify-center space-x-1 mt-1';
                types.forEach(type => {
                    const dot = document.createElement('div');
                    dot.className = `activity-dot w-2 h-2 ${typeColors[type] || 'bg-gray-500'} rounded-full`;
                    dotContainer.appendChild(dot);
                });
                
                dayElement.appendChild(dotContainer);
            }
        });
    }
    
    loadDayActivities(dateStr) {
        const [year, month, day] = dateStr.split('-');
        const formattedDate = `${parseInt(day)} ${this.getMonthName(parseInt(month) - 1)}`;
        
        document.getElementById('selected-date').textContent = formattedDate;
        
        fetch(`/api/activities/by-date?date=${dateStr}`)
            .then(response => response.json())
            .then(activities => {
                const container = document.getElementById('day-activities');
                container.innerHTML = '';
                
                if (activities.length === 0) {
                    container.innerHTML = '<div class="text-gray-500 text-sm">No hay actividades para esta fecha</div>';
                    return;
                }
                
                activities.forEach(activity => {
                    const activityElement = this.createActivityElement(activity);
                    container.appendChild(activityElement);
                });
            })
            .catch(error => {
                console.error('Error loading day activities:', error);
                document.getElementById('day-activities').innerHTML = 
                    '<div class="text-red-500 text-sm">Error al cargar actividades</div>';
            });
    }
    
    createActivityElement(activity) {
        const typeColors = {
            'recreational': 'border-green-500',
            'educational': 'border-blue-500',
            'cultural': 'border-purple-500',
            'sports': 'border-orange-500',
            'social': 'border-pink-500'
        };
        
        const borderColor = typeColors[activity.type] || 'border-gray-500';
        const div = document.createElement('div');
        div.className = `pl-4 border-l-4 ${borderColor}`;
        
        div.innerHTML = `
            <div class="text-sm font-medium">${activity.title}</div>
            <div class="text-xs text-gray-500">${activity.location}</div>
            <div class="text-xs text-gray-400">${activity.start_time}${activity.end_time ? ' - ' + activity.end_time : ''}</div>
            <div class="mt-2 flex flex-wrap gap-1">
                <button onclick="viewActivityDetails(${activity.id})"
                    class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded hover:bg-gray-200 transition-colors">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Ver detalles
                </button>
                <button onclick="editActivity(${activity.id})"
                    class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded hover:bg-blue-200 transition-colors">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </button>
                <button onclick="managePhotos(${activity.id})"
                    class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded hover:bg-green-200 transition-colors">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Fotos
                </button>
            </div>
        `;
        
        return div;
    }

    getMonthName(monthIndex) {
        const months = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        return months[monthIndex];
    }
    
    handleCreateActivity(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const submitButton = e.target.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.textContent = 'Guardando...';
    
    fetch('/activities', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                           document.querySelector('input[name="_token"]')?.value
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            this.showNotification('Actividad creada exitosamente', 'success');
            this.closeNewActivityModal();
            this.loadActivities();
            this.loadUpcomingActivities();
            document.dispatchEvent(new CustomEvent('activity-created', {
                detail: data.activity
            }));
            if (data.activity.date) {
                this.selectDateFromString(data.activity.date);
            }
        } else {
            throw new Error(data.message || 'Error al crear la actividad');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        this.showNotification('Error al crear la actividad: ' + error.message, 'error');
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.textContent = 'Guardar Actividad';
    });
}
    
    handleEditActivity(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const activityId = document.getElementById('edit_activity_id').value;
        const submitButton = e.target.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Actualizando...';
        
        fetch(`/activities/${activityId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                               document.querySelector('input[name="_token"]')?.value
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('Actividad actualizada exitosamente', 'success');
                this.closeEditActivityModal();
                this.loadActivities();
                this.loadUpcomingActivities();
                if (this.selectedDate) {
                    this.loadDayActivities(this.selectedDate);
                }
            } else {
                throw new Error(data.message || 'Error al actualizar la actividad');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Error al actualizar la actividad: ' + error.message, 'error');
        })
        .finally(() => {
            // Restaurar botón
            submitButton.disabled = false;
            submitButton.textContent = 'Actualizar Actividad';
        });
    }
    
    selectDateFromString(dateStr) {
        const date = new Date(dateStr);
        if (date.getFullYear() === this.currentYear && date.getMonth() === this.currentMonth) {
            const dayElement = document.querySelector(`[data-date="${dateStr}"]`);
            if (dayElement) {
                this.selectDate(dateStr, dayElement);
            }
        }
    }
    
    loadUpcomingActivities() {
        const container = document.getElementById('upcoming-activities');
        if (!container) return;
        fetch('/api/activities/upcoming?include_cancelled=true')
            .then(response => response.json())
            .then(activities => {
                container.innerHTML = '';
                
                if (activities.length === 0) {
                    container.innerHTML = `
                        <div class="text-gray-500 text-center py-8">
                            No hay actividades programadas
                        </div>
                    `;
                    return;
                }
                const activeActivities = activities.filter(a => a.status !== 'cancelled');
                const cancelledActivities = activities.filter(a => a.status === 'cancelled');
                activeActivities.forEach(activity => {
                    const activityCard = this.createUpcomingActivityCard(activity);
                    container.appendChild(activityCard);
                });
                if (cancelledActivities.length > 0) {
                    const separator = document.createElement('div');
                    separator.className = 'border-t border-red-200 my-4 pt-4';
                    separator.innerHTML = '<h5 class="text-sm font-medium text-red-600 mb-3">Actividades Canceladas</h5>';
                    container.appendChild(separator);
                    
                    cancelledActivities.forEach(activity => {
                        const activityCard = this.createUpcomingActivityCard(activity);
                        container.appendChild(activityCard);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading upcoming activities:', error);
                container.innerHTML = `
                    <div class="text-red-500 text-center py-4">
                        Error al cargar actividades próximas
                    </div>
                `;
            });
    }
    
    createUpcomingActivityCard(activity) {
        const div = document.createElement('div');
        const isCancelled = activity.status === 'cancelled';
        const baseClasses = 'border rounded-lg p-4 activity-card';
        const cancelledClasses = isCancelled ? 'border-red-300 bg-red-50' : '';
        
        div.className = `${baseClasses} ${cancelledClasses}`;
        div.setAttribute('data-activity-id', activity.id);
        
        const statusColors = {
            'scheduled': 'green',
            'in_progress': 'yellow', 
            'completed': 'blue',
            'cancelled': 'red'
        };
        
        const statusLabels = {
            'scheduled': 'Programada',
            'in_progress': 'En Progreso',
            'completed': 'Completada', 
            'cancelled': 'Cancelada'
        };
        
        const statusColor = statusColors[activity.status] || 'gray';
        const statusLabel = statusLabels[activity.status] || this.capitalizeFirst(activity.status);
        
        div.innerHTML = `
            <div class="flex items-start justify-between">
                <h4 class="font-medium ${isCancelled ? 'text-red-600 line-through' : ''}">${activity.title}</h4>
                ${isCancelled ? '<span class="text-red-500 text-xs font-semibold">CANCELADA</span>' : ''}
            </div>
            <div class="flex items-center text-sm text-gray-500 mt-1">
                <span class="status-badge bg-${statusColor}-100 text-${statusColor}-800 text-xs px-2 py-1 rounded-full mr-2">
                    ${statusLabel}
                </span>
                <span class="${isCancelled ? 'text-red-500' : ''}">${activity.formatted_date} - ${activity.formatted_time}</span>
            </div>
            <div class="mt-2 text-sm ${isCancelled ? 'text-red-600' : ''}">
                <div><span class="font-medium">Ubicación:</span> ${activity.location}</div>
                
            </div>
            <div class="mt-3 flex space-x-2">
                ${!isCancelled ? `
                    <button onclick="activityCalendar.editActivity(${activity.id})" 
                            class="text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded hover:bg-blue-200">
                        Editar
                    </button>
                ` : ''}
                <button onclick="activityCalendar.managePhotos(${activity.id})" 
                        class="text-sm bg-green-100 text-green-800 px-3 py-1 rounded hover:bg-green-200">
                    Fotos
                </button>
                ${!isCancelled ? `
                    <button onclick="activityCalendar.cancelActivity(${activity.id})" 
                            class="text-sm  text-red-800 px-3 py-1 rounded hover:bg-red-200">
                        
                    </button>
                ` : `
                    <button onclick="activityCalendar.deleteActivity(${activity.id})" 
                            class="text-sm bg-gray-100 text-gray-800 px-3 py-1 rounded hover:bg-gray-200">
                        Eliminar
                    </button>
                `}
            </div>
        `;
        
        return div;
    }
        
    deleteActivity(activityId) {
        if (confirm('¿Estás seguro de que deseas eliminar permanentemente esta actividad? Esta acción no se puede deshacer.')) {
            fetch(`/activities/${activityId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showNotification('Actividad eliminada permanentemente', 'success');
                    this.loadActivities();
                    this.loadUpcomingActivities();
                    if (this.selectedDate) {
                        this.loadDayActivities(this.selectedDate);
                    }
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showNotification('Error al eliminar la actividad', 'error');
            });
        }
    }

    cancelActivity(activityId) {
        if (confirm('¿Estás seguro de que deseas cancelar esta actividad?')) {
            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('status', 'cancelled');
            
            fetch(`/activities/${activityId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showNotification('Actividad cancelada', 'success');
                    this.loadActivities();
                    this.loadUpcomingActivities();
                    if (this.selectedDate) {
                        this.loadDayActivities(this.selectedDate);
                    }
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showNotification('Error al cancelar la actividad', 'error');
            });
        }
    }

    capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
    
    showNotification(message, type = 'info') {
        this.setCookie('notification', JSON.stringify({message, type}), 1);
        this.displayNotification(message, type);
    }
    
    displayNotification(message, type) {
        const existing = document.getElementById('activity-notification');
        if (existing) existing.remove();
        
        const notification = document.createElement('div');
        notification.id = 'activity-notification';
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    openNewActivityModal() {
        document.getElementById('new-activity-modal')?.classList.remove('hidden');
    }
    
    closeNewActivityModal() {
        document.getElementById('new-activity-modal')?.classList.add('hidden');
        document.getElementById('new-activity-form')?.reset();
    }
    
    openEditActivityModal() {
        document.getElementById('edit-activity-modal')?.classList.remove('hidden');
    }
    
    closeEditActivityModal() {
        document.getElementById('edit-activity-modal')?.classList.add('hidden');
        document.getElementById('edit-activity-form')?.reset();
    }
    
    editActivity(activityId) {
    fetch(`/activities/${activityId}`)
        .then(response => response.json())
        .then(activity => {
            document.getElementById('edit_activity_id').value = activity.id;
            document.getElementById('edit_activity_title').value = activity.title;
            document.getElementById('edit_activity_date').value = activity.date;
            document.getElementById('edit_activity_start_time').value = activity.start_time.substring(0, 5);
            document.getElementById('edit_activity_end_time').value = activity.end_time ? activity.end_time.substring(0, 5) : '';
            document.getElementById('edit_activity_type').value = activity.type;
            document.getElementById('edit_activity_status').value = activity.status;
            document.getElementById('edit_activity_location').value = activity.location;
            document.getElementById('edit_activity_description').value = activity.description || '';
            const buddySelect = document.getElementById('edit_activity_buddy_id');
            if (buddySelect && activity.buddy_id) {
                buddySelect.value = activity.buddy_id;
            } 
            const peerBuddy1Select = document.getElementById('edit_activity_peer_buddy_id');
            const peerBuddy2Select = document.getElementById('edit_activity_peer_buddy_id_2');
            
            if (peerBuddy1Select && activity.peer_buddies && activity.peer_buddies.length > 0) {
                peerBuddy1Select.value = activity.peer_buddies[0].id || '';
            }
            
            if (peerBuddy2Select && activity.peer_buddies && activity.peer_buddies.length > 1) {
                peerBuddy2Select.value = activity.peer_buddies[1].id || '';
            }
            
            this.openEditActivityModal();
        })
        .catch(error => {
            console.error('Error loading activity:', error);
            this.showNotification('Error al cargar la actividad', 'error');
        });
}
    
    managePhotos(activityId) {
        if (window.photoManager && typeof window.photoManager.openPhotosModal === 'function') {
            window.photoManager.openPhotosModal(activityId);
        } else {
            console.log('PhotoManager not loaded, attempting to open modal directly');
            const modal = document.getElementById('photos-modal');
            if (modal) {
                modal.classList.remove('hidden');
                fetch(`/activities/${activityId}`)
                    .then(response => response.json())
                    .then(activity => {
                        const titleElement = document.getElementById('photos-activity-title');
                        const dateElement = document.getElementById('photos-activity-date');
                        
                        if (titleElement) titleElement.textContent = activity.title || 'Actividad';
                        if (dateElement) dateElement.textContent = activity.formatted_date || activity.date;
                        
                        window.currentPhotoActivityId = activityId;
                        if (typeof loadActivityPhotos === 'function') {
                            loadActivityPhotos();
                        }
                    })
                    .catch(error => {
                        console.error('Error loading activity:', error);
                        this.showNotification('Error al cargar información de la actividad', 'error');
                    });
            } else {
                this.showNotification('Modal de fotos no encontrado', 'error');
            }
        }
    }
    
    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }
        setCookie(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = `${name}=${encodeURIComponent(value)};expires=${expires.toUTCString()};path=/`;
        } 
        getCookie(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
            }
            return null;}
        initPhotoIntegration() {
        const checkPhotoManager = () => {
            if (window.photoManager) {
                console.log('PhotoManager connected successfully');
            } else {
                setTimeout(checkPhotoManager, 100);
            }
        };
        
        checkPhotoManager();
    }
}






 </script>