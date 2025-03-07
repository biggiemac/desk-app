<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <x-flash-message />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Week Navigation -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <a href="{{ request()->fullUrlWithQuery(['week' => $weekOffset - 1]) }}" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 {{ $weekOffset <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                           {{ $weekOffset <= 0 ? 'disabled' : '' }}>
                            ← Previous Week
                        </a>
                        
                        <span class="text-lg font-medium">
                            Week of {{ $startOfWeek->format('M d, Y') }}
                        </span>
                        
                        <a href="{{ request()->fullUrlWithQuery(['week' => $weekOffset + 1]) }}" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 {{ $weekOffset >= 8 ? 'opacity-50 cursor-not-allowed' : '' }}"
                           {{ $weekOffset >= 8 ? 'disabled' : '' }}>
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
                                    {{ \Carbon\Carbon::parse($date)->format('M d') }}
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
                                            {{ \Carbon\Carbon::parse($reservation->date)->format('l, M d, Y') }}
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

    <!-- Add this modal markup at the bottom of your dashboard view -->
    <div x-data="{ showEditModal: false, currentReservation: null }"
         x-on:close-modal.window="showEditModal = false"
         x-show="showEditModal"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center"
         style="display: none;">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-bold mb-4">Edit Reservation</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" 
                           x-model="currentReservation.date" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Time Slot</label>
                    <select x-model="currentReservation.time_slot" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="AM">Morning (AM)</option>
                        <option value="PM">Afternoon (PM)</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button @click="showEditModal = false"
                            class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                        Cancel
                    </button>
                    <button @click="updateReservation()"
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function bookSlot(date, timeSlot) {
            // Confirm before booking
            if (!confirm(`Would you like to book the desk for ${timeSlot} on ${date}?`)) {
                return;
            }

            // Send AJAX request
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
                    // Reload the page to show updated availability
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

        function editReservation(reservation) {
            Alpine.store('editModal', {
                showEditModal: true,
                currentReservation: reservation
            });
        }

        function updateReservation() {
            const reservation = Alpine.store('editModal').currentReservation;
            
            fetch(`/reservations/${reservation.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    date: reservation.date,
                    time_slot: reservation.time_slot
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Alpine.store('editModal').showEditModal = false;
                    alert('Reservation updated successfully!');
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
</x-app-layout>