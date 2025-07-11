<?php
?>
<div class="space-y-6" x-data="activitiesSidebar()">
    <!-- Activity Types -->
    <div class="bg-white p-4 rounded-lg shadow-sm border">
        <h3 class="font-semibold mb-3 text-gray-800">Tipos de Actividades</h3>
        <div class="space-y-2">
            <!-- Recreativas -->
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg cursor-pointer hover:bg-blue-100 transition-colors duration-200 border-l-4 border-blue-500" 
                 @click="filterByType('recreational')">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                    <span class="text-sm font-medium text-gray-700">Recreativas</span>
                </div>
                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium" 
                      x-text="getActivityCountByType('recreational')"></span>
            </div>
            
            <!-- Educativas -->
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg cursor-pointer hover:bg-green-100 transition-colors duration-200 border-l-4 border-green-500" 
                 @click="filterByType('educational')">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                    <span class="text-sm font-medium text-gray-700">Educativas</span>
                </div>
                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full font-medium" 
                      x-text="getActivityCountByType('educational')"></span>
            </div>
            
            <!-- Culturales -->
            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg cursor-pointer hover:bg-purple-100 transition-colors duration-200 border-l-4 border-purple-500" 
                 @click="filterByType('cultural')">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                    <span class="text-sm font-medium text-gray-700">Culturales</span>
                </div>
                <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full font-medium" 
                      x-text="getActivityCountByType('cultural')"></span>
            </div>
            
            <!-- Deportivas -->
            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg cursor-pointer hover:bg-orange-100 transition-colors duration-200 border-l-4 border-orange-500" 
                 @click="filterByType('sports')">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-orange-500 rounded-full mr-3"></div>
                    <span class="text-sm font-medium text-gray-700">Deportivas</span>
                </div>
                <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-full font-medium" 
                      x-text="getActivityCountByType('sports')"></span>
            </div>

            <!-- Sociales -->
            <div class="flex items-center justify-between p-3 bg-pink-50 rounded-lg cursor-pointer hover:bg-pink-100 transition-colors duration-200 border-l-4 border-pink-500" 
                 @click="filterByType('social')">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-pink-500 rounded-full mr-3"></div>
                    <span class="text-sm font-medium text-gray-700">Sociales</span>
                </div>
                <span class="text-xs bg-pink-100 text-pink-800 px-2 py-1 rounded-full font-medium" 
                      x-text="getActivityCountByType('social')"></span>
            </div>
        </div>
        
        <!-- Botón para limpiar filtros -->
        <div class="mt-4 pt-3 border-t border-gray-200">
            <button type="button" @click="clearFilters()" 
                    class="w-full text-sm text-gray-600 hover:text-gray-800 py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors">
                Ver todas las actividades
            </button>
        </div>
    </div>
    
    @include('components.activities.filters')
    @include('components.activities.upcoming')
</div>

<script src="js/activities-sidebar.js"></script>