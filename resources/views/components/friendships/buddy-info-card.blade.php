{{-- programaAmistad/resources/views/components/friendship/buddy-info-card.blade.php --}}

<!-- Buddy Information -->
<div class="bg-blue-50 rounded-lg p-6 shadow-sm">
    <div class="flex items-center mb-4">
        <img class="h-16 w-16 rounded-full object-cover border-2 border-blue-200" 
             src="https://i.pravatar.cc/300?u=buddy" 
             alt="{{ __('Avatar del Buddy') }}">
        <div class="ml-4">
            <h3 class="text-lg font-semibold text-blue-800">{{ __('Buddy') }}</h3>
            <p id="view_buddy_name" class="text-base font-medium text-gray-900"></p>
        </div>
    </div>
    
    <div class="mt-3 space-y-2">
        <div class="flex items-start">
            <span class="text-blue-600 font-medium w-32">{{ __('Discapacidad:') }}</span>
            <span id="view_buddy_disability" class="text-gray-700"></span>
        </div>
        <div class="flex items-start">
            <span class="text-blue-600 font-medium w-32">{{ __('Edad:') }}</span>
            <span id="view_buddy_age" class="text-gray-700"></span>
        </div>
        <div class="flex items-start">
            <span class="text-blue-600 font-medium w-32">{{ __('CI:') }}</span>
            <span id="view_buddy_ci" class="text-gray-700"></span>
        </div>
        <div class="flex items-start">
            <span class="text-blue-600 font-medium w-32">{{ __('Teléfono:') }}</span>
            <span id="view_buddy_phone" class="text-gray-700"></span>
        </div>
        <div class="flex items-start">
            <span class="text-blue-600 font-medium w-32">{{ __('Email:') }}</span>
            <span id="view_buddy_email" class="text-gray-700"></span>
        </div>
        <div class="flex items-start">
            <span class="text-blue-600 font-medium w-32">{{ __('Dirección:') }}</span>
            <span id="view_buddy_address" class="text-gray-700"></span>
        </div>
    </div>
</div>