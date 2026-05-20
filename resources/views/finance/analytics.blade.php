@extends('layouts.app')

@section('title', 'Analisa')

@section('styles')
<style>
    .analytics-kpi {
        min-height: 148px;
    }

    .kpi-label {
        color: var(--text-muted);
        font-size: 0.74rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .kpi-value {
        font-size: 1.45rem;
        font-weight: 800;
        letter-spacing: 0;
    }

    .soft-panel {
        background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%);
    }

    .insight-item {
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1rem;
        background: #fff;
    }

    .insight-dot {
        width: 12px;
        height: 12px;
        border-radius: 999px;
        flex: 0 0 12px;
        margin-top: 0.28rem;
    }

    .risk-pill {
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.34rem 0.7rem;
    }

    .metric-bar {
        height: 9px;
        border-radius: 999px;
        background: #edf1f6;
        overflow: hidden;
    }

    .metric-fill {
        height: 100%;
        border-radius: inherit;
    }

    .chart-box {
        height: 320px;
    }

    .mini-chart-box {
        height: 240px;
    }
</style>
@endsection

@section('content')
<div class="header-section">
    <div class="header-title">
        <h1>Analisa Keuangan</h1>
        <p>Forecast, risiko budget, pola pengeluaran, dan sinyal keputusan finansial.</p>
    </div>
    <div class="header-action">
        <form action="{{ route('analytics') }}" method="GET" class="d-flex gap-2">
            <input type="month" name="month" class="form-control" value="{{ $selectedMonth->format('Y-m') }}" onchange="this.form.submit()">
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card analytics-kpi">
            <div class="card-body p-4">
                <div class="kpi-label mb-2">Saldo Bersih</div>
                <div class="kpi-value text-primary">Rp {{ number_format($summary['balance'], 0, ',', '.') }}</div>
                <div class="small text-muted mt-2">
                    Runway: {{ $summary['runwayMonths'] !== null ? $summary['runwayMonths'].' bulan' : 'belum bisa dihitung' }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card analytics-kpi">
            <div class="card-body p-4">
                <div class="kpi-label mb-2">Net Bulan Ini</div>
                <div class="kpi-value {{ $summary['net'] >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($summary['net'], 0, ',', '.') }}
                </div>
                <div class="small text-muted mt-2">
                    Rata-rata 12 bulan: Rp {{ number_format($summary['avgNet'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card analytics-kpi">
            <div class="card-body p-4">
                <div class="kpi-label mb-2">Forecast Pengeluaran</div>
                <div class="kpi-value text-danger">Rp {{ number_format($summary['projectedExpense'], 0, ',', '.') }}</div>
                <div class="small text-muted mt-2">
                    Aktual: Rp {{ number_format($summary['expense'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card analytics-kpi">
            <div class="card-body p-4">
                <div class="kpi-label mb-2">Stabilitas Belanja</div>
                <div class="kpi-value {{ $summary['expenseVolatility'] >= 35 ? 'text-warning' : 'text-success' }}">
                    {{ $summary['expenseVolatility'] }}%
                </div>
                <div class="small text-muted mt-2">
                    Semakin kecil, arus kas makin mudah diprediksi.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Tren 12 Bulan</h5>
                <span class="badge bg-light text-primary border">Income vs Expense vs Net</span>
            </div>
            <div class="card-body p-4">
                <div class="chart-box">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card soft-panel">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">Insight Otomatis</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-grid gap-3">
                    @foreach($insights as $insight)
                        @php
                            $dot = [
                                'success' => '#2fbf8f',
                                'danger' => '#ef6b73',
                                'warning' => '#e6a93f',
                                'info' => '#5b6ee1',
                            ][$insight['level']] ?? '#5b6ee1';
                        @endphp
                        <div class="insight-item d-flex gap-3">
                            <span class="insight-dot" style="background: {{ $dot }};"></span>
                            <div>
                                <div class="fw-bold mb-1">{{ $insight['title'] }}</div>
                                <div class="small text-muted">{{ $insight['body'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">Forecast Bulan Ini</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Pemasukan</span>
                    <strong>Rp {{ number_format($summary['income'], 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Proyeksi pengeluaran</span>
                    <strong class="text-danger">Rp {{ number_format($summary['projectedExpense'], 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Proyeksi net</span>
                    <strong class="{{ $summary['projectedNet'] >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($summary['projectedNet'], 0, ',', '.') }}
                    </strong>
                </div>
                <div class="d-flex justify-content-between mb-0">
                    <span class="text-muted">Proyeksi tabungan</span>
                    <strong class="{{ $summary['projectedSavingsRate'] >= 20 ? 'text-success' : 'text-warning' }}">
                        {{ $summary['projectedSavingsRate'] }}%
                    </strong>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">Pola Hari Belanja</h5>
            </div>
            <div class="card-body p-4">
                <div class="mini-chart-box">
                    <canvas id="weekdayChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">Budget Berisiko</h5>
            </div>
            <div class="card-body p-4">
                @forelse($budgetAtRisk as $budget)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-semibold">{{ $budget['category']->name }}</div>
                            <span class="risk-pill {{ $budget['remaining'] < 0 ? 'bg-danger text-white' : 'bg-light text-warning border' }}">
                                {{ $budget['progress'] }}%
                            </span>
                        </div>
                        <div class="metric-bar">
                            <div class="metric-fill" style="width: {{ min(100, $budget['progress']) }}%; background: {{ $budget['category']->color }};"></div>
                        </div>
                        <div class="small text-muted mt-2">
                            Sisa: Rp {{ number_format($budget['remaining'], 0, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-shield-alt fa-2x opacity-25 mb-3"></i>
                        <div>Tidak ada budget berisiko bulan ini.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">Analisa Kategori</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Kategori</th>
                                <th>Kontribusi</th>
                                <th>Rata-rata</th>
                                <th>Budget</th>
                                <th class="pe-4 text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categoryAnalysis as $category)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: {{ $category['color'] }}20; color: {{ $category['color'] }};">
                                                <i class="fas fa-{{ $category['icon'] }}"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $category['name'] }}</div>
                                                <div class="small text-muted">{{ $category['count'] }} transaksi</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="min-width: 150px;">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span>{{ $category['percentage'] }}%</span>
                                        </div>
                                        <div class="metric-bar">
                                            <div class="metric-fill" style="width: {{ min(100, $category['percentage']) }}%; background: {{ $category['color'] }};"></div>
                                        </div>
                                    </td>
                                    <td>Rp {{ number_format($category['average'], 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-light text-muted border">{{ $category['risk'] }}</span>
                                    </td>
                                    <td class="pe-4 text-end fw-bold">Rp {{ number_format($category['total'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Belum ada pengeluaran bulan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">Merchant Terbesar</h5>
            </div>
            <div class="card-body p-4">
                @forelse($merchantAnalysis as $merchant)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="fw-semibold">{{ $merchant['name'] }}</div>
                            <div class="small text-muted">{{ $merchant['count'] }} transaksi, rata-rata Rp {{ number_format($merchant['average'], 0, ',', '.') }}</div>
                        </div>
                        <div class="fw-bold text-danger">Rp {{ number_format($merchant['total'], 0, ',', '.') }}</div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-store fa-2x opacity-25 mb-3"></i>
                        <div>Isi field merchant pada transaksi agar analisa vendor aktif.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const trendData = @json($series);
    const weekdayData = @json($weekdayAnalysis);

    new Chart(document.getElementById('trendChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: trendData.map(item => item.label),
            datasets: [
                {
                    type: 'line',
                    label: 'Net',
                    data: trendData.map(item => item.net),
                    borderColor: '#5b6ee1',
                    backgroundColor: 'rgba(91, 110, 225, .12)',
                    tension: .35,
                    fill: true,
                    pointRadius: 3
                },
                {
                    label: 'Pemasukan',
                    data: trendData.map(item => item.income),
                    backgroundColor: 'rgba(47, 191, 143, .78)',
                    borderRadius: 8
                },
                {
                    label: 'Pengeluaran',
                    data: trendData.map(item => item.expense),
                    backgroundColor: 'rgba(239, 107, 115, .78)',
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { color: '#eef1f6' } }
            },
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 18 } }
            }
        }
    });

    new Chart(document.getElementById('weekdayChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: weekdayData.map(item => item.label),
            datasets: [{
                label: 'Pengeluaran',
                data: weekdayData.map(item => item.total),
                backgroundColor: '#5b6ee1',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { color: '#eef1f6' } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endsection
