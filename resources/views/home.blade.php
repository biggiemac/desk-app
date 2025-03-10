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

        @auth
            <div class="mt-8 text-center">
                <p class="text-gray-600 mb-4">Find this useful? Consider supporting the project!</p>
                <a href="https://paypal.me/biggiemac" 
                   target="_blank"
                   class="inline-flex items-center px-6 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20.067 8.478c.492.315.844.825.983 1.422.545 2.339-.881 4.803-3.13 5.42l-.04.007c-.29.055-.59.083-.89.083h-3.35l-.89 4.11c-.054.258-.282.442-.548.442h-2.76c-.242 0-.422-.224-.367-.456l.077-.363.84-3.89.078-.363c.056-.232.236-.456.478-.456h1.674c3.19 0 5.686-.886 6.412-3.45.56-1.97-.346-3.08-1.07-3.51"/>
                        <path d="M7.926 8.478c.492.315.844.825.983 1.422.545 2.339-.881 4.803-3.13 5.42l-.04.007c-.29.055-.59.083-.89.083h-3.35l-.89 4.11c-.054.258-.282.442-.548.442h-2.76c-.242 0-.422-.224-.367-.456l.077-.363.84-3.89.078-.363c.056-.232.236-.456.478-.456h1.674c3.19 0 5.686-.886 6.412-3.45.56-1.97-.346-3.08-1.07-3.51"/>
                    </svg>
                    Make a Donation
                </a>
            </div>
        @endauth
    </div>
</div>
@endsection