<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">
        <div class="flex items-center justify-between">
            <div class="font-bold text-gray-700 text-xl dark:text-white">Deductions</div>
            <x-add-edit-modal title="Add Deduction" type="button" action="{{ route('payroll.deductions.save') }}">

                <div
                    x-data="{ type: '{{ old('type', 'fixed') }}', scope: '{{ old('scope', 'all') }}', schedule: '{{ old('schedule', 'every_payroll') }}' }">
                    <x-input name="description" label="Description" value="{{ old('description') }}" />

                    <div class="mt-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Type</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input id="type-fixed-add" type="radio" value="fixed" name="type" x-model="type"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                <label for="type-fixed-add"
                                    class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Fixed
                                    Amount</label>
                            </div>
                            <div class="flex items-center">
                                <input id="type-percentage-add" type="radio" value="percentage" name="type"
                                    x-model="type"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                <label for="type-percentage-add"
                                    class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Percentage</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Scope</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input id="scope-all-add" type="radio" value="all" name="scope" x-model="scope"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                <label for="scope-all-add"
                                    class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">All
                                    Employees</label>
                            </div>
                            <div class="flex items-center">
                                <input id="scope-position-add" type="radio" value="position" name="scope"
                                    x-model="scope"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                <label for="scope-position-add"
                                    class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">By
                                    Position</label>
                            </div>
                            <div class="flex items-center">
                                <input id="scope-employee-add" type="radio" value="employee" name="scope"
                                    x-model="scope"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                <label for="scope-employee-add"
                                    class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">By
                                    Employee</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Schedule</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input id="schedule-every-add" type="radio" value="every_payroll" name="schedule"
                                    x-model="schedule"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                <label for="schedule-every-add"
                                    class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Every
                                    Payroll</label>
                            </div>
                            <div class="flex items-center">
                                <input id="schedule-specific-add" type="radio" value="specific_month" name="schedule"
                                    x-model="schedule"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                <label for="schedule-specific-add"
                                    class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Specific
                                    Month</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4" x-show="schedule === 'specific_month'">
                        <div class="grid grid-cols-2 gap-4">
                            <x-select name="target_month" label="Target Month">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ old('target_month') == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endforeach
                            </x-select>
                            <x-input name="target_year" label="Target Year (Optional)" type="number"
                                value="{{ old('target_year', date('Y')) }}" placeholder="Year" />
                        </div>
                    </div>

                    <div class="mt-4" x-show="scope === 'all' && type === 'fixed'">
                        <x-input name="amount" label="Amount" type="number" step="0.01" value="{{ old('amount') }}" />
                    </div>

                    <div class="mt-4" x-show="scope === 'all' && type === 'percentage'">
                        <x-input name="percentage" label="Percentage (%)" type="number" step="0.01"
                            value="{{ old('percentage') }}" />
                    </div>

                    <div class="mt-4" x-show="scope === 'position'">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Position
                            Rates</label>
                        <div class="max-h-60 overflow-y-auto border rounded p-2">
                            @foreach($positions as $position)
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm">{{ $position->description }}</span>
                                    <input type="number" step="0.01" name="position_amounts[{{ $position->id }}]"
                                        class="w-24 text-sm rounded border-gray-300"
                                        :placeholder="type === 'fixed' ? 'Amount' : 'Percent'">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4" x-show="scope === 'employee'" x-data="{ empSearch: '' }">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Employee
                            Rates</label>
                        <input type="text" x-model="empSearch" placeholder="Search employees..."
                            class="mb-2 block w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <div class="max-h-60 overflow-y-auto border rounded p-2">
                            @foreach($employees as $employee)
                                <div x-show="!empSearch || '{{ strtolower($employee->full_name) }}'.includes(empSearch.toLowerCase())"
                                    class="flex items-center justify-between mb-2">
                                    <span class="text-sm">{{ $employee->full_name }}</span>
                                    <input type="number" step="0.01" name="employee_amounts[{{ $employee->id }}]"
                                        class="w-24 text-sm rounded border-gray-300"
                                        :placeholder="type === 'fixed' ? 'Amount' : 'Percent'">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </x-add-edit-modal>
        </div>
        <div class="mt-6">
            <!-- Start coding here -->
            <div class="bg-white border border-slate-300 rounded px-4 relative">

                <table id="myTable">
                    <thead>
                        <tr>
                            <th style="width:50px;">No.</th>
                            <th>
                                <span class="flex items-center">Deduction</span>
                            </th>
                            <th>
                                <span class="flex items-center">Type</span>
                            </th>
                            <th>
                                <span class="flex items-center">Value</span>
                            </th>
                            <th>
                                <span class="flex items-center">Scope</span>
                            </th>
                            <th>
                                <span class="flex items-center">Schedule</span>
                            </th>
                            <th>
                                <span class="flex items-center">Action</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $count = 1 @endphp
                        @foreach ($deductions as $e)
                            <tr class="border-t">
                                <td class="text-end">{{ $count }}.</td>
                                <td>{{ $e->description }}</td>
                                <td>{{ ucfirst($e->type) }}</td>
                                <td>
                                    @if($e->scope == 'all')
                                        @if($e->type == 'fixed')
                                            {{ number_format($e->amount, 2) }}
                                        @else
                                            {{ $e->percentage }}%
                                        @endif
                                    @else
                                        <span class="text-gray-500 italic">Varies</span>
                                    @endif
                                </td>
                                <td>
                                    @if($e->scope == 'all')
                                        All Employees
                                    @elseif($e->scope == 'position')
                                        By Position
                                    @else
                                        <div class="group relative cursor-pointer">
                                            <span class="underline decoration-dotted">By Employee
                                                ({{ $e->employees->count() }})</span>
                                            <div
                                                class="absolute z-10 hidden group-hover:block bg-white border border-gray-200 shadow-lg rounded p-2 text-xs w-64 max-h-48 overflow-y-auto">
                                                <ul class="list-disc pl-4">
                                                    @foreach($e->employees as $emp)
                                                        <li>{{ $emp->full_name }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($e->schedule == 'specific_month')
                                        {{ date('F', mktime(0, 0, 0, $e->target_month, 1)) }}
                                        @if($e->target_year) {{ $e->target_year }} @endif
                                    @else
                                        Every Payroll
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <x-view-modal title="View Deduction">
                                            <div class="grid grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <span class="block text-gray-500 font-medium">Description</span>
                                                    <span class="text-gray-900 dark:text-white">{{ $e->description }}</span>
                                                </div>
                                                <div>
                                                    <span class="block text-gray-500 font-medium">Type</span>
                                                    <span
                                                        class="text-gray-900 dark:text-white">{{ ucfirst($e->type) }}</span>
                                                </div>
                                                <div>
                                                    <span class="block text-gray-500 font-medium">Schedule</span>
                                                    <span class="text-gray-900 dark:text-white">
                                                        @if($e->schedule == 'specific_month')
                                                            Specific Month
                                                            ({{ date('F', mktime(0, 0, 0, $e->target_month, 1)) }}
                                                            @if($e->target_year) {{ $e->target_year }} @endif)
                                                        @else
                                                            Every Payroll
                                                        @endif
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="block text-gray-500 font-medium">Scope</span>
                                                    <span class="text-gray-900 dark:text-white">
                                                        @if($e->scope == 'all') All Employees
                                                        @elseif($e->scope == 'position') By Position
                                                        @else By Employee
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="mt-4 border-t pt-4">
                                                <h3 class="font-medium text-gray-900 dark:text-white mb-2">Rates / Amounts
                                                </h3>

                                                @if($e->scope == 'all')
                                                    <div
                                                        class="flex justify-between items-center bg-gray-50 p-2 rounded dark:bg-gray-700">
                                                        <span>All Employees</span>
                                                        <span class="font-bold">
                                                            @if($e->type == 'fixed') {{ number_format($e->amount, 2) }}
                                                            @else {{ $e->percentage }}%
                                                            @endif
                                                        </span>
                                                    </div>
                                                @elseif($e->scope == 'position')
                                                    <div class="space-y-1">
                                                        @foreach($e->positions as $pos)
                                                            <div
                                                                class="flex justify-between items-center text-sm border-b border-gray-100 py-1 last:border-0">
                                                                <span>{{ $pos->description }}</span>
                                                                <span class="font-mono">
                                                                    @if($e->type == 'fixed')
                                                                        {{ number_format($pos->pivot->amount, 2) }}
                                                                    @else {{ $pos->pivot->percentage }}%
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @elseif($e->scope == 'employee')
                                                    <div class="space-y-1 max-h-60 overflow-y-auto">
                                                        @foreach($e->employees as $emp)
                                                            <div
                                                                class="flex justify-between items-center text-sm border-b border-gray-100 py-1 last:border-0">
                                                                <span>{{ $emp->full_name }}</span>
                                                                <span class="font-mono">
                                                                    @if($e->type == 'fixed')
                                                                        {{ number_format($emp->pivot->amount, 2) }}
                                                                    @else {{ $emp->pivot->percentage }}%
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </x-view-modal>

                                        <x-add-edit-modal title="Edit Deduction" type="link" icon=''
                                            action="{{ route('payroll.deductions.update', $e->id) }}">
                                            <div
                                                x-data="{ type: '{{ old('type', $e->type) }}', scope: '{{ old('scope', $e->scope) }}', schedule: '{{ old('schedule', $e->schedule) }}' }">
                                                <x-input name="description" label="Description"
                                                    value="{{ old('description', $e->description) }}" />

                                                <div class="mt-4">
                                                    <label
                                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Type</label>
                                                    <div class="flex items-center space-x-4">
                                                        <div class="flex items-center">
                                                            <input id="type-fixed-{{ $e->id }}" type="radio" value="fixed"
                                                                name="type" x-model="type"
                                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                                            <label for="type-fixed-{{ $e->id }}"
                                                                class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Fixed
                                                                Amount</label>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <input id="type-percentage-{{ $e->id }}" type="radio"
                                                                value="percentage" name="type" x-model="type"
                                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                                            <label for="type-percentage-{{ $e->id }}"
                                                                class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Percentage</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-4">
                                                    <label
                                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Scope</label>
                                                    <div class="flex items-center space-x-4">
                                                        <div class="flex items-center">
                                                            <input id="scope-all-{{ $e->id }}" type="radio" value="all"
                                                                name="scope" x-model="scope"
                                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                                            <label for="scope-all-{{ $e->id }}"
                                                                class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">All
                                                                Employees</label>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <input id="scope-position-{{ $e->id }}" type="radio"
                                                                value="position" name="scope" x-model="scope"
                                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                                            <label for="scope-position-{{ $e->id }}"
                                                                class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">By
                                                                Position</label>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <input id="scope-employee-{{ $e->id }}" type="radio"
                                                                value="employee" name="scope" x-model="scope"
                                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                                            <label for="scope-employee-{{ $e->id }}"
                                                                class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">By
                                                                Employee</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-4">
                                                    <label
                                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Schedule</label>
                                                    <div class="flex items-center space-x-4">
                                                        <div class="flex items-center">
                                                            <input id="schedule-every-{{ $e->id }}" type="radio"
                                                                value="every_payroll" name="schedule" x-model="schedule"
                                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                                            <label for="schedule-every-{{ $e->id }}"
                                                                class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Every
                                                                Payroll</label>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <input id="schedule-specific-{{ $e->id }}" type="radio"
                                                                value="specific_month" name="schedule" x-model="schedule"
                                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                                            <label for="schedule-specific-{{ $e->id }}"
                                                                class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Specific
                                                                Month</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-4" x-show="schedule === 'specific_month'">
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <x-select name="target_month" label="Target Month">
                                                            @foreach(range(1, 12) as $m)
                                                                <option value="{{ $m }}" {{ old('target_month', $e->target_month) == $m ? 'selected' : '' }}>
                                                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                                                </option>
                                                            @endforeach
                                                        </x-select>
                                                        <x-input name="target_year" label="Target Year (Optional)"
                                                            type="number" value="{{ old('target_year', $e->target_year) }}"
                                                            placeholder="Year" />
                                                    </div>
                                                </div>

                                                <div class="mt-4" x-show="scope === 'all' && type === 'fixed'">
                                                    <x-input name="amount" label="Amount" type="number" step="0.01"
                                                        value="{{ old('amount', $e->amount) }}" />
                                                </div>

                                                <div class="mt-4" x-show="scope === 'all' && type === 'percentage'">
                                                    <x-input name="percentage" label="Percentage (%)" type="number"
                                                        step="0.01" value="{{ old('percentage', $e->percentage) }}" />
                                                </div>

                                                <div class="mt-4" x-show="scope === 'position'">
                                                    <label
                                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Position
                                                        Rates</label>
                                                    <div class="max-h-60 overflow-y-auto border rounded p-2">
                                                        @foreach($positions as $position)
                                                            @php
                                                                $pivotVal = 0;
                                                                $pivot = $e->positions->find($position->id);
                                                                if ($pivot) {
                                                                    if ($e->type == 'fixed')
                                                                        $pivotVal = $pivot->pivot->amount;
                                                                    else
                                                                        $pivotVal = $pivot->pivot->percentage;
                                                                }
                                                            @endphp
                                                            <div class="flex items-center justify-between mb-2">
                                                                <span class="text-sm">{{ $position->description }}</span>
                                                                <input type="number" step="0.01"
                                                                    name="position_amounts[{{ $position->id }}]"
                                                                    value="{{ $pivotVal }}"
                                                                    class="w-24 text-sm rounded border-gray-300"
                                                                    :placeholder="type === 'fixed' ? 'Amount' : 'Percent'">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="mt-4" x-show="scope === 'employee'" x-data="{ empSearch: '' }">
                                                    <label
                                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Employee
                                                        Rates</label>
                                                    <input type="text" x-model="empSearch" placeholder="Search employees..."
                                                        class="mb-2 block w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                                    <div class="max-h-60 overflow-y-auto border rounded p-2">
                                                        @foreach($employees as $employee)
                                                            @php
                                                                $pivotVal = 0;
                                                                $pivot = $e->employees->find($employee->id);
                                                                if ($pivot) {
                                                                    if ($e->type == 'fixed')
                                                                        $pivotVal = $pivot->pivot->amount;
                                                                    else
                                                                        $pivotVal = $pivot->pivot->percentage;
                                                                }
                                                            @endphp
                                                            <div x-show="!empSearch || '{{ strtolower($employee->full_name) }}'.includes(empSearch.toLowerCase())"
                                                                class="flex items-center justify-between mb-2">
                                                                <span class="text-sm">{{ $employee->full_name }}</span>
                                                                <input type="number" step="0.01"
                                                                    name="employee_amounts[{{ $employee->id }}]"
                                                                    value="{{ $pivotVal }}"
                                                                    class="w-24 text-sm rounded border-gray-300"
                                                                    :placeholder="type === 'fixed' ? 'Amount' : 'Percent'">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </x-add-edit-modal>
                                        <x-confirm-delete action="{{ route('payroll.deductions.delete', $e->id) }}" />
                                    </div>
                                </td>
                            </tr>
                            @php $count++ @endphp
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>

    </div>

    <script>
        let table = new DataTable('#myTable', {
            lengthChange: false,
            ordering: false,
            paginate: false,
            columnDefs: [
                { width: "40px", targets: 0 },   // No. column
            ]
        });
    </script>

</x-app-layout>