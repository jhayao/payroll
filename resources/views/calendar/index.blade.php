<x-app-layout>
    <div class="py-12 max-w-7xl mx-auto">
        <x-breadcrumb :items="[
        ['label' => 'Calendar']
    ]" />

        <div class="flex items-center justify-between mb-6">
            <div class="font-bold text-gray-700 text-xl dark:text-white">Calendar</div>

            <div class="flex items-center space-x-4">
                <a href="{{ route('calendar.index', ['year' => $date->copy()->subMonth()->year, 'month' => $date->copy()->subMonth()->month]) }}"
                    class="p-2 border rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                    &larr; Prev
                </a>
                <span class="font-bold text-lg">{{ $date->format('F Y') }}</span>
                <a href="{{ route('calendar.index', ['year' => $date->copy()->addMonth()->year, 'month' => $date->copy()->addMonth()->month]) }}"
                    class="p-2 border rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                    Next &rarr;
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="grid grid-cols-7 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="p-4 text-center font-semibold text-gray-600 dark:text-gray-300">{{ $day }}</div>
                @endforeach
            </div>

            <div class="grid grid-cols-7 auto-rows-fr">
                @php
                    $startOfMonth = $date->copy()->startOfMonth();
                    $endOfMonth = $date->copy()->endOfMonth();
                    $startOfWeek = $startOfMonth->copy()->startOfWeek(Carbon\Carbon::SUNDAY);
                    $endOfWeek = $endOfMonth->copy()->endOfWeek(Carbon\Carbon::SUNDAY);

                    $current = $startOfWeek->copy();
                @endphp

                @while($current->lte($endOfWeek))
                    @php
                        $isCurrentMonth = $current->month === $date->month;
                        $dateString = $current->toDateString();
                        $isHoliday = $holidays->has($dateString);
                        $holiday = $isHoliday ? $holidays[$dateString] : null;
                    @endphp

                    <div
                        class="min-h-[120px] p-2 border-b border-r dark:border-gray-700 {{ $isCurrentMonth ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900 text-gray-400' }}">
                        <div class="flex justify-between items-start">
                            <span
                                class="font-medium {{ $current->isToday() ? 'bg-blue-500 text-white w-6 h-6 rounded-full flex items-center justify-center' : '' }}">
                                {{ $current->day }}
                            </span>
                            @if($isCurrentMonth)
                                <div x-data="">
                                    <button x-on:click.prevent="$dispatch('open-modal', 'holiday-modal-{{ $dateString }}')"
                                        type="button" class="text-gray-400 hover:text-blue-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>

                                    <x-modal name="holiday-modal-{{ $dateString }}" focusable>
                                        <div class="p-6">
                                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                {{ $isHoliday ? 'Edit Holiday' : 'Set as Holiday' }} -
                                                {{ $current->format('F d, Y') }}
                                            </h2>

                                            <form action="{{ route('calendar.store') }}" method="POST" class="mt-6">
                                                @csrf
                                                <input type="hidden" name="date" value="{{ $dateString }}">

                                                <div class="mt-4">
                                                    <x-input name="description" label="Holiday Description" :value="$holiday ? $holiday->description : ''" required placeholder="e.g. New Year" />
                                                </div>

                                                <div class="mt-4">
                                                    <x-select name="type" label="Type" :options="$holidayTypes" :value="$holiday ? $holiday->type : 'Regular Holiday'" />
                                                </div>

                                                <div class="mt-6 flex justify-between items-center">
                                                    <div>
                                                        @if($isHoliday)
                                                            <button type="button"
                                                                x-on:click="$dispatch('close-modal', 'holiday-modal-{{ $dateString }}'); document.getElementById('delete-form-{{ $dateString }}').submit();"
                                                                class="text-red-600 hover:text-red-900 text-sm font-medium">
                                                                Revert to Regular Day
                                                            </button>
                                                        @endif
                                                    </div>

                                                    <div class="flex items-center gap-3">
                                                        <x-secondary-button
                                                            x-on:click="$dispatch('close-modal', 'holiday-modal-{{ $dateString }}')">
                                                            Cancel
                                                        </x-secondary-button>

                                                        <x-primary-button>
                                                            Save
                                                        </x-primary-button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </x-modal>
                                </div>

                                @if($isHoliday)
                                    <form id="delete-form-{{ $dateString }}" action="{{ route('calendar.destroy') }}" method="POST"
                                        class="hidden">
                                        @csrf
                                        <input type="hidden" name="date" value="{{ $dateString }}">
                                    </form>
                                @endif
                            @endif
                        </div>

                        @if($isHoliday)
                            <div class="mt-2 p-1 bg-red-100 text-red-800 text-xs rounded border border-red-200">
                                <div class="font-bold">{{ $holiday->description }}</div>
                                <div class="text-[10px] uppercase">{{ $holiday->type }}</div>
                            </div>
                        @else
                            <div class="mt-2 min-h-[1.5em]"></div>
                        @endif
                    </div>

                    @php $current->addDay(); @endphp
                @endwhile
            </div>
        </div>
    </div>
</x-app-layout>