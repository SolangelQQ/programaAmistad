<div class="flex justify-between items-center mb-6 w-full">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Gestión de Amistades</h1>
        <p class="text-gray-600">Administra las relaciones entre buddies y peerbuddies</p>
    </div>
    
    <div class="flex space-x-2 items-center">
        <button type="button"
                class="h-10 p-2 inline-flex items-center bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150"
                onclick="window.location='{{ route('buddies.index') }}'">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-1a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v1h-3zM4.75 12.094A5.973 5.973 0 004 15v1H1v-1a3 3 0 013.75-2.906z" />
            </svg>
            Ver Personas
        </button>
    
        @include('components.friendships.actions')
    </div>
</div>

@include('components.friendships.filters')
@include('components.friendships.list')