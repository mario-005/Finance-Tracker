@extends('layouts.app')

@section('title', 'Tambah Transaksi - ' . config('app.name'))

@section('content')
<div class="max-w-2xl mx-auto" x-data="transactionForm()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Tambah Transaksi Baru</h1>
        <p class="text-muted">Catat pengeluaran atau pemasukan Anda</p>
        <!-- <p class="text-sm text-gray-400">Debug: income={{ $incomeCategories->count() }} expense={{ $expenseCategories->count() }}</p> -->
    </div>

    <!-- Form Card -->
    <x-card class="bg-linear-to-br from-surface to-[#0a0a0c]">
        <form method="POST" action="{{ route('transactions.store') }}" class="space-y-6">
            @csrf
            
            <!-- Tipe Transaksi -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-white mb-2">Tipe Transaksi *</label>
                    <select name="type" id="transactionType" required @change="filterCategories()" x-model="type" class="w-full bg-[#0b0b0b] text-gray-300 border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                        <option value="">Pilih Tipe</option>
                        <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>💰 Pemasukan</option>
                        <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>💸 Pengeluaran</option>
                    </select>
                    @error('type') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Jumlah -->
                <div>
                    <label class="block text-sm font-semibold text-white mb-2">Jumlah (Rp) *</label>
                    <input type="number" name="amount" step="1" min="0" required value="{{ old('amount') }}" placeholder="Contoh: 50000" class="w-full bg-[#0b0b0b] text-gray-300 placeholder:text-gray-600 border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition" />
                    @error('amount') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-sm font-semibold text-white mb-2">Kategori (Opsional)</label>
                <input type="text" name="category_name" id="categoryInput" list="categoriesDatalist" placeholder="Ketik atau pilih kategori" value="{{ old('category_name') }}" class="w-full bg-[#0b0b0b] text-gray-300 border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition" />
                <input type="hidden" name="category_id" id="categoryIdInput" value="{{ old('category_id') }}" />

                <!-- Datalists for suggestions split by type; we'll switch the input's list attribute via JS -->
                <datalist id="incomeCategoriesList">
                    @foreach($incomeCategories as $cat)
                    <option data-id="{{ $cat->id }}" value="{{ $cat->name }}"></option>
                    @endforeach
                </datalist>

                <datalist id="expenseCategoriesList">
                    @foreach($expenseCategories as $cat)
                    <option data-id="{{ $cat->id }}" value="{{ $cat->name }}"></option>
                    @endforeach
                </datalist>

                @error('category_name') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Tanggal -->
            <div>
                <label class="block text-sm font-semibold text-white mb-2">Tanggal *</label>
                <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}" class="w-full bg-[#0b0b0b] text-gray-300 border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition" />
                @error('date') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-sm font-semibold text-white mb-2">Deskripsi</label>
                <textarea name="description" rows="4" placeholder="Contoh: Belanja di supermarket" class="w-full bg-[#0b0b0b] text-gray-300 placeholder:text-gray-600 border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition resize-none">{{ old('description') }}</textarea>
                @error('description') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-6 border-t border-gray-800">
                <x-button type="submit" class="flex-1 justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span>Simpan Transaksi</span>
                </x-button>
                <a href="{{ route('transactions.index') }}" class="flex-1 px-4 py-2 rounded-lg border border-primary text-primary hover:bg-primary hover:text-white transition text-center font-medium flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"></path>
                    </svg>
                    <span>Batal</span>
                </a>
            </div>
        </form>
    </x-card>

    <!-- Info Box -->
    <div class="mt-6 p-4 bg-linear-to-r from-blue-900 to-surface rounded-lg border border-blue-800">
        <div class="flex gap-3">
            <div class="text-blue-400 text-lg">ℹ️</div>
            <div>
                <p class="text-white font-medium">Tips Mencatat Transaksi</p>
                <p class="text-muted text-sm mt-1">Gunakan kategori yang konsisten agar laporan keuangan Anda lebih akurat. Deskripsi membantu Anda mengingat detail pengeluaran.</p>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
function transactionForm() {
    return {
        type: '{{ old('type') }}',
        selectedCategory: '{{ old('category_id') }}',
        filterCategories() {
            const typeValue = document.getElementById('transactionType').value;
            const categorySelect = document.getElementById('categorySelect');
            const options = categorySelect.querySelectorAll('option[data-type]');
            
            options.forEach(option => {
                if (typeValue === option.dataset.type) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
        }
    }
}

// Initial filter on page load
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('transactionType');
    const categorySelect = document.getElementById('categorySelect');
    
    if (typeSelect && typeSelect.value) {
        const options = categorySelect.querySelectorAll('option[data-type]');
        options.forEach(option => {
            if (typeSelect.value === option.dataset.type) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
    }
});
</script>
