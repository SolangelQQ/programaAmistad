{{-- programaAmistad/resources/views/modals/friendships/view.blade.php --}}

<!-- Modal para ver detalles del Emparejamiento -->
<div id="view-friendship-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-6xl max-h-[90vh] overflow-y-auto">
        <div id="modal-content">
            
            @include('components.friendships.modal-header')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                @include('components.friendships.buddy-info-card')
                @include('components.friendships.peerbuddy-info-card')
            </div>
            
            @include('components.friendships.leaders-info')
            
            @include('components.friendships.friendship-details')
            
            @include('components.friendships.follow-up-section')
            
            @include('components.friendships.attendance-section')
            
            @include('components.friendships.modal-actions')
            
        </div>
    </div>
</div>

<script src="{{ asset('js/friendshipModal.js') }}"></script>
