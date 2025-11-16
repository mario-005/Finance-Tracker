@extends('layouts.app')

@section('title', 'Tambah Budget - ' . config('app.name'))

@section('content')
<h1>Tambah Budget Baru</h1>

<div class="card" style="max-width: 600px;">
    <form method="POST" action="{{ route('budgets.store') }}">
        @csrf
        
        <div class="form-group">
            <label>Kategori *</label>
            <select name="category_id" required>
                <option value="">Pilih Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('category_id') <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Limit Budget (Rp) *</label>
            <input type="number" name="limit" step="0.01" min="0" required value="{{ old('limit') }}" placeholder="Contoh: 500000" />
            @error('limit') <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span> @enderror
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn">Simpan Budget</button>
            <a href="{{ route('budgets.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@endsection
