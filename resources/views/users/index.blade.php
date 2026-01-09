<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">
        <div class="flex items-center justify-between">
            <div class="font-bold text-gray-700 text-xl dark:text-white">User Accounts</div>
            <div class="flex items-center space-x-1">
                <x-add-edit-modal
                    title="Add User"
                    action="{{ route('users.save') }}"
                    type="button"
                >

                    <x-input 
                        name="name"
                        label="Name"
                        value="{{ old('name') }}"
                    />

                    <x-select 
                        name="role"
                        label="Role"
                        value="{{ old('role') }}"
                        :options="[
                            'accounting' => 'Accounting',
                            'hr' => 'Hr',
                            'timekeeper' => 'Timekeeper',
                            'admin' => 'Admin'
                        ]"
                    />

                    <x-input 
                        name="email"
                        label="Email"
                        value="{{ old('email') }}"
                    />

                    <x-input 
                        type="password"
                        name="password"
                        label="Password"
                    />

                </x-add-edit-modal>
            </div>
            
        </div>
        <div class="mt-6">
            <!-- Start coding here -->
            <div class="bg-white border border-slate-300 rounded px-4 relative">
                <div class="overflow-x-auto w-full">
                    <table id="myTable">
                        <thead>
                            <tr>
                                <th>
                                    <span class="flex items-center">Name</span>
                                </th>
                                <th>
                                    <span>Role</span>
                                </th>
                                <th>
                                    <span>Email</span>
                                </th>
                                <th>
                                    <span class="flex items-center">Action</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $e)
                            <tr class="border-t">
                                <td>{{ $e->name }}</td>
                                <td>{{ ucfirst($e->role) }}</td>
                                <td>{{ $e->email }}</td>
                                <td>
                                    <x-add-edit-modal
                                        title="Edit User"
                                        action="{{ route('users.update', $e->id) }}"
                                        type="link"
                                    >

                                        <x-input 
                                            name="name"
                                            label="Name"
                                            value="{{ old('name', $e->name) }}"
                                        />

                                        <x-select 
                                            name="role"
                                            label="Role"
                                            value="{{ old('name', $e->role) }}"
                                            :options="[
                                                'accounting' => 'Accounting',
                                                'hr' => 'Hr',
                                                'timekeeper' => 'Timekeeper',
                                                'admin' => 'Admin'
                                            ]"
                                        />

                                        <x-input 
                                            name="email"
                                            label="Email"
                                            value="{{ old('email', $e->email) }}"
                                        />

                                    </x-add-edit-modal>
                                </td>
                            </tr>    
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>

    <script>
        let table = new DataTable('#myTable', {
            lengthChange: false,
            ordering: false,
            paginate: false
        });
    </script>

</x-app-layout>