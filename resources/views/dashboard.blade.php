@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Week Navigation -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    @if($weekOffset > 0)
                        <a href="{{ request()->fullUrlWithQuery(['week' => $weekOffset - 1]) }}" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                            ← Previous Week
                        </a>
                    @else
                        <div></div>
                    @endif
                    
                    <span class="text-lg font-medium">
                        Week of {{ $startOfWeek->format('M d, Y') }}
                    </span>
                    
                    <a href="{{ request()->fullUrlWithQuery(['week' => $weekOffset + 1]) }}" 
                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Next Week →
                    </a>
                </div>

                <!-- Weekly Schedule Grid -->
                <div class="grid grid-cols-5 gap-4">
                    @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri'] as $day)
                        <div class="text-center font-semibold text-gray-700 bg-gray-100 py-2 rounded">
                            {{ $day }}
                        </div>
                    @endforeach

                    @foreach($slots as $date => $timeSlots)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="text-sm font-medium text-gray-700 mb-3 text-center">
                                {{ Carbon\Carbon::parse($date)->format('M d') }}
                                @if($timeSlots['isHoliday'])
                                    <div class="text-xs text-red-600 mt-1">
                                        {{ $timeSlots['holidayName'] }}
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-3">
                                <button 
                                    @if($timeSlots['AM'] && !$timeSlots['isAMPassed'] && !$timeSlots['isHoliday']) 
                                        onclick="bookSlot('{{ $date }}', 'AM')"
                                    @endif
                                    class="w-full text-sm rounded-md p-2 text-center
                                    {{ $timeSlots['isPast'] || $timeSlots['isAMPassed'] || $timeSlots['isHoliday']
                                        ? 'bg-gray-100 text-gray-500 cursor-not-allowed' 
                                        : ($timeSlots['AM'] 
                                            ? 'bg-green-100 text-green-800 hover:bg-green-200' 
                                            : 'bg-red-100 text-red-800') 
                                    }}">
                                    AM {{ $timeSlots['isHoliday'] ? '× Holiday' : ($timeSlots['isPast'] || $timeSlots['isAMPassed'] ? '× Expired' : ($timeSlots['AM'] ? '✓ Book' : '× Booked')) }}
                                </button>
                                <button 
                                    @if($timeSlots['PM'] && !$timeSlots['isPMPassed'] && !$timeSlots['isHoliday']) 
                                        onclick="bookSlot('{{ $date }}', 'PM')"
                                    @endif
                                    class="w-full text-sm rounded-md p-2 text-center
                                    {{ $timeSlots['isPast'] || $timeSlots['isPMPassed'] || $timeSlots['isHoliday']
                                        ? 'bg-gray-100 text-gray-500 cursor-not-allowed' 
                                        : ($timeSlots['PM'] 
                                            ? 'bg-green-100 text-green-800 hover:bg-green-200' 
                                            : 'bg-red-100 text-red-800') 
                                    }}">
                                    PM {{ $timeSlots['isHoliday'] ? '× Holiday' : ($timeSlots['isPast'] || $timeSlots['isPMPassed'] ? '× Expired' : ($timeSlots['PM'] ? '✓ Book' : '× Booked')) }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Your Reservations Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-semibold mb-4">Your Upcoming Reservations</h3>
                
                @if($upcomingReservations->isEmpty())
                    <p class="text-gray-500">You don't have any upcoming reservations.</p>
                @else
                    <div class="space-y-4">
                        @foreach($upcomingReservations as $reservation)
                            <div class="border p-4 rounded-lg flex justify-between items-center">
                                <div>
                                    <div class="font-medium">
                                        {{ $reservation->date->format('l, M d, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $reservation->time_slot }} Session
                                    </div>
                                </div>
                                <button 
                                    onclick="cancelReservation({{ $reservation->id }})"
                                    class="text-red-600 hover:text-red-800 font-medium">
                                    Cancel
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function bookSlot(date, timeSlot) {
        if (!confirm(`Would you like to book the desk for ${timeSlot} on ${date}?`)) {
            return;
        }

        fetch('{{ route('reservations.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                date: date,
                time_slot: timeSlot
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Booking successful!');
                window.location.reload();
            } else {
                alert(data.message || 'Something went wrong. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong. Please try again.');
        });
    }

    function cancelReservation(id) {
        if (!confirm('Are you sure you want to cancel this reservation?')) {
            return;
        }

        fetch(`/reservations/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Reservation cancelled successfully!');
                window.location.reload();
            } else {
                alert(data.message || 'Something went wrong. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong. Please try again.');
        });
    }
</script>
@endsection