
{{-- Componente para etiquetas de estado --}}
@props(['status', 'classes' => ''])

@php
    $statusClasses = [
        'Emparejado' => 'bg-green-100 text-green-800',
        'Inactivo' => 'bg-yellow-100 text-yellow-800',
        'Finalizado' => 'bg-gray-100 text-gray-800',
        'buddy' => 'bg-blue-100 text-blue-800',
        'peerbuddy' => 'bg-green-100 text-green-800',
        'default' => 'bg-gray-100 text-gray-800'
    ];
    
    $statusClass = $statusClasses[$status] ?? $statusClasses['default'];
@endphp

<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }} {{ $classes }}">
    {{ $slot }}
</span>