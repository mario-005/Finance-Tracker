<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Carbon\Carbon;

interface TransactionRepositoryInterface
{
    public function paginateForUser(User $user, array $filters = [], int $perPage = 15);
    public function create(array $data): \App\Models\Transaction;
    public function update(\App\Models\Transaction $transaction, array $data): \App\Models\Transaction;
    public function delete(\App\Models\Transaction $transaction): bool;
    public function sumByCategory(User $user, Carbon $from, Carbon $to): array;
    public function monthlySummary(User $user, int $year, int $month): array;
    public function getAllForUser(User $user): \Illuminate\Database\Eloquent\Collection;
}
