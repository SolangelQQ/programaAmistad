@props(['type' => 'success', 'message' => ''])

@if(session()->has('success'))
    <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
        <p>{{ session('success') }}</p>
    </div>
@endif

@if(session()->has('error'))
    <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
        <p>{{ session('error') }}</p>
    </div>
@endif

@if(session()->has('warning'))
    <div class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
        <p>{{ session('warning') }}</p>
    </div>
@endif

@if(session()->has('info'))
    <div class="mb-4 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700">
        <p>{{ session('info') }}</p>
    </div>
@endif

@if($errors->any())
    <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif