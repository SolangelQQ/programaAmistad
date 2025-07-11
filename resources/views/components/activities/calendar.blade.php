<div class="lg:col-span-2 bg-white p-4 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Calendario de Actividades</h2>
        <div class="flex items-center space-x-2">
            <button id="prev-month" class="p-2 hover:bg-gray-100 rounded">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <h2 id="current-month" class="text-lg font-semibold min-w-32 text-center"></h2>
            <button id="next-month" class="p-2 hover:bg-gray-100 rounded">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>
    
    <div class="mb-6">
        <div class="grid grid-cols-7 gap-1 text-center text-sm font-medium text-gray-500 mb-2">
            <div>DOM</div>
            <div>LUN</div>
            <div>MAR</div>
            <div>MIE</div>
            <div>JUE</div>
            <div>VIE</div>
            <div>SAB</div>
        </div>
        <div id="calendar-grid" class="grid grid-cols-7 gap-1"></div>
    </div>
    
    <!-- Calendar Details -->
    <div class="border-t pt-4">
        <h3 id="selected-date" class="font-semibold mb-2">Selecciona una fecha</h3>
        <div id="day-activities" class="space-y-2">
            <div class="text-gray-500 text-sm">No hay actividades para esta fecha</div>
        </div>
    </div>
    
    <!-- Quick Add Activity Button -->
    <div class="mt-4 pt-4 border-t">
        <button id="quick-add-activity" 
                class="w-full bg-blue-50 text-blue-600 py-2 px-4 rounded-md hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 border border-blue-200">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Agregar actividad para esta fecha
        </button>
    </div>
</div>
