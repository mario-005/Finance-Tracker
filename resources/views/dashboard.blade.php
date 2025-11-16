@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))

@section('content')
<div x-data="{ view: 'chart' }" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white">Dashboard {{ config('app.name') }}</h1>
            <p class="text-muted mt-1">Ringkasan finansial bulan ini</p>
        </div>
        <div class="flex items-center gap-2">
            <x-button>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <a href="{{ route('transactions.create') }}">Tambah Transaksi</a>
            </x-button>
            <a href="{{ route('transactions.index') }}" class="px-4 py-2 rounded-lg border border-primary text-primary hover:bg-primary hover:text-white transition">Lihat Semua</a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-card class="flex items-center gap-4 bg-gradient-to-br from-surface to-[#0a0a0c]">
            <div class="p-3 rounded-xl bg-gradient-to-br from-primary to-primary-dark">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zM12 14v6"/></svg>
            </div>
            <div>
                <div class="text-sm text-muted">Pemasukan</div>
                <div class="text-2xl font-bold text-white">Rp {{ number_format($summary['income'], 0, ',', '.') }}</div>
            </div>
        </x-card>

        <x-card class="flex items-center gap-4 bg-gradient-to-br from-surface to-[#0a0a0c]">
            <div class="p-3 rounded-xl bg-gradient-to-br from-red-600 to-red-700">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2h-4"/></svg>
            </div>
            <div>
                <div class="text-sm text-muted">Pengeluaran</div>
                <div class="text-2xl font-bold text-white">Rp {{ number_format($summary['expense'], 0, ',', '.') }}</div>
            </div>
        </x-card>

        <x-card class="flex items-center gap-4 bg-gradient-to-br from-surface to-[#0a0a0c]">
            <div class="p-3 rounded-xl bg-gradient-to-br from-gray-600 to-gray-700">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h4l3 8 4-16 3 8h4"/></svg>
            </div>
            <div>
                <div class="text-sm text-muted">Saldo Tersisa</div>
                <div class="text-2xl font-bold text-white">Rp {{ number_format($summary['saving'], 0, ',', '.') }}</div>
            </div>
        </x-card>
    </div>

    <!-- Budget Alerts -->
    @if(count($budgetAlerts) > 0)
        <div class="space-y-3">
            @foreach($budgetAlerts as $alert)
                <x-card class="border-l-4 border-primary bg-gradient-to-r from-red-950 to-surface">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div>
                            <div class="font-semibold text-white">⚠️ {{ $alert['category'] }}</div>
                            <div class="text-sm text-muted mt-1">Budget: Rp {{ number_format($alert['budget'], 0, ',', '.') }} — Terpakai: Rp {{ number_format($alert['spent'], 0, ',', '.') }}</div>
                        </div>
                        <div class="text-primary font-bold whitespace-nowrap">+Rp {{ number_format($alert['overage'], 0, ',', '.') }}</div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif

    <!-- Chart / Category Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-card class="bg-gradient-to-br from-surface to-[#0a0a0c]">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
                <h2 class="text-lg font-semibold text-white">Pengeluaran per Kategori</h2>
                <div class="flex items-center gap-2">
                    <button @click="view='chart'" :class="{'bg-primary text-white': view==='chart', 'bg-surface text-muted': view!=='chart'}" class="px-3 py-1 rounded-lg transition text-sm font-medium">Chart</button>
                    <button @click="view='table'" :class="{'bg-primary text-white': view==='table', 'bg-surface text-muted': view!=='table'}" class="px-3 py-1 rounded-lg transition text-sm font-medium">Table</button>
                </div>
            </div>

            <div x-show="view==='chart'" x-cloak>
                <canvas id="categoryChart"></canvas>
            </div>

            <div x-show="view==='table'" x-cloak class="overflow-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-muted border-b border-gray-800">
                            <th class="py-2">Kategori</th>
                            <th class="text-right py-2">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byCategory as $category => $amount)
                            <tr class="border-b border-gray-900 hover:bg-[#1a1a1d] transition">
                                <td class="py-3 text-white">{{ $category }}</td>
                                <td class="py-3 text-right text-white font-medium">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card class="bg-gradient-to-br from-surface to-[#0a0a0c]">
            <h2 class="text-lg font-semibold text-white mb-4">Ringkasan Kategori</h2>
            <div class="space-y-3">
                @foreach($byCategory as $category => $amount)
                    <div class="flex items-center justify-between p-2 rounded-lg hover:bg-[#1a1a1d] transition">
                        <div class="text-sm text-muted">{{ $category }}</div>
                        <div class="text-sm font-semibold text-primary">Rp {{ number_format($amount, 0, ',', '.') }}</div>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>

    <!-- Recent Transactions -->
    <x-card class="bg-gradient-to-br from-surface to-[#0a0a0c]">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-white">Transaksi Terbaru</h2>
            <a href="{{ route('transactions.index') }}" class="text-sm text-primary hover:text-primary-dark transition font-medium">Lihat semua →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-muted border-b border-gray-800">
                        <th class="py-2">Tanggal</th>
                        <th class="py-2">Kategori</th>
                        <th class="py-2">Deskripsi</th>
                        <th class="text-right py-2">Jumlah</th>
                        <th class="text-center py-2">Tipe</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions as $tx)
                        <tr class="border-b border-gray-900 hover:bg-[#1a1a1d] transition">
                            <td class="py-3 text-white">{{ $tx->date->format('d M Y') }}</td>
                            <td class="py-3 text-white">{{ $tx->category->name ?? '-' }}</td>
                            <td class="py-3 text-muted">{{ Str::limit($tx->description, 40) ?? '-' }}</td>
                            <td class="py-3 text-right font-bold {{ $tx->type === 'income' ? 'text-green-400' : 'text-primary' }}">{{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                            <td class="py-3 text-center"><span class="px-2 py-0.5 rounded-lg text-xs font-medium {{ $tx->type === 'income' ? 'bg-green-900 text-green-200' : 'bg-red-900 text-red-200' }}">{{ ucfirst($tx->type) }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-8">📊 Belum ada transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('categoryChart');
        if (ctx) {
            const categories = {!! json_encode(array_keys($byCategory)) !!};
            const amounts = {!! json_encode(array_values($byCategory)) !!};

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: categories,
                    datasets: [{
                                data: amounts,
                                backgroundColor: [
                                    '#E53935', '#C62828', '#B71C1C', '#8B0000', '#6B0000',
                                    '#DC3545', '#F87171', '#EF4444', '#FCA5A5', '#FECACA'
                                ],
                                borderColor: '#0f1113',
                                borderWidth: 2
                            }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { 
                            position: 'bottom',
                            labels: { color: '#9CA3AF', padding: 15 }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff'
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
