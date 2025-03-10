<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-900">
                        <h3 class="text-lg font-semibold mb-2">Total Users</h3>
                        <p class="text-3xl font-bold">{{ $totalUsers }}</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-900">
                        <h3 class="text-lg font-semibold mb-2">Total Reservations</h3>
                        <p class="text-3xl font-bold">{{ $totalReservations }}</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-900">
                        <h3 class="text-lg font-semibold mb-2">Today's Reservations</h3>
                        <p class="text-3xl font-bold">{{ $todayReservations }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between mb-4">
                    <h3 class="text-lg font-semibold">Quick Actions</h3>
                </div>
                <div class="space-x-4">
                    <a href="{{ route('admin.users') }}" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Manage Users
                    </a>
                    <a href="{{ route('admin.reservations') }}" class="inline-block bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        View Reservations
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 