{{-- programaAmistad/resources/views/components/friendship/follow-up-footer.blade.php --}}

<div class="mt-4 pt-3 border-t border-gray-100">
    <div class="flex justify-between items-center text-sm text-gray-500">
        <span>
            {{ __('Realizado por:') }} 
            <span id="follow_up_user" class="font-medium"></span>
        </span>
        <span>
            {{ __('Fecha:') }} 
            <span id="follow_up_date" class="font-medium"></span>
        </span>
    </div>
    <div class="mt-2" id="next_follow_up_info">
        <!-- Información sobre próximo seguimiento -->
    </div>
</div>