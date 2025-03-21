@if (session()->has('message'))
    <div x-data="{ show: true }"
         x-show="show"
         x-transition
         x-init="setTimeout(() => show = false, 3000)"
         class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg">
        {{ session('message') }}
    </div>
@endif