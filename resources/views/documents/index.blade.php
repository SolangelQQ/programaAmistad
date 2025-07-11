@extends('layouts.app')

@section('content')
<div class="w-full mx-auto" style="max-width: 95%" x-data="documentManager()">
    <!-- Header Section -->
    @include('documents.partials.header')

    <!-- Main Content -->
    <div class="max-full mx-auto py-8">
        <!-- Documents List -->
        <div class="w-full bg-white shadow rounded-lg">
            @forelse($documents as $document)
                @include('documents.partials.document-item', ['document' => $document])
            @empty
                @include('documents.partials.empty-state')
            @endforelse
        </div>

        <!-- Pagination -->
        @if($documents->hasPages())
            <div class="mt-6">
                {{ $documents->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Modals -->
    @include('documents.modals.create')
    @include('documents.modals.details')
</div>

<script>
function documentManager() {
    return {
        // Modal states
        showCreateModal: false,
        showDetailsModal: false,
        
        // Data properties
        documentDetails: null,
        
        // Methods
        async showDocumentDetails(documentId) {
            try {
                const response = await fetch(`/documents/${documentId}`);
                const data = await response.json();
                
                this.documentDetails = data;
                this.showDetailsModal = true;
            } catch (error) {
                console.error('Error fetching document details:', error);
                alert('Error al cargar los detalles del documento');
            }
        }
    }
}
</script>
@endsection