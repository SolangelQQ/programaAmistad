<div class="border-b border-gray-200 p-6 hover:bg-gray-50 transition-colors duration-200">
    <div class="flex items-center justify-between">
        <!-- Document Info -->
        <div class="flex-1">
            <div class="flex items-center space-x-3">
                <!-- File Icon -->
                <div class="flex-shrink-0">
                    @if($document->file_type == 'pdf')
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    @elseif(in_array($document->file_type, ['doc', 'docx']))
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    @else
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    @endif
                </div>
                
                <!-- Document Details -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2">
                        <h3 class="text-lg font-medium text-gray-900 truncate">{{ $document->title }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $document->category }}
                        </span>
                        @if($document->chapter)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $document->chapter }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                        <span>{{ strtoupper($document->file_type) }} • {{ $document->formatted_file_size }}</span>
                        <span>•</span>
                        <span>Actualizado: {{ $document->updated_at->format('d/m/Y') }}</span>
                        @if($document->uploader)
                            <span>•</span>
                            <div class="flex items-center space-x-1">
                                <div class="w-4 h-4 rounded-full bg-gray-300"></div>
                                <span>{{ $document->uploader->name }}</span>
                            </div>
                        @endif
                    </div>
                    
                    @if($document->description)
                        <p class="mt-2 text-sm text-gray-600">{{ Str::limit($document->description, 100) }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-2">
            <!-- View Details -->
            <button @click="showDocumentDetails({{ $document->id }})"
                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>

            <!-- Download -->
            <a href="{{ route('documents.download', $document) }}"
               class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </a>

            <!-- Delete -->
            <form method="POST" action="{{ route('documents.destroy', $document) }}" 
                  onsubmit="return confirm('¿Estás seguro de eliminar este documento?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>