<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">
        <div class="flex items-center justify-between">
            <div class="font-bold text-gray-700 text-xl dark:text-white">Allowances</div>
            <x-add-edit-modal
                title="Add Allowance"
                type="button"
                action="{{ route('payroll.allowances.save') }}">
                
                <x-input 
                    name="description"
                    label="Description"
                    vallue="{{ old('description') }}"
                />
                
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
                                <span class="flex items-center">Allowance</span>
                            </th>
                            <th>
                                <span class="flex items-center">Action</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $count = 1 @endphp
                        @foreach ($allowances as $e)
                            <tr class="border-t">
                                <td class="text-end">{{ $count }}.</td>
                                <td>{{ $e->description }}</td>
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <x-add-edit-modal
                                            title="Edit Allowance"
                                            type="link"
                                            icon=''
                                            action="{{ route('payroll.allowances.update', $e->id) }}">
                                            <x-input 
                                                name="description"
                                                label="Description"
                                                value="{{ old('description', $e->description) }}"
                                            />
                                        </x-add-edit-modal>      
                                        <x-confirm-delete 
                                            action="{{ route('payroll.allowances.delete', $e->id) }}"
                                        />      
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