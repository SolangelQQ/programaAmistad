<div class="mb-8" x-data="calendarComponent()">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Calendario</h2>
    </div>
    
    <div id="calendar-container" class="h-[600px] rounded-lg bg-blue-50 overflow-hidden shadow-sm">
        <iframe class="w-full h-full border-0" 
                src="https://calendar.google.com/calendar/embed?height=600&wkst=1&bgcolor=%23e5f0ff&ctz=America%2FLa_Paz&src=es.bo%23holiday%40group.v.calendar.google.com&color=%230B8043" 
                frameborder="0" 
                scrolling="no">
        </iframe>
    </div>
</div>

@push('scripts')
<script>
    function calendarComponent() {
        return {
            openEventModal() {
                window.dispatchEvent(new CustomEvent('open-event-modal'));
            }
        }
    }
</script>
@endpush