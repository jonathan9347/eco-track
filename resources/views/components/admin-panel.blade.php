<section x-data="{ tab: 'users' }" class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 rounded-xl">
        <div
            class="overflow-hidden border border-emerald-100 px-6 py-8 text-white shadow-lg sm:px-8"
            style="background: linear-gradient(135deg, #052e16 0%, #166534 55%, #4d7c0f 100%); border-radius: 0.35rem !important;"
        >
            <p class="text-sm font-semibold uppercase tracking-[0.28em]" style="color: rgba(236, 253, 245, 0.82);">Admin Panel</p>
            <h1 class="mt-3 text-3xl font-black sm:text-4xl">Platform controls for Eco-Track.</h1>
            <p class="mt-3 max-w-3xl text-sm leading-7 sm:text-base" style="color: rgba(240, 253, 244, 0.90);">
                Manage users, emission factors, classrooms, announcements, and weekly challenges from one admin-only page.
            </p>
        </div>

        @if ($statusMessage)
            <div class="border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-950/20 dark:text-emerald-400" style="border-radius: 0.35rem !important;">
                {{ $statusMessage }}
            </div>
        @endif

        <!-- Tabs Component (shadcn-inspired) -->
        <div class="inline-flex w-fit items-center rounded-lg bg-zinc-100 p-1 dark:bg-zinc-800">
            <button type="button" @click="tab = 'users'" class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50" :class="tab === 'users' ? 'bg-white text-emerald-700 shadow-sm dark:bg-zinc-950 dark:text-emerald-400' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'">User Management</button>
            <button type="button" @click="tab = 'factors'" class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50" :class="tab === 'factors' ? 'bg-white text-emerald-700 shadow-sm dark:bg-zinc-950 dark:text-emerald-400' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'">Factor Management</button>
            <button type="button" @click="tab = 'classrooms'" class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50" :class="tab === 'classrooms' ? 'bg-white text-emerald-700 shadow-sm dark:bg-zinc-950 dark:text-emerald-400' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'">Classroom Management</button>
            <button type="button" @click="tab = 'announcements'" class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50" :class="tab === 'announcements' ? 'bg-white text-emerald-700 shadow-sm dark:bg-zinc-950 dark:text-emerald-400' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'">Announcements</button>
            <button type="button" @click="tab = 'challenges'" class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50" :class="tab === 'challenges' ? 'bg-white text-emerald-700 shadow-sm dark:bg-zinc-950 dark:text-emerald-400' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'">Challenges</button>
        </div>

        <div x-show="tab === 'users'" class="border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:shadow-none" style="border-radius: 0.35rem !important;">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-2xl font-black text-zinc-900 dark:text-zinc-100">User Management</h2>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Search users, filter by classroom, update classroom assignments, toggle admin, and reset logs.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <input wire:model.live.debounce.300ms="userSearch" type="text" placeholder="Search name or email" class="border border-zinc-200 px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                    <select wire:model.live="userClassroomFilter" class="border border-zinc-200 px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                        <option value="">All classrooms</option>
                        @foreach ($classrooms as $classroom)
                            <option value="{{ $classroom['name'] }}">{{ $classroom['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 overflow-hidden border border-zinc-200 dark:border-zinc-800" style="border-radius: 0.35rem !important;">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                        <thead class="bg-zinc-50 dark:bg-zinc-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.22em] text-zinc-500">User</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.22em] text-zinc-500">Classroom</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.22em] text-zinc-500">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.22em] text-zinc-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @forelse ($users as $user)
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-900">
                                    <td class="px-4 py-4 text-sm">
                                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</div>
                                        <div class="text-zinc-500 dark:text-zinc-400">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        <div x-data="{ classroom: @js($user->classroom ?? '') }" class="flex gap-2">
                                            <select x-model="classroom" class="min-w-44 border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                                                <option value="">Unassigned</option>
                                                @foreach ($classrooms as $classroom)
                                                    <option value="{{ $classroom['name'] }}">{{ $classroom['name'] }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" @click="$wire.saveUserClassroom({{ $user->id }}, classroom)" class="bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700" style="border-radius: 0.35rem !important;">Save</button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold {{ $user->is_admin ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}" style="border-radius: 0.35rem !important;">
                                            {{ $user->is_admin ? 'Admin' : 'Student' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        <div class="flex flex-wrap gap-2">
                                            <button wire:click="toggleAdmin({{ $user->id }})" class="border border-zinc-200 px-3 py-2 text-xs font-semibold text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800" style="border-radius: 0.35rem !important;">Toggle Admin</button>
                                            <button wire:click="resetLogs({{ $user->id }})" class="border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-100" style="border-radius: 0.35rem !important;">Reset Logs</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">No users matched your filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="tab === 'factors'" class="border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:shadow-none" style="border-radius: 0.35rem !important;">
            <h2 class="text-2xl font-black text-zinc-900 dark:text-zinc-100">Factor Management</h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Edit transport, diet, and gadget fallback factors, then save them to Firebase. API values below are cached snapshots used when external services are reachable.</p>
            <div class="mt-6 grid gap-6 xl:grid-cols-3">
                <div class="bg-emerald-50 p-5 ring-1 ring-emerald-100 dark:bg-emerald-950/20 dark:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Transport</h3>
                    <div class="mt-4 grid gap-3">
                        @foreach (['jeepney', 'bus', 'tricycle', 'car', 'walking'] as $key)
                            <label class="grid gap-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                <span>{{ ucfirst($key) }}</span>
                                <input wire:model="factorForm.transport.{{ $key }}" type="number" step="0.01" min="0" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                            </label>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                API: {{ number_format((float) data_get($apiFactorStatus, "transport.$key.value", 0), 2) }} kg CO2/km
                                | Source: {{ ucfirst((string) data_get($apiFactorStatus, "transport.$key.source", 'fallback')) }}
                                | Last synced: {{ data_get($apiFactorStatus, "transport.$key.last_synced_at", 'Not synced yet') }}
                            </p>
                        @endforeach
                    </div>
                </div>
                <div class="bg-emerald-50 p-5 ring-1 ring-emerald-100 dark:bg-emerald-950/20 dark:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Diet</h3>
                    <div class="mt-4 grid gap-3">
                        @foreach (['meat', 'average', 'vegetarian', 'plant_based'] as $key)
                            <label class="grid gap-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                <span>{{ $key === 'plant_based' ? 'Plant-based' : ucfirst($key) }}</span>
                                <input wire:model="factorForm.diet.{{ $key }}" type="number" step="0.01" min="0" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                            </label>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                API: {{ number_format((float) data_get($apiFactorStatus, "diet.$key.value", 0), 2) }} kg CO2/day
                                | Source: {{ ucfirst((string) data_get($apiFactorStatus, "diet.$key.source", 'fallback')) }}
                                | Last synced: {{ data_get($apiFactorStatus, "diet.$key.last_synced_at", 'Not synced yet') }}
                            </p>
                        @endforeach
                    </div>
                </div>
                <div class="bg-emerald-50 p-5 ring-1 ring-emerald-100 dark:bg-emerald-950/20 dark:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Gadgets</h3>
                    <label class="mt-4 grid gap-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <span>Per Hour</span>
                        <input wire:model="factorForm.gadgets.per_hour" type="number" step="0.01" min="0" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                    </label>
                    <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                        API electricity factor: {{ number_format((float) data_get($apiFactorStatus, 'gadgets.electricity.value', 0), 4) }} kg CO2/kWh
                        | Source: {{ ucfirst((string) data_get($apiFactorStatus, 'gadgets.electricity.source', 'fallback')) }}
                        | Last synced: {{ data_get($apiFactorStatus, 'gadgets.electricity.last_synced_at', 'Not synced yet') }}
                    </p>
                </div>
            </div>
            <button wire:click="saveFactors" class="mt-6 bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700" style="border-radius: 0.35rem !important;">Save to Firebase</button>
        </div>

        <div x-show="tab === 'classrooms'" class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
            <div class="border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:shadow-none" style="border-radius: 0.35rem !important;">
                <h2 class="text-2xl font-black text-zinc-900 dark:text-zinc-100">Classroom Management</h2>
                <div class="mt-6 grid gap-4">
                    <input wire:model="classroomForm.name" type="text" placeholder="Classroom name" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                    <input wire:model="classroomForm.section" type="text" placeholder="Section" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                    <textarea wire:model="classroomForm.description" rows="4" placeholder="Description" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;"></textarea>
                </div>
                <button wire:click="saveClassroom" class="mt-6 bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700" style="border-radius: 0.35rem !important;">{{ $editingClassroomId ? 'Update Classroom' : 'Create Classroom' }}</button>
            </div>
            <div class="border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:shadow-none" style="border-radius: 0.35rem !important;">
                <h2 class="text-2xl font-black text-zinc-900 dark:text-zinc-100">Classroom Stats</h2>
                <div class="mt-6 space-y-4">
                    @forelse ($classrooms as $classroom)
                        <article class="border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none" style="border-radius: 0.35rem !important;">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ $classroom['name'] }}</h3>
                                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $classroom['description'] ?: 'No description yet.' }}</p>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-3">
                                    <div class="bg-emerald-50 px-4 py-3 ring-1 ring-emerald-100 dark:bg-emerald-950/20 dark:ring-emerald-900/40" style="border-radius: 0.35rem !important;"><p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700">Students</p><p class="mt-2 text-xl font-black text-zinc-900 dark:text-zinc-100">{{ $classroom['user_count'] }}</p></div>
                                    <div class="bg-emerald-50 px-4 py-3 ring-1 ring-emerald-100 dark:bg-emerald-950/20 dark:ring-emerald-900/40" style="border-radius: 0.35rem !important;"><p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700">Logs</p><p class="mt-2 text-xl font-black text-zinc-900 dark:text-zinc-100">{{ $classroom['log_count'] }}</p></div>
                                    <div class="bg-emerald-50 px-4 py-3 ring-1 ring-emerald-100 dark:bg-emerald-950/20 dark:ring-emerald-900/40" style="border-radius: 0.35rem !important;"><p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700">Avg CO2</p><p class="mt-2 text-xl font-black text-zinc-900 dark:text-zinc-100">{{ $classroom['average_emission'] }}</p></div>
                                </div>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <button wire:click="editClassroom('{{ $classroom['id'] }}')" class="border border-zinc-200 px-3 py-2 text-xs font-semibold text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800" style="border-radius: 0.35rem !important;">Edit</button>
                                <button wire:click="deleteClassroom('{{ $classroom['id'] }}')" class="border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100" style="border-radius: 0.35rem !important;">Delete</button>
                            </div>
                        </article>
                    @empty
                        <div class="border border-dashed border-zinc-200 bg-zinc-50 px-6 py-10 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400" style="border-radius: 0.35rem !important;">No classrooms created yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div x-show="tab === 'announcements'" class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
            <div class="border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:shadow-none" style="border-radius: 0.35rem !important;">
                <h2 class="text-2xl font-black text-zinc-900 dark:text-zinc-100">Announcements</h2>
                <div class="mt-6 grid gap-4">
                    <input wire:model="announcementForm.title" type="text" placeholder="Title" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                    <textarea wire:model="announcementForm.message" rows="5" placeholder="Message" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;"></textarea>
                    <select wire:model="announcementForm.target_audience" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                        <option value="all">All Users</option>
                        <option value="students">Students</option>
                        <option value="teachers">Teachers</option>
                        <option value="admins">Admins</option>
                    </select>
                    <input wire:model="announcementForm.scheduled_for" type="datetime-local" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                </div>
                <button wire:click="saveAnnouncement" class="mt-6 bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700" style="border-radius: 0.35rem !important;">Create Announcement</button>
            </div>
            <div class="border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:shadow-none" style="border-radius: 0.35rem !important;">
                <h2 class="text-2xl font-black text-zinc-900 dark:text-zinc-100">Recent Announcements</h2>
                <div class="mt-6 space-y-4">
                    @forelse ($announcements as $announcement)
                        <article class="border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none" style="border-radius: 0.35rem !important;">
                            <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ $announcement['title'] ?? 'Untitled' }}</h3>
                                <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100 dark:bg-emerald-950/30 dark:text-emerald-400 dark:ring-emerald-900/40">{{ $announcement['target_audience'] ?? 'all' }}</span>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-zinc-600 dark:text-zinc-400">{{ $announcement['message'] ?? '' }}</p>
                            <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">Scheduled: {{ $announcement['scheduled_for'] ?? 'Immediately' }}</p>
                        </article>
                    @empty
                        <div class="border border-dashed border-zinc-200 bg-zinc-50 px-6 py-10 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400" style="border-radius: 0.35rem !important;">No announcements created yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div x-show="tab === 'challenges'" class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
            <div class="border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:shadow-none" style="border-radius: 0.35rem !important;">
                <h2 class="text-2xl font-black text-zinc-900 dark:text-zinc-100">Challenges</h2>
                <div class="mt-6 grid gap-4">
                    <input wire:model="challengeForm.title" type="text" placeholder="Challenge title" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                    <textarea wire:model="challengeForm.description" rows="4" placeholder="Challenge description" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;"></textarea>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <select wire:model="challengeForm.metric" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                            <option value="walking_days">Walking Days</option>
                            <option value="plant_based_meals">Plant-based Meals</option>
                            <option value="energy_saver_days">Energy Saver Days</option>
                            <option value="co2_saved">CO2 Saved</option>
                            <option value="streak_days">Streak Days</option>
                            <option value="log_days">Log Days</option>
                        </select>
                        <input wire:model="challengeForm.target" type="number" min="1" placeholder="Target" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                        <input wire:model="challengeForm.points" type="number" min="1" placeholder="Points" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <input wire:model="challengeForm.starts_at" type="date" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                        <input wire:model="challengeForm.ends_at" type="date" class="border border-zinc-200 px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                    </div>
                    <label class="inline-flex items-center gap-3 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <input wire:model="challengeForm.is_active" type="checkbox" class="h-4 w-4 rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500">
                        <span>Set as active challenge</span>
                    </label>
                </div>
                <button wire:click="saveChallenge" class="mt-6 bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700" style="border-radius: 0.35rem !important;">Create Weekly Challenge</button>
            </div>
            <div class="border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:shadow-none" style="border-radius: 0.35rem !important;">
                <h2 class="text-2xl font-black text-zinc-900 dark:text-zinc-100">Challenge Queue</h2>
                <div class="mt-6 space-y-4">
                    @forelse ($challenges as $challenge)
                        <article class="border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none" style="border-radius: 0.35rem !important;">
                            <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ $challenge['title'] ?? 'Untitled' }}</h3>
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ !empty($challenge['is_active']) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}">{{ !empty($challenge['is_active']) ? 'Active' : 'Queued' }}</span>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-zinc-600 dark:text-zinc-400">{{ $challenge['description'] ?? '' }}</p>
                            <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">Metric: {{ $challenge['metric'] ?? 'log_days' }} | Target: {{ $challenge['target'] ?? 0 }} | Points: {{ $challenge['points'] ?? 100 }}</p>
                        </article>
                    @empty
                        <div class="border border-dashed border-zinc-200 bg-zinc-50 px-6 py-10 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400" style="border-radius: 0.35rem !important;">No challenges created yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
