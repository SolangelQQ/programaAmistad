{{-- programaAmistad/resources/views/components/friendship/peerbuddy-info-card.blade.php --}}

<!-- PeerBuddy Information -->
<div class="bg-green-50 rounded-lg p-6 shadow-sm">
    <div class="flex items-center mb-4">
        <img class="h-16 w-16 rounded-full object-cover border-2 border-green-200" 
             src="https://i.pravatar.cc/300?u=peerbuddy" 
             alt="{{ __('Avatar del PeerBuddy') }}">
        <div class="ml-4">
            <h3 class="text-lg font-semibold text-green-800">{{ __('PeerBuddy') }}</h3>
            <p id="view_peerbuddy_name" class="text-base font-medium text-gray-900"></p>
        </div>
    </div>
    
    <div class="mt-3 space-y-2">
        <div class="flex items-start">
            <span class="text-green-600 font-medium w-32">{{ __('Edad:') }}</span>
            <span id="view_peerbuddy_age" class="text-gray-700"></span>
        </div>
        <div class="flex items-start">
            <span class="text-green-600 font-medium w-32">{{ __('CI:') }}</span>
            <span id="view_peerbuddy_ci" class="text-gray-700"></span>
        </div>
        <div class="flex items-start">
            <span class="text-green-600 font-medium w-32">{{ __('Teléfono:') }}</span>
            <span id="view_peerbuddy_phone" class="text-gray-700"></span>
        </div>
        <div class="flex items-start">
            <span class="text-green-600 font-medium w-32">{{ __('Email:') }}</span>
            <span id="view_peerbuddy_email" class="text-gray-700"></span>
        </div>
        <div class="flex items-start">
            <span class="text-green-600 font-medium w-32">{{ __('Dirección:') }}</span>
            <span id="view_peerbuddy_address" class="text-gray-700"></span>
        </div>
    </div>
</div>