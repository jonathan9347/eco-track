<section
    x-data="carbonCalculator({
        calculateUrl: '{{ url('/api/calculate-carbon') }}',
        saveUrl: '{{ url('/api/save-log') }}',
        csrfToken: '{{ csrf_token() }}',
        userId: @js($userId),
    })"
    class="w-full"
>
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Modal container - completely transparent, no stacking */
        .cc-modal-container {
            max-width: 550px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            background: transparent !important;
            box-shadow: none !important;
            border: none !important;
        }

        /* Grid layout - no background, standalone appearance */
        .cc-modal-grid {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 0 !important;
            min-width: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            border: none !important;
        }

        @media (max-width: 640px) {
            .cc-modal-grid {
                grid-template-columns: 1fr !important;
            }
        }

        .cc-form-grid {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 0.375rem !important; /* ~gap-1.5 - more compact */
            min-width: 0 !important;
        }

        /* Compact padding for smaller elements */
        .cc-form-column {
            padding: 0.75rem !important; /* ~p-3 - more compact */
        }

        .cc-result-column {
            padding: 0.75rem !important; /* ~p-3 - more compact */
        }

        /* Prevent parent overflow from clipping dropdown */
        .cc-modal-container *,
        .cc-modal-grid *,
        .cc-form-column *,
        .cc-result-column * {
            overflow: visible !important;
        }

        /* shadcn-style select dropdown - compact */
        .shadcn-select-trigger {
            position: relative;
        }

        /* Overlay dropdown - floats above everything */
        .shadcn-select-content {
            position: fixed !important;
            z-index: 9999 !important;
            min-width: 200px !important;
            max-height: 300px !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            background: white !important;
            border: 1px solid #e5e5e5 !important;
            border-radius: 0.35rem !important;
            box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.15), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
            padding: 0.25rem !important;
            animation: shadcn-select-in 150ms ease-out !important;
            clip: auto !important;
            clip-path: none !important;
        }

        .shadcn-select-content.shadcn-select-top {
            /* Positioned above trigger */
        }

        .shadcn-select-content.shadcn-select-bottom {
            /* Positioned below trigger */
        }

        .shadcn-select-item {
            padding: 0.375rem 0.5rem; /* ~py-1.5 px-2 - more compact */
            font-size: 0.75rem; /* ~text-xs - smaller text */
            color: #171717;
            border-radius: 0.35rem;
            cursor: pointer;
            transition: background-color 100ms ease;
        }

        .shadcn-select-item:hover {
            background-color: #f5f5f5;
        }

        .shadcn-select-item[aria-selected="true"] {
            background-color: #f5f5f5;
            font-weight: 500;
        }

        @keyframes shadcn-select-in {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>

    <!-- Carbon Calculator - No Container Layout -->
    <div class="cc-modal-container">
        <!-- Title only (no container) -->
        <div class="text-lg font-semibold text-gray-900 mb-4">Carbon Calculator</div>

        <!-- Main 2-column layout (forms left, result right) -->
        <div class="cc-modal-grid">
            <!-- Left Column: Form -->
            <div class="cc-form-column min-w-0">
                <form id="carbon-calculator-form" @submit.prevent="calculate" class="flex flex-col gap-3">
                    <!-- 4-grid layout (2x2) -->
                    <div class="cc-form-grid">
                        <!-- Transport Type - shadcn-style select -->
                        <div class="min-w-0" x-data="{
                            open: false,
                            value: '',
                            placeholder: 'Select transport',
                            options: @js($transportOptions),
                            selectedLabel: '',
                            dropdownStyle: '',
                            init() {
                                this.$watch('value', (val) => {
                                    const opt = this.options.find(o => o.value === val);
                                    this.selectedLabel = opt ? opt.label : '';
                                    // Sync with parent x-model
                                    form.transport_type = val;
                                });
                                // Watch parent for external changes
                                this.$watch('form.transport_type', (val) => {
                                    if (this.value !== val) {
                                        this.value = val;
                                        const opt = this.options.find(o => o.value === val);
                                        this.selectedLabel = opt ? opt.label : '';
                                    }
                                });
                            },
                            select(val, label) {
                                this.value = val;
                                this.selectedLabel = label;
                                this.open = false;
                            },
                            toggle() {
                                if (this.open) {
                                    this.open = false;
                                    this.dropdownStyle = '';
                                    return;
                                }
                                this.open = true;
                                this.$nextTick(() => {
                                    const trigger = this.$el.querySelector('.shadcn-select-trigger button');
                                    const rect = trigger.getBoundingClientRect();
                                    this.dropdownStyle = `top: ${rect.bottom + 4}px; left: ${rect.left}px; width: ${rect.width}px;`;
                                });
                            },
                            get isOpen() { return this.open; },
                            get displayValue() { return this.selectedLabel || this.placeholder; }
                        }">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Transport Type</label>
                            <div class="shadcn-select-trigger">
                                 <button
                                     type="button"
                                     @click="toggle()"
                                     @click.away="open = false; dropdownStyle = ''"
                                     :aria-expanded="open"
                                     aria-haspopup="listbox"
                                     class="flex h-9 w-full items-center justify-between border border-gray-300 bg-white px-2.5 py-1.5 text-xs placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 text-left"
                                     style="border-radius: 0.35rem !important;"
                                     :class="open && 'ring-2 ring-emerald-600 ring-offset-2'"
                                 >
                                    <span x-text="displayValue" :class="!value && 'text-gray-500'"></span>
                                    <svg class="h-3.5 w-3.5 shrink-0 text-gray-500 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                                    </svg>
                                </button>
                                <div
                                    x-show="open"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="shadcn-select-content shadcn-select-bottom"
                                    role="listbox"
                                    :style="dropdownStyle"
                                >
                                    <div
                                        class="shadcn-select-item"
                                        @click="select('', 'Select transport')"
                                        :aria-selected="!value"
                                        role="option"
                                    >
                                        Select transport
                                    </div>
                                    <template x-for="opt in options" :key="opt.value">
                                        <div
                                            class="shadcn-select-item"
                                            @click="select(opt.value, opt.label)"
                                            :aria-selected="value === opt.value"
                                            role="option"
                                            x-text="opt.label"
                                        ></div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Distance -->
                        <div class="min-w-0">
                            <label for="distance" class="block text-xs font-medium text-gray-700 mb-1">Distance (km)</label>
                            <input
                                id="distance"
                                x-model="form.distance"
                                type="number"
                                min="0"
                                step="0.1"
                                class="flex h-9 w-full border border-gray-300 bg-white px-2.5 py-1.5 text-xs placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                style="border-radius: 0.35rem !important;"
                                placeholder="0.0"
                            >
                        </div>

                        <!-- Diet Type - shadcn-style select -->
                        <div class="min-w-0" x-data="{
                            open: false,
                            value: '',
                            placeholder: 'Select diet',
                            options: @js($dietOptions),
                            selectedLabel: '',
                            dropdownStyle: '',
                            init() {
                                this.$watch('value', (val) => {
                                    const opt = this.options.find(o => o.value === val);
                                    this.selectedLabel = opt ? opt.label : '';
                                    // Sync with parent x-model
                                    form.diet_type = val;
                                });
                                // Watch parent for external changes
                                this.$watch('form.diet_type', (val) => {
                                    if (this.value !== val) {
                                        this.value = val;
                                        const opt = this.options.find(o => o.value === val);
                                        this.selectedLabel = opt ? opt.label : '';
                                    }
                                });
                            },
                            select(val, label) {
                                this.value = val;
                                this.selectedLabel = label;
                                this.open = false;
                                this.dropdownStyle = '';
                            },
                            toggle() {
                                if (this.open) {
                                    this.open = false;
                                    this.dropdownStyle = '';
                                    return;
                                }
                                this.open = true;
                                this.$nextTick(() => {
                                    const trigger = this.$el.querySelector('.shadcn-select-trigger button');
                                    const rect = trigger.getBoundingClientRect();
                                    this.dropdownStyle = `top: ${rect.bottom + 4}px; left: ${rect.left}px; width: ${rect.width}px;`;
                                });
                            },
                            get isOpen() { return this.open; },
                            get displayValue() { return this.selectedLabel || this.placeholder; }
                        }">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Diet Type</label>
                            <div class="shadcn-select-trigger">
                                 <button
                                     type="button"
                                     @click="toggle()"
                                     @click.away="open = false; dropdownStyle = ''"
                                     :aria-expanded="open"
                                     aria-haspopup="listbox"
                                     class="flex h-9 w-full items-center justify-between border border-gray-300 bg-white px-2.5 py-1.5 text-xs placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 text-left"
                                     style="border-radius: 0.35rem !important;"
                                     :class="open && 'ring-2 ring-emerald-600 ring-offset-2'"
                                 >
                                     <span x-text="displayValue" :class="!value && 'text-gray-500'"></span>
                                     <svg class="h-3.5 w-3.5 shrink-0 text-gray-500 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                                     </svg>
                                 </button>
                                 <div
                                     x-show="open"
                                     x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="shadcn-select-content shadcn-select-bottom"
                                     role="listbox"
                                     :style="dropdownStyle"
                                 >
                                     <div
                                         class="shadcn-select-item"
                                         @click="select('', 'Select diet')"
                                         :aria-selected="!value"
                                         role="option"
                                     >
                                         Select diet
                                     </div>
                                    <template x-for="opt in options" :key="opt.value">
                                        <div
                                            class="shadcn-select-item"
                                            @click="select(opt.value, opt.label)"
                                            :aria-selected="value === opt.value"
                                            role="option"
                                            x-text="opt.label"
                                        ></div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Gadget Hours -->
                        <div class="min-w-0">
                            <label for="gadget_hours" class="block text-xs font-medium text-gray-700 mb-1">Gadget Hours</label>
                            <input
                                id="gadget_hours"
                                x-model="form.gadget_hours"
                                type="number"
                                min="0"
                                step="0.1"
                                class="flex h-9 w-full border border-gray-300 bg-white px-2.5 py-1.5 text-xs placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                style="border-radius: 0.35rem !important;"
                                placeholder="0.0"
                            >
                        </div>
                    </div>

                    <!-- Error Message -->
                    <template x-if="error">
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3" style="border-radius: 0.35rem !important;" x-text="error"></div>
                    </template>

                    <!-- Actions -->
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <button
                            type="submit"
                            class="shadcn-btn shadcn-btn-primary inline-flex items-center justify-center gap-2 whitespace-nowrap text-xs font-medium shadow-sm transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-emerald-600 text-white hover:bg-emerald-700 hover:shadow-md active:scale-[0.98] px-4 py-2 w-full sm:w-auto"
                            style="border-radius: 0.35rem !important;"
                            :disabled="loading"
                        >
                            <span x-show="!loading">Calculate</span>
                            <span x-show="loading">Calculating...</span>
                        </button>

                        <button
                            type="button"
                            @click="save"
                            class="shadcn-btn shadcn-btn-secondary inline-flex items-center justify-center gap-2 whitespace-nowrap text-xs font-medium shadow-sm transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-gray-900 text-white hover:bg-gray-800 hover:shadow-md active:scale-[0.98] px-4 py-2 w-full sm:w-auto"
                            style="border-radius: 0.35rem !important;"
                            :disabled="!result || saving"
                        >
                            <span x-show="!saving">Save Log</span>
                            <span x-show="saving">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Column: Results -->
            <div class="cc-result-column min-w-0">
                <div class="flex items-start justify-start">
                    <template x-if="result">
                        <div class="w-full max-w-none">
                            <div class="text-center">
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Emissions</h3>
                                <div class="mt-1.5">
                                    <span class="text-3xl font-bold text-gray-900" x-text="result.total_emission"></span>
                                    <span class="text-xl font-semibold text-gray-600 ml-1">kg CO₂</span>
                                </div>
                            </div>
                            
                            <div class="mt-4 grid grid-cols-3 gap-2">
                                <div class="text-center">
                                    <div class="text-[10px] text-gray-500 uppercase font-semibold">Transport</div>
                                    <div class="mt-0.5 text-base font-bold" x-text="result.breakdown.transport + ' kg'"></div>
                                </div>
                                <div class="text-center">
                                    <div class="text-[10px] text-gray-500 uppercase font-semibold">Diet</div>
                                    <div class="mt-0.5 text-base font-bold" x-text="result.breakdown.diet + ' kg'"></div>
                                </div>
                                <div class="text-center">
                                    <div class="text-[10px] text-gray-500 uppercase font-semibold">Gadgets</div>
                                    <div class="mt-0.5 text-base font-bold" x-text="result.breakdown.gadgets + ' kg'"></div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="!result">
                        <div class="w-full max-w-none text-center">
                            <div class="mb-3">
                                <svg class="w-12 h-12 text-emerald-500 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="7" height="7" x="3" y="3" rx="1" />
                                    <rect width="7" height="7" x="14" y="3" rx="1" />
                                    <rect width="7" height="7" x="14" y="14" rx="1" />
                                    <rect width="7" height="7" x="3" y="14" rx="1" />
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 mb-1.5">Calculate Your Impact</h3>
                            <p class="text-gray-600 text-xs">Fill in the form and click Calculate to see your carbon footprint estimate.</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
    <!-- End Carbon Calculator -->

    <!-- Toast notification -->
    <div
        x-show="toast.show"
        x-transition.opacity.duration.250ms
        x-cloak
        class="fixed bottom-4 right-4 bg-emerald-600 text-white px-4 py-2 shadow-lg"
        style="border-radius: 0.35rem !important;"
    >
        <p x-text="toast.message"></p>
    </div>
</section>

<script>
    function carbonCalculator({ calculateUrl, saveUrl, csrfToken, userId }) {
        return {
            form: {
                transport_type: '',
                distance: '',
                diet_type: '',
                gadget_hours: '',
            },
            result: null,
            loading: false,
            saving: false,
            error: '',
            toast: {
                show: false,
                message: '',
            },

            async calculate() {
                this.loading = true;
                this.error = '';

                try {
                    const response = await fetch(calculateUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify(this.form),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        this.error = this.extractError(data, 'Unable to calculate emissions right now.');
                        return;
                    }

                    this.result = data;
                } catch (error) {
                    this.error = 'Something went wrong while contacting the calculator API.';
                } finally {
                    this.loading = false;
                }
            },

            async save() {
                if (!this.result) {
                    return;
                }

                if (!userId) {
                    this.error = 'You need to be logged in before saving to your log.';
                    return;
                }

                this.saving = true;
                this.error = '';

                try {
                    const response = await fetch(saveUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            ...this.form,
                            user_id: userId,
                        }),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        this.error = this.extractError(data, 'Unable to save your carbon log right now.');
                        return;
                    }

                    window.dispatchEvent(new CustomEvent('carbon-log-saved', {
                        detail: data.log ?? null,
                    }));

                    this.showToast(data.message ?? 'Saved to your log.');
                } catch (error) {
                    this.error = 'Something went wrong while saving your carbon log.';
                } finally {
                    this.saving = false;
                }
            },

            extractError(data, fallback) {
                if (data?.message) {
                    return data.message;
                }

                const firstValidationError = Object.values(data?.errors ?? {})[0]?.[0];

                return firstValidationError ?? fallback;
            },

            showToast(message) {
                this.toast.message = message;
                this.toast.show = true;

                window.clearTimeout(this.toastTimeout);
                this.toastTimeout = window.setTimeout(() => {
                    this.toast.show = false;
                }, 2500);
            },
        };
    }
</script>