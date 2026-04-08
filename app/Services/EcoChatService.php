<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class EcoChatService
{
    public function __construct(
        protected EcoChatContextService $contextService,
    ) {
    }

    public function replyForUser($user, array $messages): array
    {
        $apiKey = (string) config('services.gemini.api_key');
        $model = (string) config('services.gemini.model', 'gemini-2.5-flash');

        if ($apiKey === '') {
            throw new RuntimeException('Gemini is not configured yet. Add GEMINI_API_KEY to your environment.');
        }

        $conversation = array_slice($messages, -12);

        $contents = [];

        foreach ($conversation as $message) {
            $role = ($message['role'] ?? 'user') === 'assistant' ? 'model' : 'user';
            $content = trim((string) ($message['content'] ?? ''));

            if ($content === '') {
                continue;
            }

            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $content],
                ],
            ];
        }

        try {
            $response = Http::acceptJson()
                ->timeout(45)
                ->post(
                    'https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent?key='.$apiKey,
                    [
                        'systemInstruction' => [
                            'parts' => [
                                ['text' => $this->buildInstructionBlock($user)],
                            ],
                        ],
                        'contents' => $contents,
                        'generationConfig' => [
                            'temperature' => 0.6,
                            'maxOutputTokens' => 700,
                        ],
                    ]
                )
                ->throw()
                ->json();
        } catch (ConnectionException) {
            throw new RuntimeException('Eco Chat could not reach Gemini right now. Please try again in a moment.');
        } catch (RequestException $exception) {
            $message = data_get($exception->response?->json(), 'error.message')
                ?: 'Gemini returned an error while generating the chat response.';

            throw new RuntimeException($message);
        }

        $reply = $this->extractText($response);

        if ($reply === '') {
            throw new RuntimeException('Eco Chat did not receive any reply text from Gemini.');
        }

        return [
            'content' => $reply,
            'model' => $model,
        ];
    }

    protected function buildInstructionBlock($user): string
    {
        return trim(implode("\n\n", [
            'You are Eco Chat, the AI assistant inside Eco Track.',
            'Your job is to answer only questions related to Eco Track, sustainability choices connected to Eco Track, the user\'s recorded carbon logs, AI predictions, achievements, leaderboard context, and how the app works.',
            'If the user asks about something unrelated to Eco Track or their sustainability activity, politely refuse and steer the conversation back to Eco Track topics.',
            'When discussing user performance, use only the factual information provided in the context. Do not invent logs, ranks, savings, or predictions.',
            'When the user asks for advice, keep it practical and tie it to the user\'s recorded patterns when possible.',
            'Keep answers clear, short, supportive, and product-aware. Prefer plain language over jargon.',
            $this->contextService->buildForUser($user),
        ]));
    }

    protected function extractText(array $response): string
    {
        $parts = data_get($response, 'candidates.0.content.parts', []);

        $segments = [];

        foreach ($parts as $part) {
            $text = trim((string) ($part['text'] ?? ''));

            if ($text !== '') {
                $segments[] = $text;
            }
        }

        return trim(implode("\n\n", $segments));
    }
}
