{{-- programaAmistad/resources/views/components/friendship/attendance-table.blade.php --}}

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('Fecha') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('Buddy') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('PeerBuddy') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('Notas') }}
                </th>
            </tr>
        </thead>
        <tbody id="attendance_table_body" class="bg-white divide-y divide-gray-200">
            <!-- Filas se llenarán dinámicamente -->
        </tbody>
    </table>
</div>