<form method="GET" action="{{ route('documents.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <!-- Search by title -->
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Buscar documento por título"
               class="block w-full h-10 pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
    </div>

    <!-- Category -->
    <select name="category" class="block w-full h-10 px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        <option value="">Todas las categorías</option>
        @foreach($categories as $category)
            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                {{ $category }}
            </option>
        @endforeach
    </select>

    <!-- Date -->
    <input type="date" name="date" value="{{ request('date') }}"
           class="block w-full h-10 px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">

    <!-- Filter button -->
    <button type="submit" class="h-10 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <span class="flex items-center justify-center">
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Filtrar
        </span>
    </button>
</form>