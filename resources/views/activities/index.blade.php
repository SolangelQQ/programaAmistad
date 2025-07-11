@extends('layouts.app')

@section('content')
<div x-data="activityManager()" class="container mx-auto px-4 py-6">
    @include('components.activities.section')
    @include('modals.activities.create')
    @include('modals.activities.edit')
    @include('modals.activities.photos')
</div>

<script>
function activityManager() {
    return {
        currentYear: new Date().getFullYear(),
        currentMonth: new Date().getMonth() + 1,
        selectedDate: null,
        activities: {},
        dayActivities: [],
        editingActivity: null,
        
        init() {
            this.loadCalendarData();
        },
        
        async loadCalendarData() {
            try {
                const response = await fetch(`/api/activities/calendar?year=${this.currentYear}&month=${this.currentMonth}`);
                const data = await response.json();
                this.activities = data.activities;
                this.updateCalendarDisplay(data);
            } catch (error) {
                console.error('Error loading calendar:', error);
            }
        },
        
        async loadDayActivities(date) {
            try {
                const response = await fetch(`/api/activities/day?date=${date}`);
                this.dayActivities = await response.json();
                this.selectedDate = date;
            } catch (error) {
                console.error('Error loading day activities:', error);
            }
        },
        
        async saveActivity(formData) {
            try {
                const response = await fetch('/activities', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                if (result.success) {
                    this.closeNewActivityModal();
                    this.loadCalendarData();
                    this.showNotification('Actividad creada exitosamente', 'success');
                }
                return result;
            } catch (error) {
                console.error('Error saving activity:', error);
                this.showNotification('Error al guardar la actividad', 'error');
            }
        },
        
        async updateActivity(id, formData) {
            try {
                const response = await fetch(`/activities/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                if (result.success) {
                    this.closeEditModal();
                    this.loadCalendarData();
                    this.showNotification('Actividad actualizada exitosamente', 'success');
                }
                return result;
            } catch (error) {
                console.error('Error updating activity:', error);
                this.showNotification('Error al actualizar la actividad', 'error');
            }
        },
        
        async deleteActivity(id) {
            if (!confirm('¿Estás seguro de que quieres eliminar esta actividad?')) return;
            
            try {
                const response = await fetch(`/activities/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                if (result.success) {
                    this.loadCalendarData();
                    this.showNotification('Actividad eliminada exitosamente', 'success');
                }
            } catch (error) {
                console.error('Error deleting activity:', error);
                this.showNotification('Error al eliminar la actividad', 'error');
            }
        },
        
        changeMonth(direction) {
            if (direction === 'prev') {
                this.currentMonth--;
                if (this.currentMonth < 1) {
                    this.currentMonth = 12;
                    this.currentYear--;
                }
            } else {
                this.currentMonth++;
                if (this.currentMonth > 12) {
                    this.currentMonth = 1;
                    this.currentYear++;
                }
            }
            this.loadCalendarData();
        },
        
        updateCalendarDisplay(data) {
            // Actualizar el display del mes y año
            document.getElementById('calendar-month-year').textContent = data.month;
        },
        
        showNotification(message, type) {
            // Implementar sistema de notificaciones
            alert(message);
        },
        
        openNewActivityModal() {
            document.getElementById('new-activity-modal').classList.remove('hidden');
        },
        
        closeNewActivityModal() {
            document.getElementById('new-activity-modal').classList.add('hidden');
            document.getElementById('new-activity-form').reset();
        },
        
        openEditModal(activity) {
            this.editingActivity = activity;
            document.getElementById('edit-activity-modal').classList.remove('hidden');
            this.fillEditForm(activity);
        },
        
        closeEditModal() {
            document.getElementById('edit-activity-modal').classList.add('hidden');
            this.editingActivity = null;
        },
        
        fillEditForm(activity) {
            document.getElementById('edit_activity_title').value = activity.title;
            document.getElementById('edit_activity_description').value = activity.description || '';
            document.getElementById('edit_activity_location').value = activity.location;
            document.getElementById('edit_activity_date').value = activity.date;
            document.getElementById('edit_activity_start_time').value = activity.start_time;
            document.getElementById('edit_activity_end_time').value = activity.end_time || '';
            document.getElementById('edit_activity_status').value = activity.status;
        }
    }
}

// Funciones globales para mantener compatibilidad
function openNewActivityModal() {
    document.querySelector('[x-data]').__x.$data.openNewActivityModal();
}

function closeNewActivityModal() {
    document.querySelector('[x-data]').__x.$data.closeNewActivityModal();
}
</script>
@endsection