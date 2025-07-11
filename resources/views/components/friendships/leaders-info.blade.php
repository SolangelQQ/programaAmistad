{{-- programaAmistad/resources/views/components/friendship/leaders-info.blade.php --}}

<!-- Información de Líderes -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
    <!-- Líder de Buddy -->
    <div class="bg-purple-50 rounded-lg p-4 shadow-sm">
        <h3 class="text-md font-semibold text-purple-800 mb-3">
            {{ __('Líder de Buddy') }}
        </h3>
        <div class="space-y-1">
            <p class="text-sm">
                <span class="font-medium text-purple-600">{{ __('Nombre:') }}</span> 
                <span id="view_buddy_leader_name" class="text-gray-700"></span>
            </p>
            <p class="text-sm">
                <span class="font-medium text-purple-600">{{ __('Email:') }}</span> 
                <span id="view_buddy_leader_email" class="text-gray-700"></span>
            </p>
        </div>
    </div>
    
    <!-- Líder de PeerBuddy -->
    <div class="bg-orange-50 rounded-lg p-4 shadow-sm">
        <h3 class="text-md font-semibold text-orange-800 mb-3">
            {{ __('Líder de PeerBuddy') }}
        </h3>
        <div class="space-y-1">
            <p class="text-sm">
                <span class="font-medium text-orange-600">{{ __('Nombre:') }}</span> 
                <span id="view_peerbuddy_leader_name" class="text-gray-700"></span>
            </p>
            <p class="text-sm">
                <span class="font-medium text-orange-600">{{ __('Email:') }}</span> 
                <span id="view_peerbuddy_leader_email" class="text-gray-700"></span>
            </p>
        </div>
    </div>
</div>