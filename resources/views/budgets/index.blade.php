@extends('layouts.app')

@section('title', 'Budget - ' . config('app.name'))

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1>Atur Budget Bulanan</h1>
    <a href="{{ route('budgets.create') }}" class="btn">+ Tambah Budget</a>
</div>

<div class="card">
    @if(count($budgets) > 0)
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th style="text-align: right;">Limit Budget</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($budgets as $b)
            <tr>
                <td>{{ $b->category->name }}</td>
                <td style="text-align: right;">Rp {{ number_format($b->limit, 0, ',', '.') }}</td>
                <td>
                    <form method="POST" action="{{ route('budgets.destroy', $b->id) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus?')" style="padding: 0.25rem 0.5rem; font-size: 0.85rem;">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align: center; color: #999; padding: 2rem;">Belum ada budget. <a href="{{ route('budgets.create') }}">Buat budget sekarang</a></p>
    @endif
</div>

@endsection
