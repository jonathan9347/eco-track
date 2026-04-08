<?php

namespace App\Livewire;

use App\Services\EcoChatService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class EcoChat extends Component
{
    public array $messages = [];

    public string $draft = '';

    public bool $isSending = false;

    public ?string $error = null;

    public array $starterPrompts = [
        'What does my recent carbon activity say about my habits?',
        'Which part of my footprint should I reduce first?',
        'Explain how Eco Track calculates my emissions.',
        'Give me simple actions based on my latest logs.',
    ];

    public function mount(): void
    {
        $this->messages = session()->get($this->sessionKey(), []);
    }

    public function usePrompt(string $prompt): void
    {
        $this->draft = $prompt;
    }

    public function clearConversation(): void
    {
        $this->messages = [];
        $this->draft = '';
        $this->error = null;

        session()->forget($this->sessionKey());
    }

    public function send(): void
    {
        $message = trim($this->draft);

        if ($message === '') {
            return;
        }

        $this->error = null;
        $this->isSending = true;

        $this->messages[] = [
            'id' => (string) Str::uuid(),
            'role' => 'user',
            'content' => $message,
        ];

        $this->draft = '';
        $this->persistMessages();

        try {
            $reply = app(EcoChatService::class)->replyForUser(auth()->user(), $this->messages);

            $this->messages[] = [
                'id' => (string) Str::uuid(),
                'role' => 'assistant',
                'content' => $reply['content'],
                'meta' => [
                    'model' => $reply['model'] ?? null,
                ],
            ];

            $this->persistMessages();
        } catch (\Throwable $exception) {
            $this->error = $exception->getMessage();
            array_pop($this->messages);
            $this->persistMessages();
        } finally {
            $this->isSending = false;
        }
    }

    public function render(): View
    {
        return view('livewire.eco-chat');
    }

    protected function persistMessages(): void
    {
        session()->put($this->sessionKey(), array_slice($this->messages, -20));
    }

    protected function sessionKey(): string
    {
        return 'eco-chat.'.(string) auth()->id();
    }
}
