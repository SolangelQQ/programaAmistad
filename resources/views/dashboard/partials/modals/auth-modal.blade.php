<div x-data="authModalComponent()" 
     x-show="isOpen" 
     x-cloak
     @open-auth-modal.window="isOpen = true"
     @click.self="isOpen = false"
     class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
</div>

@push('scripts')
<script>
    function authModalComponent() {
        return {
            isOpen: false,
            
            authenticate() {
                handleAuthClick();
                this.isOpen = false;
            }
        }
    }
</script>
@endpush