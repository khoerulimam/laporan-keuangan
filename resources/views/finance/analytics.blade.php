@extends('layouts.app')

@section('title', 'Analisa')

@section('styles')
<style>
    .analytics-kpi {
        min-height: 120px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .analytics-kpi:hover {
        transform: translateY(-2px);
    }

    .kpi-label {
        color: var(--text-muted);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .kpi-value {
        font-size: 1.6rem;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .insight-card {
        border-left: 4px solid var(--border-color, var(--primary));
    }

    .insight-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
        margin-top: 5px;
    }

    .metric-bar {
        height: 8px;
        border-radius: 999px;
        background: #edf1f6;
        overflow: hidden;
    }

    .metric-fill {
        height: 100%;
        border-radius: inherit;
    }

    .chart-box {
        height: 300px;
        position: relative;
    }
</style>
@endsection

@section('content')
<div class="header-section">
    <div class="header-title">
        <h1>Analisa Keuangan (Terbaru)</h1>
        <p>Evaluasi pengeluaran, perbandingan pemasukan, dan pencapaian anggaran Anda.</p>
    </div>
    <div class="header-action">
        <form action="{{ route('analytics') }}" method="GET" class="d-flex gap-2">
            <input type="month" name="month" class="form-control" value="{{ $selectedMonth->format('Y-m') }}" onchange="this.form.submit()">
        </form>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card analytics-kpi border-0 shadow-sm">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div class="kpi-label">Pemasukan Bulan Ini</div>
                <div>
                    <div class="kpi-value text-success">Rp {{ number_format($summary['income'], 0, ',', '.') }}</div>
                    <div class="small text-muted mt-1">Total uang masuk</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card analytics-kpi border-0 shadow-sm">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div class="kpi-label">Pengeluaran Bulan Ini</div>
                <div>
                    <div class="kpi-value text-danger">Rp {{ number_format($summary['expense'], 0, ',', '.') }}</div>
                    <div class="small text-muted mt-1">Total uang keluar</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card analytics-kpi border-0 shadow-sm">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div class="kpi-label">Tabungan Bersih (Net)</div>
                <div>
                    <div class="kpi-value {{ $summary['net'] >= 0 ? 'text-primary' : 'text-danger' }}">
                        Rp {{ number_format($summary['net'], 0, ',', '.') }}
                    </div>
                    <div class="small text-muted mt-1">Selisih bulan ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card analytics-kpi border-0 shadow-sm">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div class="kpi-label">Total Saldo Aktif</div>
                <div>
                    <div class="kpi-value text-dark">Rp {{ number_format($summary['balance'], 0, ',', '.') }}</div>
                    <div class="small text-muted mt-1">Saldo di seluruh akun</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Trend and Category Breakdown -->
    <div class="col-lg-8">
        <!-- 6-Month Trend Chart -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-0 bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Tren Keuangan 6 Bulan Terakhir</h5>
                <span class="badge bg-light text-muted border px-3 py-2">Pemasukan vs Pengeluaran</span>
            </div>
            <div class="card-body p-4">
                <div class="chart-box">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 bg-transparent pt-4 px-4">
                <h5 class="mb-0 fw-bold">Analisis Pengeluaran Kategori</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Kategori</th>
                                <th>Kontribusi</th>
                                <th>Transaksi</th>
                                <th class="pe-4 text-end">Total Pengeluaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categoryAnalysis as $category)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 36px; height: 36px; background: {{ $category['color'] }}15; color: {{ $category['color'] }};">
                                                <i class="fas fa-{{ $category['icon'] }}"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $category['name'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="min-width: 140px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="small fw-semibold text-muted" style="min-width: 35px;">{{ $category['percentage'] }}%</span>
                                            <div class="metric-bar flex-grow-1">
                                                <div class="metric-fill" style="width: {{ $category['percentage'] }}%; background: {{ $category['color'] }};"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $category['count'] }} kali</td>
                                    <td class="pe-4 text-end fw-bold text-dark">Rp {{ number_format($category['total'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">Belum ada transaksi pengeluaran bulan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Auto Insights & Budget Status -->
    <div class="col-lg-4">
        <!-- Automatic Insights -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-0 bg-transparent pt-4 px-4">
                <h5 class="mb-0 fw-bold">Rekomendasi Otomatis</h5>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <div class="d-grid gap-3">
                    @foreach($insights as $insight)
                        @php
                            $colorMap = [
                                'success' => ['bg' => '#eafaf1', 'border' => '#2fbf8f', 'text' => '#1b7d5a'],
                                'danger' => ['bg' => '#fdf2f2', 'border' => '#ef6b73', 'text' => '#ad2f36'],
                                'warning' => ['bg' => '#fefaf2', 'border' => '#e6a93f', 'text' => '#8f641b'],
                                'info' => ['bg' => '#f2f5fd', 'border' => '#5b6ee1', 'text' => '#3242a8']
                            ];
                            $colors = $colorMap[$insight['level']] ?? $colorMap['info'];
                        @endphp
                        <div class="p-3 rounded-4 insight-card d-flex gap-3" 
                             style="background-color: {{ $colors['bg'] }}; --border-color: {{ $colors['border'] }};">
                            <span class="insight-dot" style="background-color: {{ $colors['border'] }};"></span>
                            <div>
                                <div class="fw-bold small" style="color: {{ $colors['text'] }};">{{ $insight['title'] }}</div>
                                <div class="small mt-1 text-secondary" style="font-size: 0.82rem; line-height: 1.4;">{{ $insight['body'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Budget Status -->
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 bg-transparent pt-4 px-4">
                <h5 class="mb-0 fw-bold">Status Anggaran</h5>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                @php
                    $activeBudgets = $budgets->filter(fn($b) => $b['limit'] > 0);
                @endphp
                @forelse($activeBudgets as $budget)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="fw-semibold small text-dark">{{ $budget['category']->name }}</div>
                            <span class="badge {{ $budget['remaining'] < 0 ? 'bg-danger text-white' : 'bg-light text-dark border' }} px-2 py-1" style="font-size: 0.72rem;">
                                {{ $budget['progress'] }}%
                            </span>
                        </div>
                        <div class="metric-bar mb-1">
                            <div class="metric-fill" style="width: {{ min(100, $budget['progress']) }}%; background: {{ $budget['category']->color }};"></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted" style="font-size: 0.76rem;">
                            <span>Terpakai: Rp {{ number_format($budget['spent'], 0, ',', '.') }}</span>
                            <span>Limit: Rp {{ number_format($budget['limit'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-wallet fa-2x opacity-25 mb-3"></i>
                        <div style="font-size: 0.85rem;">Belum ada anggaran yang diset untuk bulan ini.</div>
                        <a href="{{ route('budgets.index') }}" class="btn btn-sm btn-light border mt-3 px-3">Atur Anggaran</a>
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

    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'bar',
        data: {
            labels: trendData.map(item => item.label),
            datasets: [
                {
                    label: 'Pemasukan',
                    data: trendData.map(item => item.income),
                    backgroundColor: 'rgba(47, 191, 143, 0.85)',
                    borderColor: 'rgba(47, 191, 143, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                },
                {
                    label: 'Pengeluaran',
                    data: trendData.map(item => item.expense),
                    backgroundColor: 'rgba(239, 107, 115, 0.85)',
                    borderColor: 'rgba(239, 107, 115, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: { family: 'Inter', size: 12 }
                    }
                }
            }
        }
    });
</script>
@endsection
