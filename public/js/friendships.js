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