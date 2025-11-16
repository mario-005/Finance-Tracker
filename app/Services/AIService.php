<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class AIService
{
    public function askFinancialAdvisor(User $user, string $message, array $structuredData): array
    {
        $apiKey = config('services.ai.key');
        $endpoint = config('services.ai.endpoint', 'https://api.openai.com/v1/chat/completions');

        // Check if API key is configured
        if (empty($apiKey)) {
            return [
                'error' => true,
                'message' => 'AI API key not configured. Please set SERVICES_AI_KEY in .env file to use the AI advisor. Example: SERVICES_AI_KEY=sk-your-api-key-here'
            ];
        }

        $system = 'You are a financial advisor AI. Respond in Indonesian and provide practical financial advice based on the user\'s transaction data.';

        $payload = [
            'model' => config('services.ai.model', 'gpt-4o-mini'),
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => 'My financial data for this month: ' . json_encode($structuredData, JSON_PRETTY_PRINT)],
                ['role' => 'user', 'content' => $message],
            ],
            'temperature' => 0.2,
            'max_tokens' => 800,
        ];

        try {
            $response = Http::timeout(30)
                ->withToken($apiKey)
                ->post($endpoint, $payload);

            if ($response->failed()) {
                $statusCode = $response->status();
                $body = $response->body();
                
                // Try to parse error as JSON
                try {
                    $errorData = json_decode($body, true);
                    $errorMessage = $errorData['error']['message'] ?? 'API request failed';
                } catch (\Exception $e) {
                    $errorMessage = 'HTTP ' . $statusCode . ': ' . substr($body, 0, 100);
                }

                return [
                    'error' => true,
                    'message' => 'AI API Error: ' . $errorMessage
                ];
            }

            $body = $response->json();
            $content = $body['choices'][0]['message']['content'] ?? null;

            if (!$content) {
                return [
                    'error' => true,
                    'message' => 'No response from AI'
                ];
            }

            // Try to decode JSON result, otherwise return raw
            $decoded = null;
            try {
                $decoded = json_decode($content, true);
            } catch (\Exception $e) {
                // Not JSON, return as plain text
            }

            return [
                'raw' => $content,
                'json' => $decoded,
                'meta' => $body['usage'] ?? null,
            ];

        } catch (\Throwable $e) {
            return [
                'error' => true,
                'message' => 'Connection Error: ' . $e->getMessage()
            ];
        }
    }
}
