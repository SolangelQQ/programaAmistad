<form method="GET" action="{{ route('documents.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
    

<!-- Search by title -->
    
    <div class="w-full relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="Buscar documento por título"
               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
    </div>

    <!-- Category -->
    <select name="category" class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        <option value="">Todas las categorías</option>
        @foreach($categories as $category)
            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                {{ $category }}
            </option>
        @endforeach
    </select>

    <!-- Date -->
    <input type="date" name="date" value="{{ request('date') }}" 
           class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">

    <!-- Chapter -->
    <!-- <select name="chapter" class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        <option value="">Todos los capítulos</option>
        @foreach($chapters as $chapter)
            <option value="{{ $chapter }}" {{ request('chapter') == $chapter ? 'selected' : '' }}>
                {{ $chapter }}
            </option>
        @endforeach
    </select> -->

    <!-- Filter button -->
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
        Filtrar
    </button>
</form>