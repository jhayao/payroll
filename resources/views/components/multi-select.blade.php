@props([
    'label' => '',
    'name',
    'options' => [],
    'selected' => [],
    'amounts' => [],
    'percentages' => [],
    'wrapperClass' => 'mb-6',
])

<div class="{{ $wrapperClass }}" x-data="multiSelect({
    selected: {{ json_encode($selected) }},
    options: {{ json_encode($options) }},
    amounts: {{ json_encode($amounts) }},
    percentages: {{ json_encode($percentages) }}
})">
    @if($label)
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</label>
    @endif

    <div class="relative" @click.outside="open = false">
        
        <!-- Display Tags & Input -->
        <div 
            class="min-h-[42px] px-3 py-2 bg-gray-50 border border-gray-300 rounded cursor-text flex flex-wrap gap-1 items-center dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500"
            @click="open = true"
        >
            <template x-for="id in selected" :key="id">
                <div class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 flex items-center">
                    <span x-text="getLabel(id)"></span>
                    <button type="button" @click.stop="toggle(id)" class="ml-1 text-blue-800 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-100 font-bold focus:outline-none">&times;</button>
                </div>
            </template>
            
            <input 
                type="text" 
                placeholder="Select..." 
                class="flex-1 bg-transparent border-none outline-none text-sm p-0 focus:ring-0 dark:text-white"
                x-model="search"
                @focus="open = true"
            >
        </div>

        <!-- Dropdown Options -->
        <div 
            x-show="open" 
            style="display: none;"
            class="absolute z-10 w-full bg-white max-h-60 overflow-y-auto rounded shadow-lg border mt-1 dark:bg-gray-800 dark:border-gray-700"
        >
            <template x-for="option in filteredOptions" :key="option.id">
                <div 
                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm dark:text-gray-200 dark:hover:bg-gray-700 flex items-center justify-between"
                    @click="toggle(option.id)"
                    :class="{ 'bg-blue-50 dark:bg-blue-900': isSelected(option.id) }"
                >
                    <span x-text="option.description"></span>
                    <span x-show="isSelected(option.id)" class="text-blue-600 dark:text-blue-400 font-bold">&check;</span>
                </div>
            </template>
            <div x-show="filteredOptions.length === 0" class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                No results found.
            </div>
        </div>
    </div>

    <!-- Hidden Inputs for Form Submission -->
    <template x-for="id in selected" :key="id">
        <input type="hidden" :name="`{{ $name }}[]`" :value="id">
    </template>

    <!-- Dynamic Inputs for Employee-Based Amounts -->
    <div class="mt-2 space-y-2">
        <template x-for="id in selected" :key="id">
            <div x-show="isEmployeeBased(id)" class="flex items-center space-x-2">
                <label class="text-sm text-gray-700 dark:text-gray-300 w-1/2 break-words" x-text="getLabel(id) + (isPercentage(id) ? ' Percentage (%):' : ' Amount:')"></label>
                
                <template x-if="!isPercentage(id)">
                    <input 
                        type="number" 
                        step="0.01" 
                        :name="'{{ $name }}_amounts[' + id + ']'" 
                        x-model="amounts[id]"
                        class="w-1/2 text-sm rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="0.00"
                    >
                </template>

                <template x-if="isPercentage(id)">
                    <input 
                        type="number" 
                        step="0.01" 
                        :name="'{{ $name }}_percentages[' + id + ']'" 
                        x-model="percentages[id]"
                        class="w-1/2 text-sm rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="0%"
                    >
                </template>
            </div>
        </template>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('multiSelect', ({ selected, options, amounts, percentages }) => ({
            open: false,
            search: '',
            selected: selected ? selected.map(String) : [], // Ensure Strings for consistency
            options: options,
            amounts: amounts || {},
            percentages: percentages || {},

            get filteredOptions() {
                if (this.search === '') {
                    return this.options;
                }
                return this.options.filter(option => 
                    option.description.toLowerCase().includes(this.search.toLowerCase())
                );
            },

            getLabel(id) {
                const opt = this.options.find(o => String(o.id) === String(id));
                return opt ? opt.description : id;
            },

            isEmployeeBased(id) {
                const opt = this.options.find(o => String(o.id) === String(id));
                // Check scope 'employee' or fallback to old type 'employee_based' just in case
                return opt && (opt.scope === 'employee' || opt.type === 'employee_based');
            },

            isPercentage(id) {
                const opt = this.options.find(o => String(o.id) === String(id));
                return opt && opt.type === 'percentage';
            },

            isSelected(id) {
                return this.selected.includes(String(id));
            },

            toggle(id) {
                const strId = String(id);
                if (this.isSelected(strId)) {
                    this.selected = this.selected.filter(i => i !== strId);
                } else {
                    this.selected.push(strId);
                }
                // Don't close on toggle to allow multiple selections easily
            }
        }))
    })
</script>
