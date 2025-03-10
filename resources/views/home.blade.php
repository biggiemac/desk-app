@extends('layouts.app')

@section('content')
<div class="container">
    <div class="flex flex-col items-center gap-4">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Desk Availability</h1>
            <p class="mt-2 text-gray-600">Book a desk for collaborative work</p>
        </div>
        
        <div class="grid grid-cols-5 gap-4 mb-4">
            @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri'] as $day)
                <div class="text-center font-semibold text-gray-700 bg-gray-100 py-2 rounded">
                    {{ $day }}
                </div>
            @endforeach

            @foreach($availableSlots as $date => $timeSlots)
                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="text-sm font-medium text-gray-700 mb-3 text-center">
                        {{ \Carbon\Carbon::parse($date)->format('M d') }}
                    </div>
                    <div class="space-y-3">
                        <div class="text-sm rounded-md p-2 text-center
                            {{ $timeSlots['AM'] 
                                ? 'bg-green-100 text-green-800' 
                                : 'bg-red-100 text-red-800' 
                            }}">
                            AM {{ $timeSlots['AM'] ? '✓ Open' : '× Booked' }}
                        </div>
                        <div class="text-sm rounded-md p-2 text-center
                            {{ $timeSlots['PM'] 
                                ? 'bg-green-100 text-green-800' 
                                : 'bg-red-100 text-red-800' 
                            }}">
                            PM {{ $timeSlots['PM'] ? '✓ Open' : '× Booked' }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-center space-x-4">
            @if(request('week', 0) > 0)
                <a href="{{ route('home') }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                    ← Current Week
                </a>
            @endif
            <a href="{{ route('home', ['week' => request('week', 0) + 1]) }}" 
               class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                Next Week →
            </a>
        </div>

        @guest
            <div class="mt-8 text-center bg-gray-50 rounded-lg p-6">
                <p class="mb-4 text-gray-600">Want to book the desk? Register or login to make a reservation.</p>
                <div class="space-x-4">
                    <a href="{{ route('register') }}" 
                       class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                        Register
                    </a>
                    <a href="{{ route('login') }}" 
                       class="inline-block bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        Login
                    </a>
                </div>
            </div>
        @endguest
    </div>
</div>
@endsection