// // activities-sidebar.js
// function activitiesSidebar() {
//     return {
//         activities: [],
//         loading: false,
        
//         init() {
//             this.loadActivities();
            
//             // Escuchar cambios de mes del calendario
//             document.addEventListener('calendar-month-changed', (event) => {
//                 this.loadActivities();
//             });
            
//             // Escuchar cuando se crea una nueva actividad
//             document.addEventListener('activity-created', () => {
//                 this.loadActivities();
//             });
//         },
        
//         async loadActivities() {
//             this.loading = true;
//             try {
//                 // Obtener el mes y año actual del calendario si existe
//                 let month = new Date().getMonth() + 1;
//                 let year = new Date().getFullYear();
                
//                 if (window.activityCalendar) {
//                     month = window.activityCalendar.currentMonth + 1;
//                     year = window.activityCalendar.currentYear;
//                 }
                
//                 const response = await fetch(`/api/activities?month=${month}&year=${year}`);
                
//                 if (!response.ok) {
//                     throw new Error('Error al cargar actividades');
//                 }
                
//                 const data = await response.json();
                
//                 // Procesar los datos según su estructura
//                 if (Array.isArray(data)) {
//                     this.activities = data;
//                 } else if (data.data && Array.isArray(data.data)) {
//                     this.activities = data.data;
//                 } else {
//                     // Si viene del calendario agrupado por días
//                     this.activities = [];
//                     Object.values(data).forEach(dayData => {
//                         if (dayData.activities && Array.isArray(dayData.activities)) {
//                             this.activities.push(...dayData.activities);
//                         } else if (Array.isArray(dayData)) {
//                             this.activities.push(...dayData);
//                         }
//                     });
//                 }
//                 this.updateUpcomingActivities();
                
//             } catch (error) {
//                 this.activities = [];
//             } finally {
//                 this.loading = false;
//             }
//         },
        
//         getActivityCountByType(type) {
//             if (!this.activities || !Array.isArray(this.activities)) {
//                 return 0;
//             }
            
//             const count = this.activities.filter(activity => {
//                 // Verificar si la actividad tiene el tipo correcto
//                 return activity.type === type && activity.status !== 'cancelled';
//             }).length;
            
//             return count;
//         },
        
//         filterByType(type) {
            
//             // Notificar al calendario para que filtre
//             if (window.activityCalendar && typeof window.activityCalendar.loadActivities === 'function') {
//                 window.activityCalendar.loadActivities({ type: type });
//             }
            
//             // Disparar evento personalizado
//             this.$dispatch('filter-changed', { type: type });
            
//             // También usar evento nativo
//             document.dispatchEvent(new CustomEvent('activity-filter-applied', {
//                 detail: { type: type }
//             }));
//         },
        
//         // todas las actividades del mes
//         clearFilters(event) {
//             if (event) {
//                 event.preventDefault();
//                 event.stopPropagation();
//             }
            
//             // Limpiar filtros del calendario
//             if (window.activityCalendar && typeof window.activityCalendar.loadActivities === 'function') {
//                 window.activityCalendar.loadActivities(); // Sin filtros
//             }
            
//             // Disparar eventos
//             this.$dispatch('filter-changed', {});
//             document.dispatchEvent(new CustomEvent('activity-filter-cleared'));
            
//             // Mostrar modal con todas las actividades del mes - NUEVA FUNCIONALIDAD
//             this.showAllActivitiesModal();
//         },
        
//         // Modal para mostrar todas las actividades
//         showAllActivitiesModal() {
//             // Crear modal dinámico
//             const modal = document.createElement('div');
//             modal.id = 'all-activities-modal';
//             modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-500 z-50';
            
//             const monthNames = [
//                 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
//                 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
//             ];
            
//             let currentMonth = new Date().getMonth();
//             let currentYear = new Date().getFullYear();
            
//             if (window.activityCalendar) {
//                 currentMonth = window.activityCalendar.currentMonth;
//                 currentYear = window.activityCalendar.currentYear;
//             }
            
//             const currentMonthName = monthNames[currentMonth];
            
//             modal.innerHTML = `
//     <div class="relative top-20 mx-auto p-5 border shadow-lg rounded-md bg-white max-w-2xl">
//         <div class="mt-3">
//             <div class="flex items-center justify-between mb-4">
//                 <h3 class="text-lg font-semibold text-gray-900">
//                     Todas las Actividades - ${currentMonthName} ${currentYear}
//                 </h3>
//                 <button onclick="document.getElementById('all-activities-modal').remove()" 
//                         class="text-gray-400 hover:text-gray-600">
//                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
//                     </svg>
//                 </button>
//             </div>
            
//             <div id="all-activities-content" class="max-h-96 overflow-y-auto">
//                 <div class="text-center py-4 text-gray-500">Cargando actividades...</div>
//             </div>
            
//             <div class="flex justify-end mt-4 pt-4 border-t">
//                 <button onclick="document.getElementById('all-activities-modal').remove()" 
//                         class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600">
//                     Cerrar
//                 </button>
//             </div>
//         </div>
//     </div>
// `;
            
//             document.body.appendChild(modal);
            
//             // Cargar y mostrar todas las actividades
//             this.loadAllActivitiesForModal();
//         },
        
//         // Cargar todas las actividades para el modal
//         async loadAllActivitiesForModal() {
//             try {
//                 let month = new Date().getMonth() + 1;
//                 let year = new Date().getFullYear();
                
//                 if (window.activityCalendar) {
//                     month = window.activityCalendar.currentMonth + 1;
//                     year = window.activityCalendar.currentYear;
//                 }
                
//                 const response = await fetch(`/api/activities?month=${month}&year=${year}`);
//                 const data = await response.json();
                
//                 let allActivities = [];
//                 if (Array.isArray(data)) {
//                     allActivities = data;
//                 } else if (data.data && Array.isArray(data.data)) {
//                     allActivities = data.data;
//                 } else {
//                     Object.values(data).forEach(dayData => {
//                         if (dayData.activities && Array.isArray(dayData.activities)) {
//                             allActivities.push(...dayData.activities);
//                         } else if (Array.isArray(dayData)) {
//                             allActivities.push(...dayData);
//                         }
//                     });
//                 }
                
//                 this.renderAllActivitiesModal(allActivities);
                
//             } catch (error) {
//                 console.error('Error loading all activities:', error);
//                 document.getElementById('all-activities-content').innerHTML = 
//                     '<div class="text-center py-4 text-red-500">Error al cargar las actividades</div>';
//             }
//         },
        
//         //Renderizar actividades en el modal
//         renderAllActivitiesModal(activities) {
//             const container = document.getElementById('all-activities-content');
//             if (!container) return;
            
//             if (!activities.length) {
//                 container.innerHTML = `
//                     <div class="text-center py-8">
//                         <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
//                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z" />
//                         </svg>
//                         <h3 class="mt-2 text-sm font-medium text-gray-900">No hay actividades este mes</h3>
//                     </div>
//                 `;
//                 return;
//             }
            
//             // Agrupar actividades por fecha
//             const groupedActivities = activities.reduce((groups, activity) => {
//                 const date = activity.date;
//                 if (!groups[date]) {
//                     groups[date] = [];
//                 }
//                 groups[date].push(activity);
//                 return groups;
//             }, {});
            
//             // Ordenar fechas
//             const sortedDates = Object.keys(groupedActivities).sort();
            
//             let html = '';
//             sortedDates.forEach(date => {
//                 const dayActivities = groupedActivities[date];
//                 html += `
//                     <div class="mb-6">
//                         <h4 class="font-semibold text-gray-800 mb-3 border-b pb-2">
//                             ${this.formatDate(date)}
//                         </h4>
//                         <div class="space-y-3">
//                 `;
                
//                 dayActivities.forEach(activity => {
//                     html += `
//                         <div class="border rounded-lg p-3 hover:shadow-md transition-all bg-white ${this.getTypeBorderClass(activity.type)}">
//                             <div class="flex items-center mb-2">
//                                 <div class="w-2 h-2 rounded-full mr-2 ${this.getTypeColorClass(activity.type)}"></div>
//                                 <h5 class="font-semibold text-gray-800 text-sm">${activity.title || 'Sin título'}</h5>
//                                 <span class="ml-auto text-xs px-2 py-1 rounded-full ${this.getTypeTagClass(activity.type)}">
//                                     ${this.getTypeLabel(activity.type)}
//                                 </span>
//                             </div>
                            
//                             <div class="text-xs text-gray-500 space-y-1">
//                                 ${activity.start_time ? `
//                                 <div class="flex items-center">
//                                     <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
//                                     </svg>
//                                     ${activity.start_time.substring(0,5)}${activity.end_time ? ' - ' + activity.end_time.substring(0,5) : ''}
//                                 </div>
//                                 ` : ''}
                                
//                                 ${activity.location ? `
//                                 <div class="flex items-center">
//                                     <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
//                                     </svg>
//                                     ${activity.location}
//                                 </div>
//                                 ` : ''}
                                
//                                 ${activity.description ? `
//                                 <div class="text-gray-600 text-xs mt-2">${activity.description}</div>
//                                 ` : ''}
//                             </div>
                            
//                             <div class="mt-2 flex gap-1">
//                                 <button onclick="editActivity(${activity.id}); document.getElementById('all-activities-modal').remove();" 
//                                         class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
//                                     Editar
//                                 </button>
//                                 <button onclick="managePhotos(${activity.id})" 
//                                         class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">
//                                     Fotos
//                                 </button>
//                             </div>
//                         </div>
//                     `;
//                 });
                
//                 html += `
//                         </div>
//                     </div>
//                 `;
//             });
            
//             container.innerHTML = html;
//         },
        
//         updateUpcomingActivities() {
//             if (!this.activities || !Array.isArray(this.activities)) return;
            
//             const now = new Date();
//             const upcoming = this.activities
//                 .filter(activity => {
//                     if (!activity.date) return false;
                    
//                     // Crear fecha de actividad
//                     const activityDate = new Date(activity.date);
//                     if (activity.start_time) {
//                         const [hours, minutes] = activity.start_time.split(':');
//                         activityDate.setHours(parseInt(hours), parseInt(minutes));
//                     }
                    
//                     return activityDate >= now && activity.status !== 'cancelled';
//                 })
//                 .sort((a, b) => {
//                     const dateA = new Date(a.date);
//                     const dateB = new Date(b.date);
//                     if (a.start_time && b.start_time) {
//                         const [hoursA, minutesA] = a.start_time.split(':');
//                         const [hoursB, minutesB] = b.start_time.split(':');
//                         dateA.setHours(parseInt(hoursA), parseInt(minutesA));
//                         dateB.setHours(parseInt(hoursB), parseInt(minutesB));
//                     }
//                     return dateA - dateB;
//                 })
//                 .slice(0, 5);
                
//             this.renderUpcomingActivities(upcoming);
//         },
        
//         renderUpcomingActivities(activities) {
//             const container = document.getElementById('upcoming-activities-list');
//             if (!container) return;
            
//             if (!activities.length) {
//                 container.innerHTML = `
//                     <div class="text-center py-8">
//                         <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
//                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z" />
//                         </svg>
//                         <h3 class="mt-2 text-sm font-medium text-gray-900">No hay actividades programadas</h3>
//                     </div>
//                 `;
//                 return;
//             }

//             container.innerHTML = activities.map(activity => `
//                 <div class="border rounded-lg p-3 hover:shadow-md transition-all bg-white ${this.getTypeBorderClass(activity.type)}">
//                     <div class="flex items-center mb-2">
//                         <div class="w-2 h-2 rounded-full mr-2 ${this.getTypeColorClass(activity.type)}"></div>
//                         <h4 class="font-semibold text-gray-800 text-sm">${activity.title || 'Sin título'}</h4>
//                     </div>
                    
//                     <div class="text-xs text-gray-500 space-y-1">
//                         <div class="flex items-center">
//                             <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"></path>
//                             </svg>
//                             ${this.formatDate(activity.date)} ${activity.start_time ? '- ' + activity.start_time.substring(0,5) : ''}
//                         </div>
                        
//                         ${activity.location ? `
//                         <div class="flex items-center">
//                             <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
//                             </svg>
//                             ${activity.location}
//                         </div>
//                         ` : ''}
//                     </div>
                    
//                     <div class="mt-2 flex gap-1">
//                         <button onclick="editActivity(${activity.id})" 
//                                 class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
//                             Editar
//                         </button>
//                         <button onclick="managePhotos(${activity.id})" 
//                                 class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">
//                             Fotos
//                         </button>
//                     </div>
//                 </div>
//             `).join('');
//         },
        
//         getTypeColorClass(type) {
//             const colors = {
//                 'recreational': 'bg-blue-500',
//                 'educational': 'bg-green-500',
//                 'cultural': 'bg-purple-500',
//                 'sports': 'bg-orange-500',
//                 'social': 'bg-pink-500'
//             };
//             return colors[type] || 'bg-gray-500';
//         },
        
//         getTypeBorderClass(type) {
//             const colors = {
//                 'recreational': 'border-l-4 border-l-blue-500',
//                 'educational': 'border-l-4 border-l-green-500',
//                 'cultural': 'border-l-4 border-l-purple-500',
//                 'sports': 'border-l-4 border-l-orange-500',
//                 'social': 'border-l-4 border-l-pink-500'
//             };
//             return colors[type] || 'border-l-4 border-l-gray-500';
//         },
        
//         getTypeTagClass(type) {
//             const colors = {
//                 'recreational': 'bg-blue-100 text-blue-800',
//                 'educational': 'bg-green-100 text-green-800',
//                 'cultural': 'bg-purple-100 text-purple-800',
//                 'sports': 'bg-orange-100 text-orange-800',
//                 'social': 'bg-pink-100 text-pink-800'
//             };
//             return colors[type] || 'bg-gray-100 text-gray-800';
//         },
        
//         getTypeLabel(type) {
//             const labels = {
//                 'recreational': 'Recreativa',
//                 'educational': 'Educativa',
//                 'cultural': 'Cultural',
//                 'sports': 'Deportiva',
//                 'social': 'Social'
//             };
//             return labels[type] || 'Otro';
//         },
        
//         formatDate(dateString) {
//             if (!dateString) return '';
            
//             // Parsear la fecha manualmente para evitar problemas de timezone
//             const [year, month, day] = dateString.split('-').map(num => parseInt(num));
            
//             // Crear fecha usando los componentes individuales (mes es 0-indexed)
//             const date = new Date(year, month - 1, day);
            
//             return date.toLocaleDateString('es-ES', {
//                 weekday: 'long',
//                 day: '2-digit',
//                 month: 'long',
//                 year: 'numeric'
//             });
//         }
//     }
// }

// // Exponer globalmente para debugging
// window.sidebarInstance = null;

// // Refrescar el sidebar cuando se crea una nueva actividad
// document.addEventListener('activity-created', function() {
//     if (window.sidebarInstance) {
//         window.sidebarInstance.loadActivities();
//     }
// });

// document.addEventListener('alpine:init', () => {
//     // Guardar referencia al sidebar
//     document.addEventListener('alpine:initialized', () => {
//         const sidebarElement = document.querySelector('[x-data*="activitiesSidebar"]');
//         if (sidebarElement && sidebarElement._x_dataStack) {
//             window.sidebarInstance = sidebarElement._x_dataStack[0];
//         }
//     });
// });