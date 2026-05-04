<div
    x-data="{
        draftModel: $wire.entangle('draft'),
        optimisticMessage: '',
        stickToBottom() {
            requestAnimationFrame(() => {
                const viewport = this.$refs.viewport;
                if (! viewport) {
                    return;
                }

                viewport.scrollTop = viewport.scrollHeight;
            });
        },
        previewMessage() {
            const value = this.draftModel.trim();

            if (! value) {
                return;
            }

            this.optimisticMessage = value;
        }
    }"
    x-init="stickToBottom()"
    x-on:livewire:navigated.window="stickToBottom()"
    x-effect="stickToBottom()"
    class="w-full"
>
    <section class="min-h-[78vh] overflow-hidden rounded-[0.35rem] border border-emerald-200 bg-[linear-gradient(180deg,#effef6_0%,#d9fbe8_58%,#fef3c7_100%)] shadow-sm dark:border-emerald-900/40 dark:bg-[linear-gradient(180deg,#0b2418_0%,#123823_62%,#3a2a0f_100%)]">
        <div class="flex justify-end px-1 py-2">
            <button
                type="button"
                wire:click="clearConversation"
                class="rounded-[0.35rem] border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-600 transition hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:border-zinc-600 dark:hover:bg-zinc-800 dark:hover:text-zinc-100"
            >
                New chat
            </button>
        </div>

        <div x-ref="viewport" class="h-[calc(78vh-162px)] overflow-y-auto bg-transparent px-3 py-6 sm:px-4">
            @if(empty($messages))
                <div class="mx-auto flex h-full max-w-4xl flex-col items-center justify-center text-center">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-700">Assistant</p>
                    <h3 class="mt-2 text-3xl font-black text-zinc-900 dark:text-zinc-100 sm:text-4xl">Eco Chat</h3>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-zinc-600 dark:text-zinc-400 sm:text-base">
                        Ask an AI assistant about Eco Track, your carbon logs, predictions, and practical ways to improve your sustainability habits.
                    </p>

                    <div class="mt-10 grid w-full gap-3 sm:grid-cols-2">
                        @foreach($starterPrompts as $prompt)
                            <button
                                type="button"
                                wire:click="usePrompt(@js($prompt))"
                                class="eco-page-card eco-page-card--teal rounded-[0.35rem] border border-zinc-200 bg-white px-5 py-5 text-left text-sm text-zinc-700 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-200 hover:bg-emerald-50 hover:text-zinc-900 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-300 dark:hover:border-emerald-800 dark:hover:bg-emerald-950/20 dark:hover:text-zinc-100"
                            >
                                {{ $prompt }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="mx-auto flex max-w-5xl flex-col gap-7">
                    @foreach($messages as $message)
                        <article class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[88%] sm:max-w-[82%]">
                                <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-zinc-400 {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                                    <span>{{ $message['role'] === 'user' ? 'You' : 'Eco Chat' }}</span>
                                    @if(($message['role'] === 'assistant') && !empty($message['meta']['model']))
                                        <span class="rounded-full bg-zinc-100 px-2 py-1 normal-case tracking-normal text-zinc-500">{{ $message['meta']['model'] }}</span>
                                    @endif
                                </div>

                                <div class="rounded-[0.35rem] px-5 py-4 text-sm leading-7 shadow-sm sm:text-[15px] {{ $message['role'] === 'user' ? 'bg-emerald-600 text-white shadow-[0_18px_40px_-20px_rgba(5,150,105,0.65)]' : 'eco-page-card eco-page-card--emerald border border-zinc-200 bg-white text-zinc-800 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-200' }}">
                                    {!! nl2br(e($message['content'])) !!}
                                </div>
                            </div>
                        </article>
                    @endforeach

                    <article wire:loading.flex wire:target="send" class="flex justify-end">
                        <div class="max-w-[88%] sm:max-w-[82%]">
                            <div class="mb-2 flex items-center justify-end gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-zinc-400">
                                <span>You</span>
                            </div>

                            <div class="rounded-[0.35rem] bg-emerald-600 px-5 py-4 text-sm leading-7 text-white shadow-[0_18px_40px_-20px_rgba(5,150,105,0.65)] sm:text-[15px]" x-text="optimisticMessage"></div>
                        </div>
                    </article>

                    @if($isSending)
                        <article class="flex justify-start">
                            <div class="max-w-[88%] sm:max-w-[82%]">
                                <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-zinc-400">
                                    <span>Eco Chat</span>
                                    <span class="rounded-full bg-emerald-100 px-2 py-1 normal-case tracking-normal text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300">typing...</span>
                                </div>

                                <div class="rounded-[0.35rem] border border-zinc-200 bg-white px-5 py-4 text-sm text-zinc-500 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-300">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-end gap-1">
                                            <span class="h-2 w-2 animate-bounce rounded-full bg-emerald-500 [animation-delay:0ms]"></span>
                                            <span class="h-2 w-2 animate-bounce rounded-full bg-emerald-500 [animation-delay:120ms]"></span>
                                            <span class="h-2 w-2 animate-bounce rounded-full bg-emerald-500 [animation-delay:240ms]"></span>
                                        </div>
                                        <span class="text-sm font-medium">Eco Chat is thinking through your question.</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endif
                </div>
            @endif
        </div>

        <div class="bg-transparent px-1 py-5">
            @if($error)
                <div class="mb-3 rounded-[0.35rem] border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                    {{ $error }}
                </div>
            @endif

            <form wire:submit="send" x-on:submit="previewMessage()" class="mx-auto max-w-6xl">
                <div class="overflow-hidden rounded-[0.35rem] border border-zinc-300 bg-white shadow-sm transition focus-within:border-emerald-400 focus-within:shadow-[0_0_0_4px_rgba(16,185,129,0.12)] dark:border-zinc-700 dark:bg-zinc-950">
                    <textarea
                        wire:model="draft"
                        rows="3"
                        placeholder="Ask about your logs, predictions, or how Eco Track works..."
                        class="w-full resize-none border-0 bg-transparent px-5 py-4 text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-0 dark:text-zinc-100 dark:placeholder:text-zinc-500 sm:text-[15px]"
                    ></textarea>

                    <div class="flex items-center justify-between gap-3 border-t border-zinc-200 px-4 py-3 dark:border-zinc-800">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Eco Chat stays focused on Eco Track and your recorded sustainability data.</p>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-[0.35rem] bg-emerald-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-emerald-300"
                            @disabled($isSending)
                        >
                            Send
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
