{{-- programaAmistad/resources/views/components/friendship/follow-up-details.blade.php --}}

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
    <div>
        <p class="font-medium text-gray-700 mb-1">{{ __('Logros Alcanzados:') }}</p>
        <p id="goals_achieved" class="text-gray-600"></p>
    </div>
    <div>
        <p class="font-medium text-gray-700 mb-1">{{ __('Desafíos Enfrentados:') }}</p>
        <p id="challenges_faced" class="text-gray-600"></p>
    </div>
    <div>
        <p class="font-medium text-gray-700 mb-1">{{ __('Recomendaciones:') }}</p>
        <p id="recommendations" class="text-gray-600"></p>
    </div>
    <div>
        <p class="font-medium text-gray-700 mb-1">{{ __('Próximos Pasos:') }}</p>
        <p id="next_steps" class="text-gray-600"></p>
    </div>
</div>