@props([
    'type' => 'button',
    'color' => 'blue',
    'icon' => null,
    'href' => null,
    'onclick' => null,
    'classes' => ''
])

@php
    $colorClasses = [
        'blue' => 'bg-blue-600 hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:ring-blue-500',
        'green' => 'bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500',
        'red' => 'bg-red-600 hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:ring-red-500',
        'purple' => 'bg-purple-600 hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:ring-purple-500',
        'gray' => 'bg-gray-200 hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:ring-gray-500 text-gray-800',
    ];
    
    $baseClasses = 'inline-flex items-center p-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 ' . $colorClasses[$color] . ' ' . $classes;
    
    // Si hay href, es un anchor, de lo contrario es un botón
    $element = $href ? 'a' : 'button';
    
    // Atributos para el elemento
    $attributes = $href ? ['href' => $href] : ['type' => $type];

    if ($onclick) {
        $attributes['onclick'] = $onclick;
    }
@endphp

<{{ $element }}
    @foreach($attributes as $attr => $value)
        {{ $attr }}="{{ $value }}"
    @endforeach
    class="{{ $baseClasses }}"
>
    @if($icon)
        <span class="mr-2">{!! $icon !!}</span>
    @endif
    {{ $slot }}
</{{ $element }}>