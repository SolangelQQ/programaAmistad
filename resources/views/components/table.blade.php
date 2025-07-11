@props(['headers' => [], 'class' => '', 'headersClass' => 'bg-gray-50'])

<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'w-full max-w-5xl rounded-lg h-full ' . $class]) }}>
        <thead class="{{ $headersClass }}">
            <tr>
                @foreach($headers as $header)
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            {{ $slot }}
        </tbody>
    </table>
</div>