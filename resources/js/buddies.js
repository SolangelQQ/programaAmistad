// // Función para ver detalles 
// function viewBuddyDetails(buddyId) {
    
//     fetch(`/buddies/${buddyId}/details`)
//         .then(response => {
//             if (!response.ok) {
//                 throw new Error('Network response was not ok');
//             }
//             return response.json();
//         })
//         .then(data => {
//             if (data.success) {
//                 populateDetailsModal(data.buddy);
//                 document.getElementById('view-details-modal').classList.remove('hidden');
//                 document.body.classList.add('overflow-hidden'); // No scroll del fondo
//             } else {
//                 alert('Error al cargar los detalles: ' + (data.message || 'Error desconocido'));
//             }
//         })
//         .catch(error => {
//             console.error('Error fetching details:', error);
//             alert('Error al cargar los detalles. Por favor revisa la consola para más información.');
//         });
// }



// function populateDetailsModal(buddy) {
//     console.log('Populating modal with buddy data:', buddy); // Debug log
    
//     // Verificar que los elementos existen antes de usarlos
//     const elements = {
//         avatar: document.getElementById('detail-avatar'),
//         name: document.getElementById('detail-name'),
//         type: document.getElementById('detail-type'),
//         ci: document.getElementById('detail-ci'),
//         age: document.getElementById('detail-age'),
//         phone: document.getElementById('detail-phone'),
//         email: document.getElementById('detail-email'),
//         createdAt: document.getElementById('detail-created-at'),
//         updatedAt: document.getElementById('detail-updated-at'),
//         disabilityInfo: document.getElementById('disability-info'),
//         experienceInfo: document.getElementById('experience-info'),
//         disability: document.getElementById('detail-disability'),
//         experience: document.getElementById('detail-experience'),
//         friendshipInfo: document.getElementById('friendship-info')
//     };
    
//     // Verificar elementos faltantes
//     for (const [key, element] of Object.entries(elements)) {
//         if (!element) {
//             console.warn(`Element not found: detail-${key}`);
//         }
//     }
    
//     // Información básica
//     if (elements.avatar) {
//         elements.avatar.src = `https://i.pravatar.cc/300?u=${buddy.email || buddy.id}`;
//         elements.avatar.alt = buddy.full_name;
//     }
//     if (elements.name) elements.name.textContent = buddy.full_name || 'Sin nombre';
//     if (elements.type) elements.type.textContent = buddy.type === 'buddy' ? 'Buddy' : 'PeerBuddy';
    
//     // Información personal
//     if (elements.ci) elements.ci.textContent = buddy.ci || 'No especificado';
//     if (elements.age) elements.age.textContent = `${buddy.age || 0} años`;
//     if (elements.phone) elements.phone.textContent = buddy.phone || 'No especificado';
//     if (elements.email) elements.email.textContent = buddy.email || 'No especificado';
    
//     // Información específica del tipo
//     if (elements.disabilityInfo && elements.experienceInfo) {
//         if (buddy.type === 'buddy') {
//             elements.disabilityInfo.classList.remove('hidden');
//             elements.experienceInfo.classList.add('hidden');
//             if (elements.disability) elements.disability.textContent = buddy.disability || 'No especificado';
//         } else {
//             elements.disabilityInfo.classList.add('hidden');
//             elements.experienceInfo.classList.remove('hidden');
//             if (elements.experience) elements.experience.textContent = buddy.experience || 'No especificado';
//         }
//     }
    
//     // Fechas
//     if (elements.createdAt) elements.createdAt.textContent = formatDate(buddy.created_at);
//     if (elements.updatedAt) elements.updatedAt.textContent = formatDate(buddy.updated_at);
    
//     // Información de emparejamiento
//     if (elements.friendshipInfo) {
//         if (buddy.active_friendship) {
//             const partnership = buddy.active_friendship;
//             const partnerName = buddy.type === 'buddy' ? partnership.peer_buddy.full_name : partnership.buddy.full_name;
//             const partnerType = buddy.type === 'buddy' ? 'PeerBuddy' : 'Buddy';
            
//             elements.friendshipInfo.innerHTML = `
//                 <div class="flex items-center justify-between">
//                     <div>
//                         <p class="text-sm font-medium text-gray-900">Emparejado con: ${partnerName}</p>
//                         <p class="text-xs text-gray-500">${partnerType}</p>
//                         <p class="text-xs text-gray-500">Desde: ${formatDate(partnership.start_date)}</p>
//                     </div>
//                     <span class="px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(partnership.status)}">
//                         ${partnership.status}
//                     </span>
//                 </div>
//             `;
//         } else {
//             elements.friendshipInfo.innerHTML = `
//                 <p class="text-sm text-gray-600">Sin emparejamiento activo</p>
//             `;
//         }
//     }
// }

// function closeViewDetailsModal() {
//     document.getElementById('view-details-modal').classList.add('hidden');
//     document.body.classList.remove('overflow-hidden');
// }

// // Función para editar 
// function editBuddy(buddyId) {
//     fetch(`/buddies/${buddyId}/edit`)
//         .then(response => {
//             if (!response.ok) {
//                 throw new Error('Network response was not ok');
//             }
//             return response.json();
//         })
//         .then(data => {
//             if (data.success) {
//                 populateEditModal(data.buddy);
//                 document.getElementById('edit-buddy-form').action = `/buddies/${buddyId}`;
//                 document.getElementById('edit-buddy-modal').classList.remove('hidden');
//                 document.body.classList.add('overflow-hidden'); // Previene scroll del fondo
//             } else {
//                 alert('Error al cargar los datos para editar: ' + (data.message || 'Error desconocido'));
//             }
//         })
//         .catch(error => {
//             console.error('Error fetching edit data:', error);
//             alert('Error al cargar los datos para editar. Por favor revisa la consola para más información.');
//         });
// }

// function populateEditModal(buddy) {
//     const fields = {
//         'edit-first-name': buddy.first_name || '',
//         'edit-last-name': buddy.last_name || '',
//         'edit-ci': buddy.ci || '',
//         'edit-age': buddy.age || '',
//         'edit-phone': buddy.phone || '',
//         'edit-email': buddy.email || '',
//         'edit-type': buddy.type || 'buddy'
//     };
    
//     // Llenar campos básicos
//     for (const [fieldId, value] of Object.entries(fields)) {
//         const field = document.getElementById(fieldId);
//         if (field) {
//             field.value = value;
//         } else {
//             console.warn(`Field not found: ${fieldId}`);
//         }
//     }
    
//     // Campos específicos del tipo
//     if (buddy.type === 'buddy') {
//         const disabilityField = document.getElementById('edit-disability');
//         if (disabilityField) disabilityField.value = buddy.disability || '';
//     } else {
//         const experienceField = document.getElementById('edit-experience');
//         if (experienceField) experienceField.value = buddy.experience || '';
//     }
    
//     // Mostrar campos correctos según el tipo
//     toggleEditTypeFields();
// }

// function toggleEditTypeFields() {
//     const typeField = document.getElementById('edit-type');
//     if (!typeField) {
//         console.warn('Type field not found');
//         return;
//     }
    
//     const type = typeField.value;
//     const buddyFields = document.getElementById('edit-buddy-fields');
//     const peerBuddyFields = document.getElementById('edit-peer-buddy-fields');
//     const disabilityField = document.getElementById('edit-disability');
//     const experienceField = document.getElementById('edit-experience');
    
//     if (type === 'buddy') {
//         if (buddyFields) buddyFields.classList.remove('hidden');
//         if (peerBuddyFields) peerBuddyFields.classList.add('hidden');
//         if (disabilityField) disabilityField.required = true;
//         if (experienceField) experienceField.required = false;
//     } else {
//         if (buddyFields) buddyFields.classList.add('hidden');
//         if (peerBuddyFields) peerBuddyFields.classList.remove('hidden');
//         if (disabilityField) disabilityField.required = false;
//         if (experienceField) experienceField.required = true;
//     }
// }

// function closeEditBuddyModal() {
//     document.getElementById('edit-buddy-modal').classList.add('hidden');
//     document.body.classList.remove('overflow-hidden');
// }

// function confirmDeleteBuddy(buddyId) {
//     const form = document.getElementById('delete-buddy-form');
//     const modal = document.getElementById('delete-buddy-modal');
    
//     if (form) {
//         form.action = '/buddies/' + buddyId;
//     }
//     if (modal) {
//         modal.classList.remove('hidden');
//     } else {
//         // Si no hay modal, usar confirm nativo
//         if (confirm('¿Está seguro que desea eliminar este buddy?')) {
//             // Crear formulario temporal para eliminar
//             const tempForm = document.createElement('form');
//             tempForm.method = 'POST';
//             tempForm.action = `/buddies/${buddyId}`;
            
//             // Agregar token CSRF
//             const csrfToken = document.querySelector('meta[name="csrf-token"]');
//             if (csrfToken) {
//                 const csrfInput = document.createElement('input');
//                 csrfInput.type = 'hidden';
//                 csrfInput.name = '_token';
//                 csrfInput.value = csrfToken.getAttribute('content');
//                 tempForm.appendChild(csrfInput);
//             }
            
//             // Agregar método DELETE
//             const methodInput = document.createElement('input');
//             methodInput.type = 'hidden';
//             methodInput.name = '_method';
//             methodInput.value = 'DELETE';
//             tempForm.appendChild(methodInput);
            
//             document.body.appendChild(tempForm);
//             tempForm.submit();
//         }
//     }
// }

// function closeDeleteBuddyModal() {
//     const modal = document.getElementById('delete-buddy-modal');
//     if (modal) {
//         modal.classList.add('hidden');
//     }
// }

// function formatDate(dateString) {
//     if (!dateString) return 'No especificado';
//     try {
//         const date = new Date(dateString);
//         return date.toLocaleDateString('es-ES', {
//             year: 'numeric',
//             month: 'long',
//             day: 'numeric',
//             hour: '2-digit',
//             minute: '2-digit'
//         });
//     } catch (error) {
//         console.warn('Error formatting date:', dateString);
//         return dateString;
//     }
// }

// function getStatusColor(status) {
//     const colors = {
//         'Emparejado': 'bg-green-100 text-green-800',
//         'Inactivo': 'bg-red-100 text-red-800',
//         'Pendiente': 'bg-yellow-100 text-yellow-800'
//     };
//     return colors[status] || 'bg-gray-100 text-gray-800';
// }

// document.addEventListener('DOMContentLoaded', function() {
//     console.log('Buddies.js loaded successfully'); // Debug log
    
//     // Cerrar modales al hacer clic fuera de ellos
//     ['delete-buddy-modal', 'view-details-modal', 'edit-buddy-modal'].forEach(modalId => {
//         const modal = document.getElementById(modalId);
//         if (modal) {
//             modal.addEventListener('click', function(e) {
//                 if (e.target === this) {
//                     this.classList.add('hidden');
//                 }
//             });
//         }
//     });
    
//     // Manejar envío del formulario de edición
//     const editForm = document.getElementById('edit-buddy-form');
//     if (editForm) {
//         editForm.addEventListener('submit', function(e) {
//             e.preventDefault();
//             const formData = new FormData(this);
            
//             // Agregar header para AJAX
//             const headers = {
//                 'X-Requested-With': 'XMLHttpRequest'
//             };
            
//             // Agregar CSRF token si existe
//             const csrfToken = document.querySelector('meta[name="csrf-token"]');
//             if (csrfToken) {
//                 headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
//             }
            
//             fetch(this.action, {
//                 method: 'POST',
//                 body: formData,
//                 headers: headers
//             })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success) {
//                     closeEditBuddyModal();
//                     // Mostrar mensaje de éxito
//                     alert(data.message || 'Buddy actualizado exitosamente');
//                     location.reload(); // Recargar la página para ver los cambios
//                 } else {
//                     if (data.errors) {
//                         let errorMessage = 'Errores de validación:\n';
//                         for (const [field, errors] of Object.entries(data.errors)) {
//                             errorMessage += `- ${field}: ${errors.join(', ')}\n`;
//                         }
//                         alert(errorMessage);
//                     } else {
//                         alert(data.message || 'Error al actualizar los datos');
//                     }
//                 }
//             })
//             .catch(error => {
//                 console.error('Error updating buddy:', error);
//                 alert('Error al actualizar los datos. Revisa la consola para más información.');
//             });
//         });
//     }
    
//     // Event listener para el cambio de tipo en el modal de edición
//     const editTypeField = document.getElementById('edit-type');
//     if (editTypeField) {
//         editTypeField.addEventListener('change', toggleEditTypeFields);
//     }
// });

// window.viewBuddyDetails = viewBuddyDetails;
// window.editBuddy = editBuddy;
// window.confirmDeleteBuddy = confirmDeleteBuddy;
// window.closeViewDetailsModal = closeViewDetailsModal;
// window.closeEditBuddyModal = closeEditBuddyModal;
// window.closeDeleteBuddyModal = closeDeleteBuddyModal;
// window.toggleEditTypeFields = toggleEditTypeFields;