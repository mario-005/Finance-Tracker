@extends('layouts.app')

@section('title', 'Transaksi - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">Daftar Transaksi</h1>
            <p class="text-muted mt-1">Kelola dan pantau semua transaksi Anda</p>
        </div>
        <x-button>
            <a href="{{ route('transactions.create') }}" class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>Tambah Transaksi</span>
            </a>
        </x-button>
    </div>

    <!-- Interactive Filters (collapsible) -->
    <div class="mb-6">
        <button @click="openFilters = !openFilters" class="flex items-center gap-2 px-4 py-2 bg-surface text-white rounded-lg hover:bg-gradient-to-r hover:from-surface hover:to-[#1a1a1d] transition">
            <svg :class="{'rotate-180': openFilters}" class="w-4 h-4 transition-transform" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.23 8.29a.75.75 0 01.0-1.08z" clip-rule="evenodd"/></svg>
            <span class="font-medium">🔍 Filter & Pencarian</span>
        </button>

        <div x-show="openFilters" x-cloak class="mt-3 p-4 bg-surface rounded-lg shadow-soft border border-gray-800">
            <form method="GET" action="{{ route('transactions.index') }}" class="grid sm:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-sm text-muted mb-2">Tipe</label>
                    <select name="type" class="w-full bg-[#0b0b0b] text-gray-300 border border-gray-700 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                        <option value="">Semua Tipe</option>
                        <option value="income" {{ ($filters['type'] ?? '') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="expense" {{ ($filters['type'] ?? '') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-muted mb-2">Kategori</label>
                    <select name="category_id" class="w-full bg-[#0b0b0b] text-gray-300 border border-gray-700 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (($filters['category_id'] ?? '') == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-muted mb-2">Dari</label>
                    <input type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}" class="w-full bg-[#0b0b0b] text-gray-300 border border-gray-700 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition" />
                </div>

                <div class="flex items-end gap-2">
                    <div class="w-full">
                        <label class="block text-sm text-muted mb-2">Sampai</label>
                        <input type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}" class="w-full bg-[#0b0b0b] text-gray-300 border border-gray-700 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition" />
                    </div>
                    <div class="flex gap-2">
                        <x-button type="submit">Filter</x-button>
                        <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-surface text-muted rounded-lg hover:bg-gray-700 transition text-sm">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Search bar + quick stats -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <form method="GET" action="{{ route('transactions.index') }}" class="flex items-center gap-4">
                <input type="hidden" name="type" value="{{ $filters['type'] ?? '' }}" />
                <input type="hidden" name="category_id" value="{{ $filters['category_id'] ?? '' }}" />
                <input type="hidden" name="from_date" value="{{ $filters['from_date'] ?? '' }}" />
                <input type="hidden" name="to_date" value="{{ $filters['to_date'] ?? '' }}" />
                <x-input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="🔍 Cari deskripsi atau kategori..." />
                <x-button type="submit" class="px-4">Cari</x-button>
            </form>
            <div class="text-sm text-muted whitespace-nowrap">Menampilkan <span class="font-semibold text-white">{{ $transactions->total() }}</span> transaksi</div>
        </div>
    </div>

    <!-- Mobile: card list (visible on small screens) -->
    <div class="block md:hidden bg-surface rounded-lg border border-gray-800 p-6 mt-6 max-w-5xl mx-auto">
        @if($transactions->isEmpty())
            <div class="p-4 text-center text-muted">
                <div class="text-3xl mb-2">📭</div>
                <p>Belum ada transaksi</p>
                <a href="{{ route('transactions.create') }}" class="inline-block mt-3 text-primary hover:text-primary-dark">Buat transaksi pertama →</a>
            </div>
        @else
            <div class="space-y-3">
                @foreach($transactions as $t)
                <x-card class="bg-[#0b0b0b]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-sm text-muted">{{ $t->date->format('d/m/Y') }}</div>
                            <div class="font-semibold text-white mt-1">{{ $t->category->name ?? 'N/A' }}</div>
                            <div class="text-sm text-muted mt-1">{{ $t->description ?: '-' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm {{ $t->type === 'income' ? 'text-green-400 font-bold' : 'text-primary font-bold' }}">Rp {{ number_format($t->amount,0,',','.') }}</div>
                            <div class="text-xs text-muted mt-1">{{ $t->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</div>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-3">
                        <a href="{{ route('transactions.edit', $t->id) }}" class="px-3 py-1 bg-blue-900 text-blue-200 rounded-lg hover:bg-blue-800 transition text-xs font-medium">Edit</a>
                        <form action="{{ route('transactions.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')">@csrf @method('DELETE')
                            <button type="submit" class="px-3 py-1 bg-red-900 text-red-200 rounded-lg hover:bg-red-800 transition text-xs font-medium">Hapus</button>
                        </form>
                    </div>
                </x-card>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Desktop: Transactions table (visible on md+ screens) -->
    <div class="hidden md:block overflow-x-auto bg-surface rounded-lg border border-gray-800 p-10 mt-6 max-w-5xl mx-auto">
        @if($transactions->isEmpty())
            <div class="p-8 text-center text-muted">
                <div class="text-4xl mb-3">📭</div>
                <p>Belum ada transaksi</p>
                <a href="{{ route('transactions.create') }}" class="inline-block mt-4 text-primary hover:text-primary-dark">Buat transaksi pertama →</a>
            </div>
        @else
        <table class="min-w-full divide-y divide-gray-800 table-fixed w-full">
            <colgroup>
                <col style="width:12%" />
                <col style="width:18%" />
                <col style="width:40%" />
                <col style="width:15%" />
                <col style="width:10%" />
                <col style="width:5%" />
            </colgroup>
            <thead class="bg-[#0b0b0b]">
                <tr>
                    <th class="px-8 py-6 text-left text-xs font-semibold text-muted uppercase">Tanggal</th>
                    <th class="px-8 py-6 text-left text-xs font-semibold text-muted uppercase">Kategori</th>
                    <th class="px-8 py-6 text-left text-xs font-semibold text-muted uppercase">Deskripsi</th>
                    <th class="px-8 py-6 text-right text-xs font-semibold text-muted uppercase">Jumlah</th>
                    <th class="px-8 py-6 text-center text-xs font-semibold text-muted uppercase">Tipe</th>
                    <th class="px-8 py-6 text-right text-xs font-semibold text-muted uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @foreach($transactions as $t)
                <tr class="hover:bg-[#0a0a0c]">
                    <td class="px-8 py-6 align-middle text-sm text-muted">{{ $t->date->format('d/m/Y') }}</td>
                    <td class="px-8 py-6 align-middle text-sm text-white">{{ $t->category->name ?? 'N/A' }}</td>
                    <td class="px-8 py-6 align-middle text-sm text-muted max-w-[40%] wrap-break-word">{{ $t->description ?: '-' }}</td>
                    <td class="px-8 py-6 align-middle text-sm text-right {{ $t->type === 'income' ? 'text-green-400 font-bold' : 'text-primary font-bold' }}">Rp {{ number_format($t->amount,0,',','.') }}</td>
                    <td class="px-8 py-6 align-middle text-center text-sm text-muted">{{ $t->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</td>
                    <td class="px-8 py-6 align-middle text-right text-sm">
                        <div class="inline-flex items-center gap-2">
                            <a href="{{ route('transactions.edit', $t->id) }}" class="px-3 py-1 bg-blue-900 text-blue-200 rounded-lg hover:bg-blue-800 transition text-xs font-medium">Edit</a>
                            <form action="{{ route('transactions.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')" style="display:inline">@csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-900 text-red-200 rounded-lg hover:bg-red-800 transition text-xs font-medium">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Server pagination controls -->
    @if($transactions->total() > 0)
    <div class="mt-8 flex justify-center items-center gap-2 flex-wrap">
        @if($transactions->onFirstPage())
            <span class="px-3 py-2 bg-surface text-muted rounded-lg text-sm">← Sebelumnya</span>
        @else
            <a href="{{ $transactions->previousPageUrl() }}" class="px-3 py-2 bg-surface text-white rounded-lg hover:bg-primary transition text-sm font-medium">← Sebelumnya</a>
        @endif

        @foreach($transactions->getUrlRange(max(1, $transactions->currentPage() - 2), min($transactions->lastPage(), $transactions->currentPage() + 2)) as $page => $url)
            @if($page == $transactions->currentPage())
                <span class="px-3 py-2 bg-primary text-white rounded-lg text-sm font-semibold">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="px-3 py-2 bg-surface text-white rounded-lg hover:bg-gray-700 transition text-sm">{{ $page }}</a>
            @endif
        @endforeach

        @if($transactions->hasMorePages())
            <a href="{{ $transactions->nextPageUrl() }}" class="px-3 py-2 bg-surface text-white rounded-lg hover:bg-primary transition text-sm font-medium">Berikutnya →</a>
        @else
            <span class="px-3 py-2 bg-surface text-muted rounded-lg text-sm">Berikutnya →</span>
        @endif
    </div>
    @endif

</div>

<!-- Removed unused Alpine client-side transactions script because the list is now server-rendered. -->

@endsection
