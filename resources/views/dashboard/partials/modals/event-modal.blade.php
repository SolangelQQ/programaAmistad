<div x-data="eventModalComponent()" 
     x-show="isOpen" 
     x-cloak
     @open-event-modal.window="isOpen = true"
     @click.self="isOpen = false"
     class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-xl">
    </div>
</div>

@push('scripts')
<script>
    function eventModalComponent() {
        return {
            isOpen: false,
            title: '',
            date: new Date().toISOString().split('T')[0],
            time: '12:00',
            description: '',
            
            submitEvent() {
                if (!gapiInited || !gisInited) {
                    window.dispatchEvent(new CustomEvent('open-auth-modal'));
                    return;
                }
                
                const eventDateTime = new Date(`${this.date}T${this.time}:00`);
                const eventDateString = eventDateTime.toLocaleString('es-BO', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    timeZone: 'America/La_Paz'
                });
                
                createCalendarEvent(this.title, eventDateTime, this.description)
                    .then(() => {
                        alert(`Actividad "${this.title}" programada para el ${eventDateString}`);
                        this.resetForm();
                        this.isOpen = false;
                    })
                    .catch(error => {
                        console.error('Error al crear el evento:', error);
                        alert('Error al crear el evento. Por favor intente nuevamente.');
                    });
            },
            
            resetForm() {
                this.title = '';
                this.date = new Date().toISOString().split('T')[0];
                this.time = '12:00';
                this.description = '';
            }
        }
    }
</script>
@endpush