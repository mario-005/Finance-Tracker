<?php

namespace App\Http\Controllers;

use App\Models\AiSession;
use App\Services\AIService;
use App\Services\FinanceAnalyzer;
use App\Repositories\Eloquent\TransactionRepository;
use Illuminate\Http\Request;

class AIChatController extends Controller
{
    protected $aiService;
    protected $analyzer;
    protected $transactionRepository;

    public function __construct(AIService $aiService, FinanceAnalyzer $analyzer, TransactionRepository $transactionRepository)
    {
        $this->aiService = $aiService;
        $this->analyzer = $analyzer;
        $this->transactionRepository = $transactionRepository;
    }

    public function ask(Request $request)
    {
        $user = $request->user();
        $data = $request->validate(['message' => 'required|string|max:500']);

        $now = now();
        $payload = $this->analyzer->buildMonthlyPayload($user, $now->year, $now->month);

        $resp = $this->aiService->askFinancialAdvisor($user, $data['message'], $payload);

        AiSession::create([
            'user_id' => $user->id,
            'intent' => 'chat',
            'user_payload' => $payload,
            'ai_response' => $resp,
        ]);

        return response()->json($resp);
    }
}
