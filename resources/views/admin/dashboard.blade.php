@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Admin Dashboard</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-blue-700 mb-2">Total Reservations</h3>
                        <p class="text-3xl font-bold text-blue-800">{{ $totalReservations }}</p>
                    </div>
                    
                    <div class="bg-green-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-green-700 mb-2">Active Users</h3>
                        <p class="text-3xl font-bold text-green-800">{{ $activeUsers }}</p>
                    </div>

                    <div class="bg-purple-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-purple-700 mb-2">This Week's Bookings</h3>
                        <p class="text-3xl font-bold text-purple-800">{{ $weeklyBookings }}</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Quick Actions</h3>
                        <div class="flex gap-4">
                            <a href="{{ route('admin.users') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                Manage Users
                            </a>
                            <a href="{{ route('admin.reservations') }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                                Manage Reservations
                            </a>
                            <a href="{{ route('admin.holidays.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-purple-500 text-white rounded-md hover:bg-purple-600">
                                Manage Holidays
                            </a>
                        </div>
                    </div>

                    <!-- Recent Reservations -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Recent Reservations</h3>
                        <div class="bg-white rounded-lg border">
                            @if($recentReservations->isEmpty())
                                <p class="p-4 text-gray-500">No recent reservations</p>
                            @else
                                <div class="divide-y">
                                    @foreach($recentReservations as $reservation)
                                        <div class="p-4 flex justify-between items-center">
                                            <div>
                                                <div class="font-medium">{{ $reservation->user->name }}</div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $reservation->date->format('M d, Y') }} - {{ $reservation->time_slot }}
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    {{ $reservation->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst($reservation->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 