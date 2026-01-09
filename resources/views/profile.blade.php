<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">

        <div class="font-bold text-gray-700 text-xl dark:text-white">Profile</div>

        <div class="mt-6">

            <div class="bg-white border border-slate-200 dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-8">

                    <form action="{{ route('profile-update') }}" method="POST">
                        @csrf
                        
                        <div class="max-w-md">

                            <x-input 
                                name="name"
                                label="Name"
                                value="{{ old('name', auth()->user()->name) }}"
                            />

                            <x-input 
                                name="email"
                                label="Email"
                                value="{{ old('email', auth()->user()->email) }}"
                            />

                            <x-input 
                                name="role"
                                label="Role"
                                value="{{ old('role', auth()->user()->role) }}"
                                :readonly="true"
                            />
                            
                        </div>
                    
                        <div class="flex items-center space-x-2 mt-6">
                            <x-primary-button>
                                Save changes
                            </x-primary-button>
                        </div>
                    
                    </form>

                </div>
            </div>

        </div>

        <div class="mt-6">

            <div class="bg-white border border-slate-200 dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-8">

                    <div class="font-semibold mb-4">Change Password</div>

                    <form action="{{ route('profile-password-update') }}" method="POST">
                        @csrf
                        
                        <div class="max-w-md">

                            <x-input 
                                type="password"
                                name="current_password"
                                label="Current Password"
                            />

                            <x-input 
                                type="password"
                                name="new_password"
                                label="New Password"
                            />

                            <x-input  
                                type="password"
                                name="new_password_confirmation"
                                label="Confirm Password"
                            />
                            
                        </div>
                    
                        <div class="flex items-center space-x-2 mt-6">
                            <x-primary-button>
                                Save changes
                            </x-primary-button>
                        </div>
                    
                    </form>

                </div>
            </div>

        </div>
        
    </div>
</x-app-layout>