<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Comunicación de actividades</h1>
        <p class="text-gray-600">Plan y gestiona actividades para las amistades</p>
    </div>
    
    <button type="button"
            class="inline-flex items-center p-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            onclick="openNewActivityModal()">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Nueva Actividad
    </button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="activitiesData()">
    @include('components.activities.calendar')  
    @include('components.activities.sidebar')
</div>

@include('modals.activities.create')
@include('modals.activities.edit')
@include('modals.activities.photos')