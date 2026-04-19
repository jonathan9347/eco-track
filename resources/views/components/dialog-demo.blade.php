<div>
    <!-- Dialog Trigger Button -->
    <button
        type="button"
        onclick="document.getElementById('dialog-modal-{{ $this->getId() }}').classList.remove('hidden');"
        class="flex items-center gap-2 rounded-md border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-500 shadow-sm hover:bg-zinc-100 transition-colors"
        style="min-width: 280px;"
    >
        <svg class="mr-2 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.3-4.3"/>
        </svg>
        <span class="flex-1 text-left">Open Dialog</span>
        <kbd class="pointer-events-none inline-flex h-5 select-none items-center gap-1 rounded border border-zinc-200 bg-zinc-50 px-1.5 font-mono text-[10px] font-medium text-zinc-500 opacity-100">⌘K</kbd>
    </button>

    <!-- Dialog Modal -->
    <div 
        id="dialog-modal-{{ $this->getId() }}"
        class="fixed inset-0 flex items-center justify-center hidden"
        style="z-index: 9999;"
    >
        <!-- Full-screen overlay with backdrop blur effect -->
        <div 
            class="fixed inset-0 bg-black/50 backdrop-blur-md"
            style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; z-index: 99999 !important; background-color: rgba(0, 0, 0, 0.5) !important;"
            onclick="this.parentElement.classList.add('hidden')"
        ></div>
        
        <!-- Dialog Content -->
        <div 
            class="relative w-full max-w-lg rounded-lg border border-zinc-200 bg-white shadow-2xl"
            style="z-index: 10000; animation: dialog-in 200ms ease-out;"
            onclick="event.stopPropagation()"
        >
            <!-- Dialog Header -->
            <div class="flex items-center border-b border-zinc-100 px-4 py-3">
                <svg class="mr-2 h-5 w-5 shrink-0 text-zinc-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
                <h2 class="text-lg font-semibold text-zinc-900">Edit Profile</h2>
            </div>

            <!-- Dialog Content -->
            <div class="p-4">
                <p class="mb-4 text-sm text-zinc-600">Make changes to your profile here. Click save when you're done.</p>
                
                <!-- Form Fields -->
                <div class="space-y-4">
                    <div>
                        <label for="name-{{ $this->getId() }}" class="block text-sm font-medium text-zinc-700 mb-1">Name</label>
                        <input
                            id="name-{{ $this->getId() }}"
                            type="text"
                            class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                            placeholder="Enter your name"
                        />
                    </div>
                    
                    <div>
                        <label for="username-{{ $this->getId() }}" class="block text-sm font-medium text-zinc-700 mb-1">Username</label>
                        <input
                            id="username-{{ $this->getId() }}"
                            type="text"
                            class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                            placeholder="Enter your username"
                        />
                    </div>
                </div>

                <!-- Suggestions Group -->
                <div class="mt-6 pt-4 border-t border-zinc-100">
                    <p class="mb-2 text-xs font-medium text-zinc-500 uppercase tracking-wider">Suggestions</p>
                    <div class="space-y-1">
                        <a href="{{ url('/carbon-history') }}" wire:navigate class="relative flex cursor-pointer select-none items-center rounded-md px-2 py-2 text-sm outline-none hover:bg-zinc-100">
                            <svg class="mr-2 h-4 w-4 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                            <span>Carbon Log</span>
                        </a>
                        <a href="{{ url('/carbon-history') }}" wire:navigate class="relative flex cursor-pointer select-none items-center rounded-md px-2 py-2 text-sm outline-none hover:bg-zinc-100">
                            <svg class="mr-2 h-4 w-4 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                                <line x1="16" x2="16" y1="2" y2="6"/>
                                <line x1="8" x2="8" y1="2" y2="6"/>
                                <line x1="3" x2="21" y1="10" y2="10"/>
                            </svg>
                            <span>View Logs</span>
                        </a>
                    </div>
                </div>

                <!-- Settings Group -->
                <div class="mt-4 pt-4 border-t border-zinc-100">
                    <p class="mb-2 text-xs font-medium text-zinc-500 uppercase tracking-wider">Settings</p>
                    <div class="space-y-1">
                        <div class="relative flex cursor-pointer select-none items-center rounded-md px-2 py-2 text-sm outline-none hover:bg-zinc-100">
                            <svg class="mr-2 h-4 w-4 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <span>Settings</span>
                            <span class="ml-auto text-[10px] font-medium text-zinc-400">⌘S</span>
                        </div>
                        <div class="relative flex cursor-pointer select-none items-center rounded-md px-2 py-2 text-sm outline-none hover:bg-zinc-100">
                            <svg class="mr-2 h-4 w-4 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <span>Profile</span>
                            <span class="ml-auto text-[10px] font-medium text-zinc-400">⌘P</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dialog Footer -->
            <div class="flex items-center justify-end gap-2 border-t border-zinc-100 px-4 py-3">
                <button
                    type="button"
                    onclick="this.closest('.fixed').classList.add('hidden')"
                    class="inline-flex items-center justify-center rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                    Save changes
                </button>
            </div>
        </div>
    </div>

    <script>
    (function() {
        const modalId = 'dialog-modal-{{ $this->getId() }}';
        
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                if (modal.classList.contains('hidden')) {
                    modal.classList.remove('hidden');
                } else {
                    modal.classList.add('hidden');
                }
            }
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        });
    })();
    </script>

    <style>
    @keyframes dialog-in {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    </style>
</div>
