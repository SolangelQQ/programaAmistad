@props([
    'title',
    'value',
    'percentage',
    'color',
    'icon'
])

<div class="stat-card bg-white p-6 flex flex-col rounded-lg shadow-md hover:shadow-lg transition-shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-semibold text-gray-700">{{ $title }}</h3>
        <div class="bg-{{ $color }} p-2 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                {!! $icon !!}
            </svg>
        </div>
    </div>
    <div class="flex items-end justify-between">
        <span class="text-4xl font-bold">{{ $value }}</span>
        <div class="flex flex-col items-end">
            <span class="text-{{ $color }} font-bold">{{ $percentage }}%</span>
            <span class="text-xs text-gray-500">vs mes anterior</span>
        </div>
    </div>
</div>