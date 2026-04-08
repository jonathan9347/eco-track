<div
    x-data="{
        stickToBottom() {
            requestAnimationFrame(() => {
                const viewport = this.$refs.viewport;
                if (! viewport) {
                    return;
                }

                viewport.scrollTop = viewport.scrollHeight;
            });
        }
    }"
    x-init="stickToBottom()"
    x-on:livewire:navigated.window="stickToBottom()"
    x-effect="stickToBottom()"
    class="w-full"
>
    <section class="min-h-[78vh] overflow-hidden bg-transparent">
        <div class="flex justify-end px-1 py-2">
            <button
                type="button"
                wire:click="clearConversation"
                class="rounded-lg border border-zinc-200 px-3 py-2 text-sm font-medium text-zinc-600 transition hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-900"
            >
                New chat
            </button>
        </div>

        <div x-ref="viewport" class="h-[calc(78vh-162px)] overflow-y-auto bg-transparent px-1 py-6">
            @if(empty($messages))
                <div class="mx-auto flex h-full max-w-4xl flex-col items-center justify-center text-center">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-700">Assistant</p>
                    <h3 class="mt-2 text-3xl font-black text-zinc-900 sm:text-4xl">Eco Chat</h3>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-zinc-600 sm:text-base">
                        Ask an AI assistant about Eco Track, your carbon logs, predictions, and practical ways to improve your sustainability habits.
                    </p>

                    <div class="mt-10 grid w-full gap-3 sm:grid-cols-2">
                        @foreach($starterPrompts as $prompt)
                            <button
                                type="button"
                                wire:click="usePrompt(@js($prompt))"
                                class="rounded-[1.35rem] border border-zinc-200 bg-white px-5 py-5 text-left text-sm text-zinc-700 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-200 hover:bg-emerald-50 hover:text-zinc-900"
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

                                <div class="rounded-[1.45rem] px-5 py-4 text-sm leading-7 shadow-sm sm:text-[15px] {{ $message['role'] === 'user' ? 'bg-emerald-600 text-white' : 'border border-zinc-200 bg-white text-zinc-800' }}">
                                    {!! nl2br(e($message['content'])) !!}
                                </div>
                            </div>
                        </article>
                    @endforeach

                    @if($isSending)
                        <article class="flex justify-start">
                            <div class="max-w-[88%] rounded-[1.45rem] border border-zinc-200 bg-white px-5 py-4 text-sm text-zinc-500 shadow-sm">
                                <div class="flex items-center gap-2">
                                    <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>
                                    <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500 [animation-delay:120ms]"></span>
                                    <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500 [animation-delay:240ms]"></span>
                                </div>
                            </div>
                        </article>
                    @endif
                </div>
            @endif
        </div>

        <div class="bg-transparent px-1 py-5">
            @if($error)
                <div class="mb-3 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                    {{ $error }}
                </div>
            @endif

            <form wire:submit="send" class="mx-auto max-w-6xl">
                <div class="overflow-hidden rounded-[1.4rem] border border-zinc-300 bg-white focus-within:border-emerald-400">
                    <textarea
                        wire:model="draft"
                        rows="3"
                        placeholder="Ask about your logs, predictions, or how Eco Track works..."
                        class="w-full resize-none border-0 bg-transparent px-5 py-4 text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-0 sm:text-[15px]"
                    ></textarea>

                    <div class="flex items-center justify-between gap-3 border-t border-zinc-200 px-4 py-3">
                        <p class="text-xs text-zinc-500">Eco Chat stays focused on Eco Track and your recorded sustainability data.</p>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-emerald-300"
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
