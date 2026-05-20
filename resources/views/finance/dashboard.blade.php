@extends('layouts.app')

@section('title', 'CFO Revenue Dashboard')

@section('styles')
<style>
    :root {
        --qb-green: #108000;
        --qb-green-2: #2ca01c;
        --qb-mint: #e9f7ec;
        --qb-ink: #12312a;
        --qb-blue: #2d6cdf;
    }

    .cfo-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(320px, 0.55fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .revenue-hero,
    .close-panel {
        border: 1px solid var(--border);
        border-radius: 14px;
        background: #ffffff;
        box-shadow: var(--shadow-subtle);
        overflow: hidden;
    }

    .revenue-hero {
        position: relative;
        padding: 1.5rem;
        background: #ffffff;
    }

    .hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.42rem 0.72rem;
        border-radius: 999px;
        color: var(--qb-green);
        background: var(--primary-soft);
        font-size: 0.78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .hero-title {
        position: relative;
        margin: 1rem 0 0.35rem;
        max-width: 720px;
        color: var(--ink);
        font-size: clamp(1.65rem, 2.8vw, 2.35rem);
        line-height: 1.12;
        font-weight: 800;
    }

    .hero-copy {
        max-width: 690px;
        color: var(--text-muted);
        margin: 0;
    }

    .hero-metrics {
        position: relative;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.85rem;
        margin-top: 1.4rem;
    }

    .metric-tile {
        padding: 1rem;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #f9fafb;
    }

    .metric-label {
        color: var(--text-muted);
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .metric-value {
        margin-top: 0.42rem;
        color: var(--ink);
        font-size: clamp(1.05rem, 1.7vw, 1.55rem);
        font-weight: 800;
    }

    .metric-note {
        margin-top: 0.25rem;
        color: var(--text-muted);
        font-size: 0.82rem;
    }

    .close-panel {
        padding: 1.25rem;
    }

    .close-score {
        width: 92px;
        height: 92px;
        border-radius: 999px;
        display: grid;
        place-items: center;
        margin: 0.8rem auto 1rem;
        color: var(--qb-green);
        background:
            radial-gradient(circle at center, #ffffff 54%, transparent 55%),
            conic-gradient(var(--qb-green-2) {{ max(0, min(100, $summary['budgetCompliance'])) }}%, #e7edf4 0);
        font-size: 1.5rem;
        font-weight: 800;
    }

    .watch-item {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.85rem 0;
        border-top: 1px solid rgba(135, 146, 170, 0.14);
    }

    .section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .section-title h5 {
        margin: 0;
        color: var(--ink);
        font-weight: 800;
    }

    .qb-card {
        height: 100%;
    }

    .chart-wrap {
        height: 330px;
    }

    .category-row {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 0.9rem;
        align-items: center;
        padding: 0.72rem 0;
        border-bottom: 1px solid rgba(135, 146, 170, 0.12);
    }

    .category-row:last-child {
        border-bottom: 0;
    }

    .category-bar {
        height: 9px;
        border-radius: 999px;
        background: rgba(135, 146, 170, 0.14);
        overflow: hidden;
    }

    .category-fill {
        height: 100%;
        border-radius: inherit;
    }

    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.38rem 0.65rem;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 800;
    }

    .status-chip.good {
        color: var(--qb-green);
        background: rgba(16, 128, 0, 0.09);
    }

    .status-chip.warn {
        color: #b7791f;
        background: rgba(217, 154, 32, 0.12);
    }

    .status-chip.bad {
        color: #ca3d4a;
        background: rgba(240, 93, 106, 0.12);
    }

    .insight-card {
        color: var(--text-main);
        background: #ffffff !important;
    }

    .table td,
    .table th {
        white-space: nowrap;
    }

    @media (max-width: 1199px) {
        .cfo-hero {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .hero-metrics {
            grid-template-columns: 1fr;
        }

        .revenue-hero {
            padding: 1.15rem;
        }

        .chart-wrap {
            height: 280px;
        }
    }
</style>
@endsection

@section('content')
@php
    $netIncome = $summary['income'] - $summary['expense'];
    $grossMargin = $summary['income'] > 0 ? round(($netIncome / $summary['income']) * 100, 1) : 0;
    $cashRunway = $summary['expense'] > 0 ? round($summary['balance'] / max(1, $summary['expense']), 1) : null;
    $expenseRatio = $summary['income'] > 0 ? round(($summary['expense'] / $summary['income']) * 100, 1) : 0;
@endphp

<div class="header-section">
    <div class="header-title">
        <h1>CFO Revenue Dashboard</h1>
        <p>Template analisa pendapatan ala QuickBooks untuk revenue, margin, cash runway, dan kontrol biaya.</p>
    </div>
    <div class="header-action">
        <form action="{{ route('dashboard') }}" method="GET" class="d-flex gap-2">
            <input type="month" name="month" class="form-control" value="{{ $selectedMonth->format('Y-m') }}" onchange="this.form.submit()">
        </form>
    </div>
</div>

<div class="cfo-hero">
    <section class="revenue-hero">
        <span class="hero-eyebrow">
            <i class="fas fa-chart-line"></i>
            QuickBooks-ready revenue view
        </span>
        <h2 class="hero-title">Pendapatan bulan ini Rp {{ number_format($summary['income'], 0, ',', '.') }}</h2>
        <p class="hero-copy">
            Ringkasan CFO untuk membaca performa pendapatan, arus kas operasional, dan risiko biaya pada periode {{ $selectedMonth->translatedFormat('F Y') }}.
        </p>

        <div class="hero-metrics">
            <div class="metric-tile">
                <div class="metric-label">Net Operating Income</div>
                <div class="metric-value {{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($netIncome, 0, ',', '.') }}
                </div>
                <div class="metric-note">Pendapatan dikurangi pengeluaran</div>
            </div>
            <div class="metric-tile">
                <div class="metric-label">Gross Margin</div>
                <div class="metric-value {{ $grossMargin >= 20 ? 'text-success' : 'text-warning' }}">{{ $grossMargin }}%</div>
                <div class="metric-note">Target CFO sehat: 20%+</div>
            </div>
            <div class="metric-tile">
                <div class="metric-label">Expense Ratio</div>
                <div class="metric-value {{ $expenseRatio <= 80 ? 'text-success' : 'text-danger' }}">{{ $expenseRatio }}%</div>
                <div class="metric-note">Biaya terhadap revenue</div>
            </div>
            <div class="metric-tile">
                <div class="metric-label">Cash Runway</div>
                <div class="metric-value text-primary">{{ $cashRunway !== null ? $cashRunway.'x' : 'N/A' }}</div>
                <div class="metric-note">Saldo dibanding biaya bulanan</div>
            </div>
        </div>
    </section>

    <aside class="close-panel">
        <div class="section-title mb-0">
            <h5>Finance Close Health</h5>
            <span class="status-chip {{ $summary['budgetCompliance'] >= 80 ? 'good' : ($summary['budgetCompliance'] >= 50 ? 'warn' : 'bad') }}">
                <i class="fas fa-circle"></i>
                {{ $summary['budgetCompliance'] >= 80 ? 'On Track' : ($summary['budgetCompliance'] >= 50 ? 'Watch' : 'Risk') }}
            </span>
        </div>
        <div class="close-score">{{ $summary['budgetCompliance'] }}%</div>
        <div class="watch-item">
            <span class="text-muted">Budget tersisa</span>
            <strong>{{ $summary['budgetCompliance'] }}%</strong>
        </div>
        <div class="watch-item">
            <span class="text-muted">Tren biaya</span>
            <strong class="{{ $summary['expenseTrend'] > 0 ? 'text-danger' : 'text-success' }}">
                {{ $summary['expenseTrend'] > 0 ? '+' : '' }}{{ $summary['expenseTrend'] }}%
            </strong>
        </div>
        <div class="watch-item">
            <span class="text-muted">Saldo tersedia</span>
            <strong>Rp {{ number_format($summary['balance'], 0, ',', '.') }}</strong>
        </div>
    </aside>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card qb-card">
            <div class="card-body p-4">
                <div class="section-title">
                    <h5>Revenue & Cash Flow Trend</h5>
                    <span class="badge bg-light text-primary border">6 bulan terakhir</span>
                </div>
                <div class="chart-wrap">
                    <canvas id="cashflowChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card qb-card">
            <div class="card-body p-4">
                <div class="section-title">
                    <h5>Expense Mix</h5>
                    <span class="badge bg-light text-muted border">Top kategori</span>
                </div>
                <div style="height: 220px; position: relative;">
                    <canvas id="categoryChart"></canvas>
                </div>

                <div class="mt-4">
                    @forelse($categoryExpenses->take(5) as $cat)
                        <div class="category-row">
                            <div>
                                <div class="d-flex align-items-center justify-content-between gap-3 mb-2">
                                    <span class="fw-semibold">{{ $cat['name'] }}</span>
                                    <span class="small text-muted">{{ $cat['percentage'] }}%</span>
                                </div>
                                <div class="category-bar">
                                    <div class="category-fill" style="width: {{ min(100, $cat['percentage']) }}%; background: {{ $cat['color'] }};"></div>
                                </div>
                            </div>
                            <strong>Rp {{ number_format($cat['total'], 0, ',', '.') }}</strong>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">Belum ada data biaya untuk periode ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card qb-card">
            <div class="card-body p-4">
                <div class="section-title">
                    <h5>Revenue Quality</h5>
                </div>
                <div class="watch-item border-top-0 pt-0">
                    <span class="text-muted">Monthly Revenue</span>
                    <strong class="text-success">Rp {{ number_format($summary['income'], 0, ',', '.') }}</strong>
                </div>
                <div class="watch-item">
                    <span class="text-muted">Operating Cost</span>
                    <strong class="text-danger">Rp {{ number_format($summary['expense'], 0, ',', '.') }}</strong>
                </div>
                <div class="watch-item">
                    <span class="text-muted">Savings / Profit Rate</span>
                    <strong class="{{ $summary['savingsRate'] >= 20 ? 'text-success' : 'text-warning' }}">{{ $summary['savingsRate'] }}%</strong>
                </div>
                <div class="watch-item">
                    <span class="text-muted">Cost Movement</span>
                    <strong class="{{ $summary['expenseTrend'] > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $summary['expenseTrend'] > 0 ? '+' : '' }}{{ $summary['expenseTrend'] }}%
                    </strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card qb-card insight-card">
            <div class="card-body p-4">
                <div class="section-title">
                    <h5>CFO Notes</h5>
                    <i class="fas fa-lightbulb"></i>
                </div>
                @foreach($insights as $insight)
                    <div class="d-flex gap-3 mb-3">
                        <div class="opacity-75"><i class="fas fa-check-circle mt-1"></i></div>
                        <p class="small mb-0 opacity-90">{{ $insight }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card qb-card">
            <div class="card-body p-4">
                <div class="section-title">
                    <h5>Quick Actions</h5>
                </div>
                <div class="d-grid gap-2">
                    <a href="{{ route('transactions.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i> Tambah transaksi
                    </a>
                    <a href="{{ route('analytics') }}" class="btn btn-light">
                        <i class="fas fa-chart-line me-2"></i> Buka analisa detail
                    </a>
                    <a href="{{ route('budgets.index') }}" class="btn btn-light">
                        <i class="fas fa-chart-pie me-2"></i> Review budget
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="d-flex justify-content-between align-items-center p-4 pb-3">
            <div>
                <h5 class="fw-bold mb-1">Recent Financial Activity</h5>
                <div class="small text-muted">Transaksi terbaru untuk proses rekonsiliasi dan review CFO.</div>
            </div>
            <a href="{{ route('transactions.index') }}" class="btn btn-light btn-sm text-primary">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Akun</th>
                        <th class="text-end pe-4">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions as $transaction)
                        <tr>
                            <td class="ps-4 small text-muted">{{ $transaction->transaction_date->format('d M Y') }}</td>
                            <td>
                                <div class="fw-semibold">{{ $transaction->description }}</div>
                                <div class="small text-muted">{{ $transaction->merchant ?? 'No merchant' }}</div>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2" style="background-color: {{ $transaction->category->color }}20; color: {{ $transaction->category->color }};">
                                    {{ $transaction->category->name }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ $transaction->account->name }}</td>
                            <td class="text-end pe-4 fw-bold {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const cashflowCtx = document.getElementById('cashflowChart').getContext('2d');
    new Chart(cashflowCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($cashflow->pluck('label')) !!},
            datasets: [
                {
                    type: 'line',
                    label: 'Net Cash',
                    data: {!! json_encode($cashflow->map(fn ($item) => $item['income'] - $item['expense'])->values()) !!},
                    borderColor: '#108000',
                    backgroundColor: 'rgba(16, 128, 0, .12)',
                    tension: .35,
                    fill: true,
                    pointRadius: 3
                },
                {
                    label: 'Revenue',
                    data: {!! json_encode($cashflow->pluck('income')) !!},
                    backgroundColor: 'rgba(44, 160, 28, .82)',
                    borderRadius: 10
                },
                {
                    label: 'Operating Cost',
                    data: {!! json_encode($cashflow->pluck('expense')) !!},
                    backgroundColor: 'rgba(240, 93, 106, .78)',
                    borderRadius: 10
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(135, 146, 170, .14)' } },
                x: { grid: { display: false } }
            },
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            }
        }
    });

    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categoryExpenses->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($categoryExpenses->pluck('total')) !!},
                backgroundColor: {!! json_encode($categoryExpenses->pluck('color')) !!},
                borderColor: '#ffffff',
                borderWidth: 4,
                cutout: '72%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endsection
