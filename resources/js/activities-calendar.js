// let activityCalendar;

// document.addEventListener('DOMContentLoaded', function() {
//     // Solo inicializar si estamos en la página de actividades
//     if (document.getElementById('calendar-grid')) {
//         activityCalendar = new ActivityCalendar();
        
//         // Exponer funciones al ámbito global
//         window.openNewActivityModal = () => activityCalendar.openNewActivityModal();
//         window.closeNewActivityModal = () => activityCalendar.closeNewActivityModal();
//         window.editActivity = (id) => activityCalendar.editActivity(id);
//         window.managePhotos = (id) => activityCalendar.managePhotos(id);
//         window.cancelActivity = (id) => activityCalendar.cancelActivity(id);
//         window.deleteActivity = (id) => activityCalendar.deleteActivity(id); 
//         window.closeEditActivityModal = () => activityCalendar.closeEditActivityModal();
        
//         // Exponer las nuevas funciones de detalles
//         window.viewActivityDetails = (id) => viewActivityDetails(id);
//         window.closeActivityDetailsModal = () => closeActivityDetailsModal();
//         window.showImageModal = (url) => showImageModal(url);
//         window.closeImageModal = () => closeImageModal();
        
//         const notification = activityCalendar.getCookie('notification');
//         if (notification) {
//             try {
//                 const {message, type} = JSON.parse(notification);
//                 activityCalendar.displayNotification(message, type);
//                 // Limpiar cookie
//                 activityCalendar.setCookie('notification', '', -1);
//             } catch (e) {
//                 console.error('Error parsing notification cookie:', e);
//             }
//         }
//     }
// });

// // Función para ver detalles de actividad
// async function viewActivityDetails(activityId) {
//     try {
//         // Mostrar loading si existe la función
//         if (typeof showLoading === 'function') {
//             showLoading();
//         }
        
//         // Hacer petición al endpoint show del controlador
//         const response = await fetch(`/activities/${activityId}`, {
//             method: 'GET',
//             headers: {
//                 'Accept': 'application/json',
//                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
//             }
//         });
        
//         if (!response.ok) {
//             throw new Error('Error al cargar los detalles de la actividad');
//         }
        
//         const activity = await response.json();
        
//         // Crear y mostrar modal con los detalles
//         showActivityDetailsModal(activity);
        
//     } catch (error) {
//         console.error('Error:', error);
//         // Usar la función de alerta si existe, sino usar alert
//         if (typeof showAlert === 'function') {
//             showAlert('Error al cargar los detalles de la actividad', 'error');
//         } else {
//             alert('Error al cargar los detalles de la actividad');
//         }
//     } finally {
//         if (typeof hideLoading === 'function') {
//             hideLoading();
//         }
//     }
// }

// function showActivityDetailsModal(activity) {
//     // Mapear tipos y estados a labels más amigables
//     const typeLabels = {
//         'recreational': 'Recreativa',
//         'educational': 'Educativa',
//         'cultural': 'Cultural',
//         'sports': 'Deportiva',
//         'social': 'Social'
//     };
    
//     const statusLabels = {
//         'scheduled': 'Programada',
//         'in_progress': 'En progreso',
//         'completed': 'Completada',
//         'cancelled': 'Cancelada'
//     };
    
//     const statusColors = {
//         'scheduled': 'bg-blue-100 text-blue-800',
//         'in_progress': 'bg-yellow-100 text-yellow-800',
//         'completed': 'bg-green-100 text-green-800',
//         'cancelled': 'bg-red-100 text-red-800'
//     };
    
//     // Formatear fecha y hora
//     const formattedDate = new Date(activity.date).toLocaleDateString('es-ES', {
//         weekday: 'long',
//         year: 'numeric',
//         month: 'long',
//         day: 'numeric'
//     });
    
//     const formattedStartTime = activity.start_time ? 
//         new Date(`2000-01-01 ${activity.start_time}`).toLocaleTimeString('es-ES', {
//             hour: '2-digit',
//             minute: '2-digit'
//         }) : '';
    
//     const formattedEndTime = activity.end_time ? 
//         new Date(`2000-01-01 ${activity.end_time}`).toLocaleTimeString('es-ES', {
//             hour: '2-digit',
//             minute: '2-digit'
//         }) : '';
    
//     // Crear modal HTML
//     const modalHtml = `
//         <div id="activity-details-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
//             <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
//                 <div class="mt-3">
//                     <!-- Header -->
//                     <div class="flex items-center justify-between pb-4 border-b">
//                         <h3 class="text-lg font-medium text-gray-900">Detalles de la Actividad</h3>
//                         <button onclick="closeActivityDetailsModal()" class="text-gray-400 hover:text-gray-600">
//                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
//                             </svg>
//                         </button>
//                     </div>
                    
//                     <!-- Content -->
//                     <div class="mt-4 space-y-4">
//                         <!-- Título y Estado -->
//                         <div class="flex items-start justify-between">
//                             <div class="flex-1">
//                                 <h4 class="text-xl font-semibold text-gray-900">${activity.title}</h4>
//                                 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-2 ${statusColors[activity.status] || 'bg-gray-100 text-gray-800'}">
//                                     ${statusLabels[activity.status] || activity.status}
//                                 </span>
//                             </div>
//                         </div>
                        
//                         <!-- Descripción -->
//                         ${activity.description ? `
//                             <div>
//                                 <h5 class="text-sm font-medium text-gray-700 mb-1">Descripción</h5>
//                                 <p class="text-gray-600">${activity.description}</p>
//                             </div>
//                         ` : ''}
                        
//                         <!-- Información básica -->
//                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
//                             <div>
//                                 <h5 class="text-sm font-medium text-gray-700 mb-1">Fecha</h5>
//                                 <p class="text-gray-600 capitalize">${formattedDate}</p>
//                             </div>
                            
//                             <div>
//                                 <h5 class="text-sm font-medium text-gray-700 mb-1">Horario</h5>
//                                 <p class="text-gray-600">
//                                     ${formattedStartTime}${formattedEndTime ? ` - ${formattedEndTime}` : ''}
//                                 </p>
//                             </div>
                            
//                             <div>
//                                 <h5 class="text-sm font-medium text-gray-700 mb-1">Ubicación</h5>
//                                 <p class="text-gray-600">${activity.location}</p>
//                             </div>
                            
//                             <div>
//                                 <h5 class="text-sm font-medium text-gray-700 mb-1">Tipo</h5>
//                                 <p class="text-gray-600">${typeLabels[activity.type] || activity.type}</p>
//                             </div>
//                         </div>
                        
//                         <!-- Fotos -->
//                         ${activity.photo_urls && activity.photo_urls.length > 0 ? `
//                             <div>
//                                 <h5 class="text-sm font-medium text-gray-700 mb-2">Fotos</h5>
//                                 <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
//                                     ${activity.photo_urls.map(photo => `
//                                         <img src="${photo.url}" alt="Foto de actividad" 
//                                              class="w-full h-24 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity"
//                                              onclick="showImageModal('${photo.url}')">
//                                     `).join('')}
//                                 </div>
//                             </div>
//                         ` : ''}
//                     </div>
                    
//                     <!-- Footer -->
//                     <div class="flex items-center justify-between pt-4 mt-6 border-t">
                        
//                         <button onclick="closeActivityDetailsModal()" 
//                                 class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded text-sm">
//                             Cerrar
//                         </button>
//                     </div>
//                 </div>
//             </div>
//         </div>
//     `;
    
//     // Agregar modal al DOM
//     document.body.insertAdjacentHTML('beforeend', modalHtml);
// }

// function closeActivityDetailsModal() {
//     const modal = document.getElementById('activity-details-modal');
//     if (modal) {
//         modal.remove();
//     }
// }

// // Función auxiliar para mostrar imágenes en modal
// function showImageModal(imageUrl) {
//     const imageModalHtml = `
//         <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-60" onclick="closeImageModal()">
//             <div class="max-w-4xl max-h-full p-4">
//                 <img src="${imageUrl}" alt="Imagen ampliada" class="max-w-full max-h-full object-contain">
//                 <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300">
//                     <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
//                     </svg>
//                 </button>
//             </div>
//         </div>
//     `;
    
//     document.body.insertAdjacentHTML('beforeend', imageModalHtml);
// }

// function closeImageModal() {
//     const modal = document.getElementById('image-modal');
//     if (modal) {
//         modal.remove();
//     }
// }


// class ActivityCalendar {
//     constructor() {
//         this.currentMonth = new Date().getMonth();
//         this.currentYear = new Date().getFullYear();
//         this.selectedDate = null;
//         this.activities = {};
//         this.initPhotoIntegration();
        
//         this.init();
//         window.activityCalendar = this;
//     }
    
//     init() {
//         this.setupEventListeners();
//         this.loadCalendar();
//         this.loadActivities();
//         this.loadUpcomingActivities();
//     }
    
//     setupEventListeners() {
//         // Navegación del calendario
//         document.getElementById('prev-month')?.addEventListener('click', () => {
//             this.currentMonth--;
//             if (this.currentMonth < 0) {
//                 this.currentMonth = 11;
//                 this.currentYear--;
//             }
//             this.loadCalendar();
//             this.loadActivities();
//             this.notifySidebarMonthChanged();
//         });
        
//         document.getElementById('next-month')?.addEventListener('click', () => {
//             this.currentMonth++;
//             if (this.currentMonth > 11) {
//                 this.currentMonth = 0;
//                 this.currentYear++;
//             }
//             this.loadCalendar();
//             this.loadActivities();
//             this.notifySidebarMonthChanged();
//         });
        
//         // Formulario nueva actividad
//         const newActivityForm = document.getElementById('new-activity-form');
//         if (newActivityForm) {
//             newActivityForm.addEventListener('submit', (e) => this.handleCreateActivity(e));
//         }
        
//         // Formulario editar actividad
//         const editActivityForm = document.getElementById('edit-activity-form');
//         if (editActivityForm) {
//             editActivityForm.addEventListener('submit', (e) => this.handleEditActivity(e));
//         }
        
//         // Botón agregar actividad rápida
//         document.getElementById('quick-add-activity')?.addEventListener('click', () => {
//             if (this.selectedDate) {
//                 document.getElementById('activity_date').value = this.selectedDate;
//             }
//             this.openNewActivityModal();
//         });  
//     }

//     notifySidebarMonthChanged() {
//         document.dispatchEvent(new CustomEvent('calendar-month-changed', {
//             detail: {
//                 month: this.currentMonth + 1,
//                 year: this.currentYear
//             }
//         }));
        
//         // También notificar al sidebar directamente si existe
//         if (window.sidebarInstance && typeof window.sidebarInstance.loadActivities === 'function') {
//             window.sidebarInstance.loadActivities();
//         }
//     }
    
//     loadCalendar() {
//         const monthNames = [
//             'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
//             'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
//         ];
        
//         document.getElementById('current-month').textContent = 
//             `${monthNames[this.currentMonth]} ${this.currentYear}`;
        
//         const firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
//         const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
//         const daysInPrevMonth = new Date(this.currentYear, this.currentMonth, 0).getDate();
        
//         const calendarGrid = document.getElementById('calendar-grid');
//         calendarGrid.innerHTML = '';
        
//         // Días del mes anterior
//         for (let i = firstDay - 1; i >= 0; i--) {
//             const dayElement = this.createDayElement(daysInPrevMonth - i, true);
//             calendarGrid.appendChild(dayElement);
//         }
        
//         // Días del mes actual
//         for (let day = 1; day <= daysInMonth; day++) {
//             const dayElement = this.createDayElement(day, false);
//             calendarGrid.appendChild(dayElement);
//         }
        
//         // Días del mes siguiente
//         const totalCells = calendarGrid.children.length;
//         const remainingCells = 42 - totalCells; // 6 filas x 7 días
//         for (let day = 1; day <= remainingCells; day++) {
//             const dayElement = this.createDayElement(day, true);
//             calendarGrid.appendChild(dayElement);
//         }
//     }
    
//     createDayElement(day, isOtherMonth) {
//         const dayElement = document.createElement('div');
//         dayElement.className = `p-2 text-center cursor-pointer rounded hover:bg-gray-100 ${
//             isOtherMonth ? 'text-gray-400' : 'text-gray-900'
//         }`;
//         dayElement.textContent = day;
        
//         if (!isOtherMonth) {
//             const dateStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
//             dayElement.setAttribute('data-date', dateStr);
            
//             // Marcar día actual
//             const today = new Date();
//             if (this.currentYear === today.getFullYear() && 
//                 this.currentMonth === today.getMonth() && 
//                 day === today.getDate()) {
//                 dayElement.classList.add('bg-blue-100', 'font-semibold');
//             }
            
//             // Event listener para seleccionar día
//             dayElement.addEventListener('click', () => this.selectDate(dateStr, dayElement));
//         }
        
//         return dayElement;
//     }
    
//     selectDate(dateStr, dayElement) {
//         // Remover selección anterior
//         document.querySelectorAll('[data-date]').forEach(el => {
//             el.classList.remove('bg-blue-500', 'text-white');
//         });
        
//         // Aplicar nueva selección
//         dayElement.classList.add('bg-blue-500', 'text-white');
//         this.selectedDate = dateStr;
        
//         this.loadDayActivities(dateStr);
//     }

//     loadActivities(filters = {}) {
//     let url = `/api/activities/calendar?month=${this.currentMonth + 1}&year=${this.currentYear}`;
    
//     // Agregar filtros a la URL
//     if (filters.type) {
//         url += `&type=${filters.type}`;
//     }
//     if (filters.status) {
//         url += `&status=${filters.status}`;
//     }
//     // FILTROS DE FECHA:
//     if (filters.dateStart) {
//         url += `&dateStart=${filters.dateStart}`;
//     }
//     if (filters.dateEnd) {
//         url += `&dateEnd=${filters.dateEnd}`;
//     }
    
    
//     fetch(url)
//         .then(response => response.json())
//         .then(data => {
//             this.activities = data;
//             this.markActivityDays();
//         })
//         .catch(error => console.error('Error loading activities:', error));
// }
    

//     markActivityDays() {
//         // Limpiar marcadores anteriores
//         document.querySelectorAll('[data-date]').forEach(el => {
//             el.classList.remove('has-activities');
//             // Remover dots anteriores
//             const existingDot = el.querySelector('.activity-dot');
//             if (existingDot) existingDot.remove();
//         });
        
//         // Definir colores por tipo
//         const typeColors = {
//             'recreational': 'bg-blue-500',
//             'educational': 'bg-green-500', 
//             'cultural': 'bg-purple-500',
//             'sports': 'bg-orange-500',
//             'social': 'bg-pink-500'
//         };
        
//         // Marcar días con actividades
//         Object.keys(this.activities).forEach(day => {
//             const dateStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
//             const dayElement = document.querySelector(`[data-date="${dateStr}"]`);
//             if (dayElement) {
//                 dayElement.classList.add('has-activities');
                
//                 const dayData = this.activities[day];
//                 const types = dayData.types || [];
                
//                 // Crear contenedor de dots
//                 const dotContainer = document.createElement('div');
//                 dotContainer.className = 'flex justify-center space-x-1 mt-1';
                
//                 // Agregar dot por cada tipo único
//                 types.forEach(type => {
//                     const dot = document.createElement('div');
//                     dot.className = `activity-dot w-2 h-2 ${typeColors[type] || 'bg-gray-500'} rounded-full`;
//                     dotContainer.appendChild(dot);
//                 });
                
//                 dayElement.appendChild(dotContainer);
//             }
//         });
//     }
    
//     loadDayActivities(dateStr) {
//         const [year, month, day] = dateStr.split('-');
//         const formattedDate = `${parseInt(day)} ${this.getMonthName(parseInt(month) - 1)}`;
        
//         document.getElementById('selected-date').textContent = formattedDate;
        
//         fetch(`/api/activities/by-date?date=${dateStr}`)
//             .then(response => response.json())
//             .then(activities => {
//                 const container = document.getElementById('day-activities');
//                 container.innerHTML = '';
                
//                 if (activities.length === 0) {
//                     container.innerHTML = '<div class="text-gray-500 text-sm">No hay actividades para esta fecha</div>';
//                     return;
//                 }
                
//                 activities.forEach(activity => {
//                     const activityElement = this.createActivityElement(activity);
//                     container.appendChild(activityElement);
//                 });
//             })
//             .catch(error => {
//                 console.error('Error loading day activities:', error);
//                 document.getElementById('day-activities').innerHTML = 
//                     '<div class="text-red-500 text-sm">Error al cargar actividades</div>';
//             });
//     }
    
//     createActivityElement(activity) {
//         const typeColors = {
//             'recreational': 'border-green-500',
//             'educational': 'border-blue-500',
//             'cultural': 'border-purple-500',
//             'sports': 'border-orange-500',
//             'social': 'border-pink-500'
//         };
        
//         const borderColor = typeColors[activity.type] || 'border-gray-500';
//         const div = document.createElement('div');
//         div.className = `pl-4 border-l-4 ${borderColor}`;
        
//         div.innerHTML = `
//             <div class="text-sm font-medium">${activity.title}</div>
//             <div class="text-xs text-gray-500">${activity.location}</div>
//             <div class="text-xs text-gray-400">${activity.start_time}${activity.end_time ? ' - ' + activity.end_time : ''}</div>
//             <div class="mt-2 flex flex-wrap gap-1">
//                 <button onclick="viewActivityDetails(${activity.id})"
//                     class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded hover:bg-gray-200 transition-colors">
//                     <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
//                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
//                     </svg>
//                     Ver detalles
//                 </button>
//                 <button onclick="editActivity(${activity.id})"
//                     class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded hover:bg-blue-200 transition-colors">
//                     <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
//                     </svg>
//                     Editar
//                 </button>
//                 <button onclick="managePhotos(${activity.id})"
//                     class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded hover:bg-green-200 transition-colors">
//                     <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
//                     </svg>
//                     Fotos
//                 </button>
//             </div>
//         `;
        
//         return div;
//     }

    
//     getMonthName(monthIndex) {
//         const months = [
//             'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
//             'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
//         ];
//         return months[monthIndex];
//     }
    
//     handleCreateActivity(e) {
//     e.preventDefault();
    
//     const formData = new FormData(e.target);
//     const submitButton = e.target.querySelector('button[type="submit"]');
    
//     // Deshabilitar botón y mostrar loading
//     submitButton.disabled = true;
//     submitButton.textContent = 'Guardando...';
    
//     fetch('/activities', {
//         method: 'POST',
//         body: formData,
//         headers: {
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
//                            document.querySelector('input[name="_token"]')?.value
//         }
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             this.showNotification('Actividad creada exitosamente', 'success');
//             this.closeNewActivityModal();
//             this.loadActivities();
//             this.loadUpcomingActivities();
            
//             // Notificar al sidebar
//             document.dispatchEvent(new CustomEvent('activity-created', {
//                 detail: data.activity
//             }));
            
//             // Seleccionar la fecha de la nueva actividad
//             if (data.activity.date) {
//                 this.selectDateFromString(data.activity.date);
//             }
//         } else {
//             throw new Error(data.message || 'Error al crear la actividad');
//         }
//     })
//     .catch(error => {
//         console.error('Error:', error);
//         this.showNotification('Error al crear la actividad: ' + error.message, 'error');
//     })
//     .finally(() => {
//         // Restaurar botón
//         submitButton.disabled = false;
//         submitButton.textContent = 'Guardar Actividad';
//     });
// }
    
//     handleEditActivity(e) {
//         e.preventDefault();
        
//         const formData = new FormData(e.target);
//         const activityId = document.getElementById('edit_activity_id').value;
//         const submitButton = e.target.querySelector('button[type="submit"]');
        
//         // Deshabilitar botón y mostrar loading
//         submitButton.disabled = true;
//         submitButton.textContent = 'Actualizando...';
        
//         fetch(`/activities/${activityId}`, {
//             method: 'POST',
//             body: formData,
//             headers: {
//                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
//                                document.querySelector('input[name="_token"]')?.value
//             }
//         })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 this.showNotification('Actividad actualizada exitosamente', 'success');
//                 this.closeEditActivityModal();
//                 this.loadActivities();
//                 this.loadUpcomingActivities();
                
//                 // Recargar actividades del día si está seleccionado
//                 if (this.selectedDate) {
//                     this.loadDayActivities(this.selectedDate);
//                 }
//             } else {
//                 throw new Error(data.message || 'Error al actualizar la actividad');
//             }
//         })
//         .catch(error => {
//             console.error('Error:', error);
//             this.showNotification('Error al actualizar la actividad: ' + error.message, 'error');
//         })
//         .finally(() => {
//             // Restaurar botón
//             submitButton.disabled = false;
//             submitButton.textContent = 'Actualizar Actividad';
//         });
//     }
    
//     selectDateFromString(dateStr) {
//         const date = new Date(dateStr);
//         if (date.getFullYear() === this.currentYear && date.getMonth() === this.currentMonth) {
//             const dayElement = document.querySelector(`[data-date="${dateStr}"]`);
//             if (dayElement) {
//                 this.selectDate(dateStr, dayElement);
//             }
//         }
//     }
    
//     loadUpcomingActivities() {
//         const container = document.getElementById('upcoming-activities');
//         if (!container) return;
        
//         // Agregar parámetro para incluir actividades canceladas
//         fetch('/api/activities/upcoming?include_cancelled=true')
//             .then(response => response.json())
//             .then(activities => {
//                 container.innerHTML = '';
                
//                 if (activities.length === 0) {
//                     container.innerHTML = `
//                         <div class="text-gray-500 text-center py-8">
//                             No hay actividades programadas
//                         </div>
//                     `;
//                     return;
//                 }
                
//                 // Separar actividades por estado
//                 const activeActivities = activities.filter(a => a.status !== 'cancelled');
//                 const cancelledActivities = activities.filter(a => a.status === 'cancelled');
                
//                 // Mostrar actividades activas primero
//                 activeActivities.forEach(activity => {
//                     const activityCard = this.createUpcomingActivityCard(activity);
//                     container.appendChild(activityCard);
//                 });
                
//                 // Si hay actividades canceladas, agregar separador y mostrarlas
//                 if (cancelledActivities.length > 0) {
//                     const separator = document.createElement('div');
//                     separator.className = 'border-t border-red-200 my-4 pt-4';
//                     separator.innerHTML = '<h5 class="text-sm font-medium text-red-600 mb-3">Actividades Canceladas</h5>';
//                     container.appendChild(separator);
                    
//                     cancelledActivities.forEach(activity => {
//                         const activityCard = this.createUpcomingActivityCard(activity);
//                         container.appendChild(activityCard);
//                     });
//                 }
//             })
//             .catch(error => {
//                 console.error('Error loading upcoming activities:', error);
//                 container.innerHTML = `
//                     <div class="text-red-500 text-center py-4">
//                         Error al cargar actividades próximas
//                     </div>
//                 `;
//             });
//     }
    
//     createUpcomingActivityCard(activity) {
//         const div = document.createElement('div');
        
//         // Agregar clase especial para actividades canceladas
//         const isCancelled = activity.status === 'cancelled';
//         const baseClasses = 'border rounded-lg p-4 activity-card';
//         const cancelledClasses = isCancelled ? 'border-red-300 bg-red-50' : '';
        
//         div.className = `${baseClasses} ${cancelledClasses}`;
//         div.setAttribute('data-activity-id', activity.id);
        
//         const statusColors = {
//             'scheduled': 'green',
//             'in_progress': 'yellow', 
//             'completed': 'blue',
//             'cancelled': 'red'
//         };
        
//         const statusLabels = {
//             'scheduled': 'Programada',
//             'in_progress': 'En Progreso',
//             'completed': 'Completada', 
//             'cancelled': 'Cancelada'
//         };
        
//         const statusColor = statusColors[activity.status] || 'gray';
//         const statusLabel = statusLabels[activity.status] || this.capitalizeFirst(activity.status);
        
//         div.innerHTML = `
//             <div class="flex items-start justify-between">
//                 <h4 class="font-medium ${isCancelled ? 'text-red-600 line-through' : ''}">${activity.title}</h4>
//                 ${isCancelled ? '<span class="text-red-500 text-xs font-semibold">CANCELADA</span>' : ''}
//             </div>
//             <div class="flex items-center text-sm text-gray-500 mt-1">
//                 <span class="status-badge bg-${statusColor}-100 text-${statusColor}-800 text-xs px-2 py-1 rounded-full mr-2">
//                     ${statusLabel}
//                 </span>
//                 <span class="${isCancelled ? 'text-red-500' : ''}">${activity.formatted_date} - ${activity.formatted_time}</span>
//             </div>
//             <div class="mt-2 text-sm ${isCancelled ? 'text-red-600' : ''}">
//                 <div><span class="font-medium">Ubicación:</span> ${activity.location}</div>
                
//             </div>
//             <div class="mt-3 flex space-x-2">
//                 ${!isCancelled ? `
//                     <button onclick="activityCalendar.editActivity(${activity.id})" 
//                             class="text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded hover:bg-blue-200">
//                         Editar
//                     </button>
//                 ` : ''}
//                 <button onclick="activityCalendar.managePhotos(${activity.id})" 
//                         class="text-sm bg-green-100 text-green-800 px-3 py-1 rounded hover:bg-green-200">
//                     Fotos
//                 </button>
//                 ${!isCancelled ? `
//                     <button onclick="activityCalendar.cancelActivity(${activity.id})" 
//                             class="text-sm  text-red-800 px-3 py-1 rounded hover:bg-red-200">
                        
//                     </button>
//                 ` : `
//                     <button onclick="activityCalendar.deleteActivity(${activity.id})" 
//                             class="text-sm bg-gray-100 text-gray-800 px-3 py-1 rounded hover:bg-gray-200">
//                         Eliminar
//                     </button>
//                 `}
//             </div>
//         `;
        
//         return div;
//     }
        
//     deleteActivity(activityId) {
//         if (confirm('¿Estás seguro de que deseas eliminar permanentemente esta actividad? Esta acción no se puede deshacer.')) {
//             fetch(`/activities/${activityId}`, {
//                 method: 'DELETE',
//                 headers: {
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
//                     'Content-Type': 'application/json'
//                 }
//             })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success) {
//                     this.showNotification('Actividad eliminada permanentemente', 'success');
//                     this.loadActivities();
//                     this.loadUpcomingActivities();
                    
//                     // Si está seleccionada la fecha, recargar actividades del día
//                     if (this.selectedDate) {
//                         this.loadDayActivities(this.selectedDate);
//                     }
//                 } else {
//                     throw new Error(data.message);
//                 }
//             })
//             .catch(error => {
//                 console.error('Error:', error);
//                 this.showNotification('Error al eliminar la actividad', 'error');
//             });
//         }
//     }

//     cancelActivity(activityId) {
//         if (confirm('¿Estás seguro de que deseas cancelar esta actividad?')) {
//             const formData = new FormData();
//             formData.append('_method', 'PUT');
//             formData.append('status', 'cancelled');
            
//             fetch(`/activities/${activityId}`, {
//                 method: 'POST',
//                 body: formData,
//                 headers: {
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
//                 }
//             })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success) {
//                     this.showNotification('Actividad cancelada', 'success');
//                     this.loadActivities();
//                     this.loadUpcomingActivities();
                    
//                     // Si está seleccionada la fecha, recargar actividades del día
//                     if (this.selectedDate) {
//                         this.loadDayActivities(this.selectedDate);
//                     }
//                 } else {
//                     throw new Error(data.message);
//                 }
//             })
//             .catch(error => {
//                 console.error('Error:', error);
//                 this.showNotification('Error al cancelar la actividad', 'error');
//             });
//         }
//     }

//     capitalizeFirst(str) {
//         return str.charAt(0).toUpperCase() + str.slice(1);
//     }
    
//     showNotification(message, type = 'info') {
//         // Crear cookie para la notificación
//         this.setCookie('notification', JSON.stringify({message, type}), 1);
        
//         // Mostrar notificación inmediatamente
//         this.displayNotification(message, type);
//     }
    
//     displayNotification(message, type) {
//         // Remover notificación existente
//         const existing = document.getElementById('activity-notification');
//         if (existing) existing.remove();
        
//         const notification = document.createElement('div');
//         notification.id = 'activity-notification';
//         notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
//             type === 'success' ? 'bg-green-500 text-white' : 
//             type === 'error' ? 'bg-red-500 text-white' : 
//             'bg-blue-500 text-white'
//         }`;
//         notification.textContent = message;
        
//         document.body.appendChild(notification);
        
//         // Auto remove after 5 seconds
//         setTimeout(() => {
//             if (notification.parentNode) {
//                 notification.remove();
//             }
//         }, 5000);
//     }
    
//     // Funciones públicas para los modales
//     openNewActivityModal() {
//         document.getElementById('new-activity-modal')?.classList.remove('hidden');
//     }
    
//     closeNewActivityModal() {
//         document.getElementById('new-activity-modal')?.classList.add('hidden');
//         document.getElementById('new-activity-form')?.reset();
//     }
    
//     openEditActivityModal() {
//         document.getElementById('edit-activity-modal')?.classList.remove('hidden');
//     }
    
//     closeEditActivityModal() {
//         document.getElementById('edit-activity-modal')?.classList.add('hidden');
//         document.getElementById('edit-activity-form')?.reset();
//     }
    
//     editActivity(activityId) {
//     fetch(`/activities/${activityId}`)
//         .then(response => response.json())
//         .then(activity => {
//             // Llenar formulario de edición - solo campos que existen
//             document.getElementById('edit_activity_id').value = activity.id;
//             document.getElementById('edit_activity_title').value = activity.title;
//             document.getElementById('edit_activity_date').value = activity.date;
//             document.getElementById('edit_activity_start_time').value = activity.start_time.substring(0, 5);
//             document.getElementById('edit_activity_end_time').value = activity.end_time ? activity.end_time.substring(0, 5) : '';
//             document.getElementById('edit_activity_type').value = activity.type;
//             document.getElementById('edit_activity_status').value = activity.status;
//             document.getElementById('edit_activity_location').value = activity.location;
//             document.getElementById('edit_activity_description').value = activity.description || '';
            
//             // Asignar participantes (solo si los campos existen)
//             const buddySelect = document.getElementById('edit_activity_buddy_id');
//             if (buddySelect && activity.buddy_id) {
//                 buddySelect.value = activity.buddy_id;
//             }
            
//             // PeerBuddies (solo si los campos existen)
//             const peerBuddy1Select = document.getElementById('edit_activity_peer_buddy_id');
//             const peerBuddy2Select = document.getElementById('edit_activity_peer_buddy_id_2');
            
//             if (peerBuddy1Select && activity.peer_buddies && activity.peer_buddies.length > 0) {
//                 peerBuddy1Select.value = activity.peer_buddies[0].id || '';
//             }
            
//             if (peerBuddy2Select && activity.peer_buddies && activity.peer_buddies.length > 1) {
//                 peerBuddy2Select.value = activity.peer_buddies[1].id || '';
//             }
            
//             this.openEditActivityModal();
//         })
//         .catch(error => {
//             console.error('Error loading activity:', error);
//             this.showNotification('Error al cargar la actividad', 'error');
//         });
// }
    
//     managePhotos(activityId) {
//     // Verificar si photoManager está disponible
//         if (window.photoManager && typeof window.photoManager.openPhotosModal === 'function') {
//             window.photoManager.openPhotosModal(activityId);
//         } else {
//             // Fallback si photoManager no está cargado aún
//             console.log('PhotoManager not loaded, attempting to open modal directly');
            
//             // Intentar abrir el modal directamente
//             const modal = document.getElementById('photos-modal');
//             if (modal) {
//                 modal.classList.remove('hidden');
                
//                 // Cargar información e la actividad
//                 fetch(`/activities/${activityId}`)
//                     .then(response => response.json())
//                     .then(activity => {
//                         const titleElement = document.getElementById('photos-activity-title');
//                         const dateElement = document.getElementById('photos-activity-date');
                        
//                         if (titleElement) titleElement.textContent = activity.title || 'Actividad';
//                         if (dateElement) dateElement.textContent = activity.formatted_date || activity.date;
                        
//                         // Guardar ID de actividad actual
//                         window.currentPhotoActivityId = activityId;
                        
//                         // Cargar fotos si existe la función
//                         if (typeof loadActivityPhotos === 'function') {
//                             loadActivityPhotos();
//                         }
//                     })
//                     .catch(error => {
//                         console.error('Error loading activity:', error);
//                         this.showNotification('Error al cargar información de la actividad', 'error');
//                     });
//             } else {
//                 this.showNotification('Modal de fotos no encontrado', 'error');
//             }
//         }
//     }
    
//     showNotification(message, type = 'success') {
//         // Crear notificación simple
//         const notification = document.createElement('div');
//         notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
//             type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
//         }`;
//         notification.textContent = message;
        
//         document.body.appendChild(notification);
        
//         // Auto remove after 3 seconds
//         setTimeout(() => {
//             if (notification.parentNode) {
//                 notification.remove();
//             }
//         }, 3000);
//     }
        
//         // Utilidades para cookies
//         setCookie(name, value, days) {
//             const expires = new Date();
//             expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
//             document.cookie = `${name}=${encodeURIComponent(value)};expires=${expires.toUTCString()};path=/`;
//         }
        
//         getCookie(name) {
//             const nameEQ = name + "=";
//             const ca = document.cookie.split(';');
//             for (let i = 0; i < ca.length; i++) {
//                 let c = ca[i];
//                 while (c.charAt(0) === ' ') c = c.substring(1, c.length);
//                 if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
//             }
//             return null;
//         }

//         initPhotoIntegration() {
//         // Esperar a que photoManager esté disponible
//         const checkPhotoManager = () => {
//             if (window.photoManager) {
//                 // PhotoManager está listo, conectar eventos si es necesario
//                 console.log('PhotoManager connected successfully');
//             } else {
//                 // Intentar de nuevo en 100ms
//                 setTimeout(checkPhotoManager, 100);
//             }
//         };
        
//         checkPhotoManager();
//     }
// }


