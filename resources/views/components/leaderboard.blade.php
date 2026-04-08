<section
    wire:poll.10s="tick"
    x-data="leaderboardBoard({
        leaderboardUrl: '{{ url('/api/leaderboard') }}',
        csrfToken: '{{ csrf_token() }}',
    })"
    x-init="init()"
    x-on:leaderboard-refresh.window="fetchLeaderboard()"
    class="w-full px-2 py-2"
>
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* shadcn-style select dropdown */
        .shadcn-select-trigger {
            position: relative;
        }

                        /* Overlay dropdown - floats above everything */
                        .shadcn-select-content {
                            position: fixed !important;
                            z-index: 9999 !important;
                            min-width: 180px !important;
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

        .shadcn-select-item {
            padding: 0.375rem 0.5rem;
            font-size: 0.875rem;
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

        .dark .shadcn-select-content {
            background: #09090b !important;
            border-color: #3f3f46 !important;
            box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.45), 0 8px 10px -6px rgb(0 0 0 / 0.35) !important;
        }

        .dark .shadcn-select-item {
            color: #e5e7eb;
        }

        .dark .shadcn-select-item:hover,
        .dark .shadcn-select-item[aria-selected="true"] {
            background-color: #18181b;
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

    <div class="overflow-hidden" style="border-radius: 0.35rem !important;">
        <div class="pb-6">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-700">Leaderboard</p>
                <h2 class="mt-1 text-2xl font-black text-zinc-900 dark:text-zinc-100 sm:text-3xl">Friendly competition for greener classrooms.</h2>
                <p class="mt-1 max-w-2xl text-sm text-zinc-600 dark:text-zinc-400">
                    Lowest average carbon footprint wins. Compare classrooms, then drill into student rankings inside each class.
                </p>
            </div>
        </div>

        <div class="space-y-5">
            <!-- Tabs Component (shadcn-inspired) -->
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="inline-flex items-center rounded-lg bg-zinc-100 p-1 dark:bg-zinc-800">
                    <button
                        type="button"
                        @click="activeTab = 'classrooms'"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                        :class="activeTab === 'classrooms'
                            ? 'bg-white text-emerald-700 shadow-sm dark:bg-zinc-950 dark:text-emerald-400'
                            : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
                    >
                        Classroom Rankings
                    </button>
                    <button
                        type="button"
                        @click="activeTab = 'students'"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                        :class="activeTab === 'students'
                            ? 'bg-white text-emerald-700 shadow-sm dark:bg-zinc-950 dark:text-emerald-400'
                            : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
                    >
                        Student Rankings
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <label for="classroom-filter" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Selected classroom</label>
                    <div class="shadcn-select-trigger" x-data="{
                        open: false,
                        dropdownStyle: '',
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
                                const offsetLeft = Math.max(8, rect.left);
                                this.dropdownStyle = `top: ${rect.bottom + 4}px; left: ${offsetLeft}px; width: ${rect.width}px;`;
                            });
                        }
                    }">
                        <button
                            type="button"
                            @click="toggle()"
                            @click.away="open = false; dropdownStyle = ''"
                            :aria-expanded="open"
                            aria-haspopup="listbox"
                            class="flex h-9 min-w-[180px] items-center justify-between border border-gray-300 bg-white px-3 py-1.5 text-left text-sm placeholder:text-gray-500 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                            style="border-radius: 0.35rem !important;"
                        >
                            <span x-text="selectedClassroom || 'Select classroom'" :class="!selectedClassroom && 'text-gray-500'"></span>
                            <svg class="h-3.5 w-3.5 shrink-0 text-gray-500 transition-transform duration-200 dark:text-zinc-400" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                            <template x-for="classroom in classrooms" :key="classroom.classroom">
                                <div
                                    class="shadcn-select-item"
                                    @click="selectedClassroom = classroom.classroom; open = false; dropdownStyle = ''"
                                    :aria-selected="selectedClassroom === classroom.classroom"
                                    role="option"
                                    x-text="classroom.classroom"
                                ></div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <template x-if="error">
                <div class="border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" style="border-radius: 0.35rem !important;" x-text="error"></div>
            </template>

            <template x-if="activeTab === 'classrooms'">
                <div class="space-y-4">
                    <template x-if="classrooms.length === 0 && !loading">
                        <div class="border border-dashed border-zinc-200 bg-zinc-50 px-6 py-10 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400" style="border-radius: 0.35rem !important;">
                            No classroom leaderboard data available yet.
                        </div>
                    </template>

                    <div class="grid gap-4">
                        <template x-for="classroom in classrooms" :key="classroom.classroom">
                            <article class="border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-950" style="border-radius: 0.35rem !important;">
                                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-14 w-14 items-center justify-center bg-emerald-50 text-2xl ring-1 ring-emerald-100 dark:bg-emerald-950/40 dark:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                                            <span x-text="medal(classroom.rank)"></span>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-zinc-500 dark:text-zinc-400">Rank <span x-text="classroom.rank"></span></p>
                                            <h3 class="mt-2 text-2xl font-black text-zinc-900 dark:text-zinc-100" x-text="classroom.classroom"></h3>
                                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                                                Lowest average wins. This classroom is saving
                                                <span class="font-semibold text-emerald-700" x-text="`${Number(classroom.classroom_savings_total).toFixed(2)} kg CO2`"></span>
                                                compared with the highest-emission classroom baseline.
                                            </p>
                                        </div>
                                    </div>

                                        <div class="grid gap-3 sm:grid-cols-3">
                                            <div class="bg-emerald-50 px-4 py-4 ring-1 ring-emerald-100 dark:bg-emerald-950/40 dark:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700">Average Carbon</p>
                                            <p class="mt-2 text-2xl font-black text-zinc-900 dark:text-zinc-100" x-text="`${Number(classroom.average_emission).toFixed(2)} kg`"></p>
                                        </div>
                                            <div class="bg-white px-4 py-4 ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-800" style="border-radius: 0.35rem !important;">
                                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-zinc-500 dark:text-zinc-400">Total Emission</p>
                                            <p class="mt-2 text-2xl font-black text-zinc-900 dark:text-zinc-100" x-text="`${Number(classroom.total_emission).toFixed(2)} kg`"></p>
                                        </div>
                                            <div class="bg-white px-4 py-4 ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-800" style="border-radius: 0.35rem !important;">
                                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-zinc-500 dark:text-zinc-400">Logs</p>
                                            <p class="mt-2 text-2xl font-black text-zinc-900 dark:text-zinc-100" x-text="classroom.log_count"></p>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </template>
                    </div>
                </div>
            </template>

            <template x-if="activeTab === 'students'">
                <div class="space-y-4">
                    <div class="overflow-hidden border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950" style="border-radius: 0.35rem !important;">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                                <thead class="bg-zinc-50 dark:bg-zinc-900">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.22em] text-zinc-500">Rank</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.22em] text-zinc-500">Student</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.22em] text-zinc-500">Average Carbon</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.22em] text-zinc-500">Total Emission</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.22em] text-zinc-500">Logs</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                    <template x-if="selectedStudents.length === 0 && !loading">
                                        <tr>
                                            <td colspan="5" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                                No student rankings found for the selected classroom yet.
                                            </td>
                                        </tr>
                                    </template>

                                    <template x-for="student in selectedStudents" :key="`${selectedClassroom}-${student.user_id}-${student.rank}`">
                                        <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-900">
                                            <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                                <span x-text="`${medal(student.rank)} ${student.rank}`"></span>
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-zinc-900 dark:text-zinc-100" x-text="student.user_name"></td>
                                            <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-emerald-700 dark:text-emerald-400" x-text="`${Number(student.average_emission).toFixed(2)} kg`"></td>
                                            <td class="whitespace-nowrap px-4 py-4 text-sm text-zinc-700 dark:text-zinc-300" x-text="`${Number(student.total_emission).toFixed(2)} kg`"></td>
                                            <td class="whitespace-nowrap px-4 py-4 text-sm text-zinc-700 dark:text-zinc-300" x-text="student.log_count"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</section>

<script>
    function leaderboardBoard({ leaderboardUrl, csrfToken }) {
        return {
            activeTab: 'classrooms',
            classrooms: [],
            studentsByClassroom: {},
            selectedClassroom: '',
            loading: false,
            error: '',

            init() {
                this.fetchLeaderboard();
                this.$watch('classrooms', () => {
                    if (!this.selectedClassroom && this.classrooms.length > 0) {
                        this.selectedClassroom = this.classrooms[0].classroom;
                    }
                });
            },

            async fetchLeaderboard() {
                this.loading = true;
                this.error = '';

                try {
                    const response = await fetch(leaderboardUrl, {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        this.error = data?.message ?? 'Unable to load leaderboard data right now.';
                        return;
                    }

                    this.classrooms = data.classroom_rankings ?? [];
                    this.studentsByClassroom = data.student_rankings ?? {};

                    if (!this.selectedClassroom && this.classrooms.length > 0) {
                        this.selectedClassroom = this.classrooms[0].classroom;
                    }

                    if (this.selectedClassroom && !this.studentsByClassroom[this.selectedClassroom] && this.classrooms.length > 0) {
                        this.selectedClassroom = this.classrooms[0].classroom;
                    }
                } catch (error) {
                    this.error = 'Something went wrong while loading the leaderboard.';
                } finally {
                    this.loading = false;
                }
            },

            medal(rank) {
                if (rank === 1) return '🥇';
                if (rank === 2) return '🥈';
                if (rank === 3) return '🥉';
                return '•';
            },

            get selectedStudents() {
                return this.studentsByClassroom[this.selectedClassroom] ?? [];
            },
        };
    }
</script>
