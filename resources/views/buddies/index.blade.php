@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white">
                <!-- Header con título y botones -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Listado de Buddies y PeerBuddies</h1>
                        <p class="text-gray-600">Administra las personas registradas en el sistema</p>
                    </div>
                    <div class="flex space-x-2">
                        <!-- Botón Volver -->
                        <button type="button"
                                class="inline-flex items-center p-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                onclick="window.location='{{ route('friendships.index') }}'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Volver
                        </button>
                        
                        <!-- Botón Agregar Persona -->
                        <button type="button"
                                class="inline-flex items-center p-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                onclick="window.location='{{ route('buddies.create') }}'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            Agregar Persona
                        </button>
                    </div>
                </div>

                @include('buddies.partials.table')
            </div>
        </div>
    </div>
</div>

<!-- Modales -->
@include('buddies.partials.view-details-modal')
@include('buddies.partials.edit-modal')
@include('buddies.partials.delete-modal')

@endsection

@section('scripts')
<script src="{{ asset('js/buddies.js') }}"></script>
@endsection