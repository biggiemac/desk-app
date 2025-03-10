@extends('layouts.app')

@section('content')
<div class="container">
    <div class="flex flex-col gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Admin Dashboard</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-blue-700 mb-2">Total Reservations</h3>
                    <p class="text-3xl font-bold text-blue-800">{{ $totalReservations ?? 0 }}</p>
                </div>
                
                <div class="bg-green-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-green-700 mb-2">Active Users</h3>
                    <p class="text-3xl font-bold text-green-800">{{ $activeUsers ?? 0 }}</p>
                </div>

                <div class="bg-purple-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-purple-700 mb-2">This Week's Bookings</h3>
                    <p class="text-3xl font-bold text-purple-800">{{ $weeklyBookings ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h3>
            <div class="flex gap-4">
                <a href="{{ route('admin.users') }}" 
                   class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                    Manage Users
                </a>
                <a href="{{ route('admin.reservations') }}" 
                   class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                    Manage Reservations
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 