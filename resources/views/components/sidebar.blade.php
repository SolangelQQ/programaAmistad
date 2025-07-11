<div x-data="{
        sidebarOpen: window.innerWidth >= 768,
        expanded: document.cookie.includes('sidebar_expanded=false') ? false : true,
        toggleExpanded() {
            this.expanded = !this.expanded;
            const date = new Date();
            date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000)); // Expira en 1 año
            document.cookie = `sidebar_expanded=${this.expanded}; expires=${date.toUTCString()}; path=/`;
            
            window.dispatchEvent(new CustomEvent('sidebar-toggled', { 
                detail: { expanded: this.expanded }
            }));
        }
    }" 
     @resize.window="sidebarOpen = window.innerWidth >= 768">
    
    <!-- Botón hamburguesa para móviles -->
    <button 
        @click="sidebarOpen = !sidebarOpen" 
        x-show="!sidebarOpen" 
        class="md:hidden fixed ml-2 mt-6 z-50 text-black rounded-lg"
        x-transition>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
    </button>
    
    <!-- Sidebar -->
    <div x-show="sidebarOpen" @click.away="sidebarOpen = window.innerWidth >= 768"
         class="h-full fixed inset-y-0 left-0 z-40 bg-blue-700 text-white shadow-lg  transition-all duration-300 h-full"
         :class="{
             'w-64': expanded,
             'w-16': !expanded,
             'fixed': !sidebarOpen,
             'relative': sidebarOpen && window.innerWidth >= 768
         }"
         x-transition:enter="transition ease-out duration-200 transform"
         x-transition:enter-start="-translate-x-full" 
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0" 
         x-transition:leave-end="-translate-x-full">
                
        <!-- Sidebar content -->
        <div class="h-full bg-blue-700 flex flex-col">
            <!-- Botón de contraer el sidebar con flechitas en la esquina superior derecha -->
            <div class="px-4 py-2 flex justify-end">
                <button @click="toggleExpanded()" class="text-white hover:text-blue-200 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              :d="expanded ? 'M11 19l-7-7 7-7m8 14l-7-7 7-7' : 'M13 5l7 7-7 7M5 5l7 7-7 7'" />
                    </svg>
                </button>
            </div>

            <!-- Logo and app name -->
            <div class="flex flex-col items-center justify-center p-4 mt-4">
                <div class="flex items-center justify-center">
                    <div class="text-blue-700">
                        <img src="{{ asset('logo.jpeg') }}" alt="Best Buddies Bolivia Logo" class="w-24 h-24 object-contain">
                    </div>
                </div>
                <div x-show="expanded" class="text-center mt-4 transition-all duration-300">
                    <h1 class="text-xl font-bold">BestBuddies</h1>
                    <p class="text-xs text-blue-200">Gestión de amistades</p>
                </div>
            </div>
            
            <!-- Menu items -->
            <nav class="mt-8 p-0 space-y-8">
                <!-- Dashboard -->
                @php
                    $isActive = request()->routeIs('dashboard');
                @endphp

                <a href="{{ route('dashboard') }}" 
                    @class([
                        'relative flex items-center transition-all group p-2',
                        'bg-white text-blue-700' => $isActive,
                        'text-white hover:bg-blue-600' => ! $isActive,
                    ])
                    :class="expanded ? 'space-x-3' : 'justify-center rounded-full p-0'"
                    :style="expanded
                        ? 'border-top-left-radius: 0; border-bottom-left-radius: 0; border-top-right-radius: 9999px; border-bottom-right-radius: 9999px;' 
                        : ''">

                    <div class="p-2">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                    </div>
                    <span x-show="expanded">Panel Principal</span>
                    <div x-show="!expanded" class="absolute left-full ml-2 bg-blue-800 text-white px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none text-sm whitespace-nowrap">
                        Panel Principal
                    </div>
                </a>
                
                <!-- Emparejamiento -->
                @php
                    $isActive = request()->routeIs('friendships.index');
                @endphp

                <a href="{{ route('friendships.index') }}"
                    @class([
                        'relative flex items-center transition-all group p-2',
                        'bg-white text-blue-700' => $isActive,
                        'text-white hover:bg-blue-600' => ! $isActive,
                    ])
                    :class="expanded ? 'space-x-3' : 'justify-center rounded-full p-0'"
                    :style="expanded 
                        ? 'border-top-left-radius: 0; border-bottom-left-radius: 0; border-top-right-radius: 9999px; border-bottom-right-radius: 9999px;' 
                        : ''">

                    <div class="p-2">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                        </svg>
                    </div>

                    <div x-show="expanded" class="mr-5">
                        <span>Emparejamiento<br>de amistades &<br>Actividades</span>
                    </div>

                    {{-- Tooltip cuando está contraído --}}
                    <div x-show="!expanded" 
                        class="absolute left-full ml-2 bg-blue-800 text-white px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none text-sm whitespace-nowrap">
                        Emparejamiento de amistades & Actividades
                    </div>
                </a>
                <!-- Reportes -->
                @php
                    $isActive = request()->routeIs('reports.index');
                @endphp

                <a href="{{ route('reports.index') }}"
                    @class([
                        'relative flex items-center transition-all group p-2',
                        'bg-white text-blue-700' => $isActive,
                        'text-white hover:bg-blue-600' => ! $isActive,
                    ])
                    :class="expanded ? 'space-x-3' : 'justify-center rounded-full p-0'"
                    :style="expanded 
                        ? 'border-top-left-radius: 0; border-bottom-left-radius: 0; border-top-right-radius: 9999px; border-bottom-right-radius: 9999px;' 
                        : ''">

                    <div class="p-2">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                        </svg>
                    </div>

                    <span x-show="expanded">Reportes</span>

                    <div x-show="!expanded" class="absolute left-full ml-2 bg-blue-800 text-white px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none text-sm whitespace-nowrap">
                        Reportes
                    </div>
                </a>

                <!-- Documentos -->
                @php
                    $isActive = request()->routeIs('documents.index');
                @endphp

                <a href="{{ route('documents.index') }}"
                    @class([
                        'relative flex items-center transition-all group p-2',
                        'bg-white text-blue-700' => $isActive,
                        'text-white hover:bg-blue-600' => ! $isActive,
                    ])
                    :class="expanded ? 'space-x-3' : 'justify-center rounded-full p-0'"
                    :style="expanded 
                        ? 'border-top-left-radius: 0; border-bottom-left-radius: 0; border-top-right-radius: 9999px; border-bottom-right-radius: 9999px;' 
                        : ''">

                    <div class="p-2">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                        </svg>
                    </div>

                    <span x-show="expanded">Documentos</span>

                    <div x-show="!expanded" class="absolute left-full ml-2 bg-blue-800 text-white px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none text-sm whitespace-nowrap">
                        Documentos
                    </div>
                </a>

                <!-- Roles -->
                @php
                    $isActive = request()->routeIs('roles.*');
                @endphp

                <a href="{{ route('roles.index') }}" 
                    @class([
                        'relative flex items-center transition-all group p-2',
                        'bg-white text-blue-700' => $isActive,
                        'text-white hover:bg-blue-600' => ! $isActive,
                    ])
                    :class="expanded ? 'space-x-3' : 'justify-center rounded-full p-0'"
                    :style="expanded 
                        ? 'border-top-left-radius: 0; border-bottom-left-radius: 0; border-top-right-radius: 9999px; border-bottom-right-radius: 9999px;' 
                        : ''">

                    <div class="p-2">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                        </svg>
                    </div>

                    <span x-show="expanded">Roles</span>

                    <div x-show="!expanded" 
                        class="absolute left-full ml-2 bg-blue-800 text-white px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none text-sm whitespace-nowrap">
                        Roles
                    </div>
                </a>
            </nav>
        </div>
    </div>
</div>