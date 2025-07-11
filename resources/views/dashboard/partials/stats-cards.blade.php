<!-- components/stat-card.blade.php -->
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
            <p class="text-2xl font-semibold text-gray-900">{{ number_format($value) }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <!-- Indicador de tendencia -->
            @if(isset($trend) && isset($percentage))
                <div class="flex items-center text-sm">
                    @if($trend === 'up' && $percentage > 0)
                        <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l5-5 5 5M7 7l5-5 5 5"></path>
                        </svg>
                        <span class="text-green-600 font-medium">+{{ $percentage }}%</span>
                    @elseif($trend === 'down' && $percentage > 0)
                        <svg class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-5 5-5-5m10 10l-5-5-5 5"></path>
                        </svg>
                        <span class="text-red-600 font-medium">-{{ $percentage }}%</span>
                    @else
                        <!-- Sin cambios (0%) -->
                        <svg class="w-4 h-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"></path>
                        </svg>
                        <span class="text-gray-500 font-medium">{{ $percentage }}%</span>
                    @endif
                </div>
            @else
                <!-- Fallback para compatibilidad con código existente -->
                <span class="text-sm text-gray-500">{{ $percentage ?? '0' }}%</span>
            @endif
            
            <!-- Icono principal -->
            <div class="p-2 bg-{{ $color }}/10 rounded-lg">
                <svg class="w-6 h-6 text-{{ $color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {{ $icon }}
                </svg>
            </div>
        </div>
    </div>
    
    <!-- Texto adicional de contexto -->
    @if(isset($trend) && isset($percentage))
        <div class="mt-3">
            <p class="text-xs text-gray-500">
                @if($trend === 'up' && $percentage > 0)
                    Incremento respecto al mes anterior
                @elseif($trend === 'down' && $percentage > 0)
                    Disminución respecto al mes anterior
                @else
                    Sin cambios respecto al mes anterior
                @endif
            </p>
        </div>
    @endif
</div>