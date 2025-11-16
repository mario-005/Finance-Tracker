@extends('layouts.app')

@section('title', 'Laporan - ' . config('app.name'))

@section('content')
<h1>Laporan {{ config('app.name') }} Bulanan</h1>

<div class="card">
    <form method="GET" action="{{ route('reports.index') }}" style="display: flex; gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0; flex: 0 0 auto;">
            <label>Tahun</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" max="{{ now()->year }}" />
        </div>
        <div class="form-group" style="margin-bottom: 0; flex: 0 0 auto;">
            <label>Bulan</label>
            <select name="month">
                @php
                    $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                @endphp
                @foreach($monthNames as $m => $name)
                <option value="{{ $m + 1 }}" {{ $month == $m + 1 ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn">Lihat Laporan</button>
    </form>
</div>

<!-- Summary -->
<div class="grid-3">
    <div class="stat-box">
        <h3>Total Pemasukan</h3>
        <div class="amount amount-income">Rp {{ number_format($summary['income'], 0, ',', '.') }}</div>
    </div>
    <div class="stat-box">
        <h3>Total Pengeluaran</h3>
        <div class="amount amount-expense">Rp {{ number_format($summary['expense'], 0, ',', '.') }}</div>
    </div>
    <div class="stat-box">
        <h3>Tabungan</h3>
        <div class="amount" style="color: {{ $summary['saving'] >= 0 ? '#27ae60' : '#e74c3c' }};">Rp {{ number_format($summary['saving'], 0, ',', '.') }}</div>
    </div>
</div>

<!-- Category Breakdown -->
<div class="grid-2">
    <div class="card">
        <h2>Pengeluaran per Kategori</h2>
        <canvas id="categoryChart"></canvas>
    </div>
    <div class="card">
        <h2>Detail Kategori</h2>
        <table style="font-size: 0.9rem;">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($byCategory as $cat => $amount)
                <tr>
                    <td>{{ $cat }}</td>
                    <td style="text-align: right;">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Budget Comparison -->
@if(count($budgetComparison) > 0)
<div class="card">
    <h2>Perbandingan dengan Budget</h2>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th style="text-align: right;">Budget</th>
                <th style="text-align: right;">Terpakai</th>
                <th style="text-align: right;">Sisa</th>
                <th>Progress</th>
            </tr>
        </thead>
        <tbody>
            @foreach($budgetComparison as $cat => $data)
            <tr>
                <td>{{ $cat }}</td>
                <td style="text-align: right;">Rp {{ number_format($data['budget'], 0, ',', '.') }}</td>
                <td style="text-align: right; color: #e74c3c; font-weight: bold;">Rp {{ number_format($data['spent'], 0, ',', '.') }}</td>
                <td style="text-align: right; color: {{ $data['remaining'] >= 0 ? '#27ae60' : '#e74c3c' }};">Rp {{ number_format($data['remaining'], 0, ',', '.') }}</td>
                <td>
                    <div style="background: #ecf0f1; border-radius: 4px; overflow: hidden; height: 20px;">
                        <div style="background: {{ $data['percentage'] > 100 ? '#e74c3c' : '#3498db' }}; width: {{ min($data['percentage'], 100) }}%; height: 100%; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.8rem;">
                            {{ round($data['percentage']) }}%
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('categoryChart');
        if (ctx) {
            const categories = {!! json_encode(array_keys($byCategory)) !!};
            const amounts = {!! json_encode(array_values($byCategory)) !!};

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: categories,
                    datasets: [{
                        data: amounts,
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                            '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
                        ],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
