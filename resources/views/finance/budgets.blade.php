@extends('layouts.app')

@section('title', 'Anggaran')

@section('content')
<div class="header-section">
    <div class="header-title">
        <h1>Anggaran Bulanan</h1>
        <p>Kontrol pengeluaran Anda agar tidak melewati batas.</p>
    </div>
    <div class="header-action d-flex gap-3">
        <form action="{{ route('budgets.index') }}" method="GET" class="d-flex gap-2">
            <input type="month" name="month" class="form-control" value="{{ $selectedMonth->format('Y-m') }}" onchange="this.form.submit()">
        </form>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBudgetModal">
            <i class="fas fa-plus me-2"></i> Set Anggaran
        </button>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Anggaran Utama Bulan Ini</h5>
                    <p class="mb-0 opacity-75 small">Total batas pengeluaran untuk seluruh kategori di bulan {{ $selectedMonth->translatedFormat('F Y') }}</p>
                </div>
                <div class="text-end">
                    @if($globalBudget)
                        <h3 class="mb-1 fw-bold">Rp {{ number_format($globalBudget->amount, 0, ',', '.') }}</h3>
                        @php
                            $remaining = $globalBudget->amount - $totalExpense;
                        @endphp
                        <p class="mb-0 {{ $remaining < 0 ? 'text-danger fw-bold' : 'opacity-75' }}">
                            {{ $remaining < 0 ? 'Over budget: Rp ' . number_format(abs($remaining), 0, ',', '.') : 'Sisa: Rp ' . number_format($remaining, 0, ',', '.') }}
                        </p>
                    @else
                        <h4 class="mb-1 opacity-75">Belum diset</h4>
                        <button type="button" class="btn btn-light btn-sm mt-2 text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#setMonthlyBudgetModal">
                            Set Anggaran Utama
                        </button>
                    @endif
                </div>
            </div>
            @if($globalBudget)
            <div class="card-footer bg-white bg-opacity-10 border-0 p-3 text-end">
                <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#setMonthlyBudgetModal">
                    Update Anggaran Utama
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    @forelse($budgets as $budget)
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: {{ $budget['category']->color }}20; color: {{ $budget['category']->color }};">
                            <i class="fas fa-{{ $budget['category']->icon }}"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">{{ $budget['category']->name }}</h6>
                            <small class="text-muted">Target: Rp {{ number_format($budget['limit'], 0, ',', '.') }}</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-bold {{ $budget['progress'] > 90 ? 'text-danger' : ($budget['progress'] > 70 ? 'text-warning' : 'text-success') }}">
                            {{ $budget['progress'] }}%
                        </span>
                        <form action="{{ route('budgets.destroy', $budget['id']) }}" method="POST" onsubmit="return confirm('Hapus batas anggaran untuk kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light text-danger" title="Hapus Anggaran">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="progress mb-3" style="height: 10px; border-radius: 5px; background-color: #f1f5f9;">
                    <div class="progress-bar rounded-pill" role="progressbar" 
                        style="width: {{ $budget['progress'] }}%; background-color: {{ $budget['category']->color }};" 
                        aria-valuenow="{{ $budget['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Terpakai: <strong>Rp {{ number_format($budget['spent'], 0, ',', '.') }}</strong></span>
                    <span class="{{ $budget['remaining'] < 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                        @if($budget['remaining'] < 0)
                            Over budget: Rp {{ number_format(abs($budget['remaining']), 0, ',', '.') }}
                        @else
                            Sisa: Rp {{ number_format($budget['remaining'], 0, ',', '.') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center text-muted">
                <i class="fas fa-piggy-bank fa-3x opacity-25 mb-3"></i>
                <p class="mb-0">Belum ada anggaran yang diset untuk bulan ini.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Add Budget Modal -->
<div class="modal fade" id="addBudgetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Atur Anggaran Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('budgets.store') }}" method="POST">
                @csrf
                <input type="hidden" name="month" value="{{ $selectedMonth->month }}">
                <input type="hidden" name="year" value="{{ $selectedMonth->year }}">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">KATEGORI PENGELUARAN</label>
                        <select name="category_id" class="form-select" required>
                            <option value="" disabled selected>Pilih Kategori</option>
                            @foreach($expenseCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">LIMIT ANGGARAN (RP)</label>
                        <input type="number" name="limit_amount" class="form-control form-control-lg" placeholder="0" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Anggaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Set Monthly Budget Modal -->
<div class="modal fade" id="setMonthlyBudgetModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('budgets.monthly.store') }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Set Anggaran Utama Bulan Ini</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="month" value="{{ $selectedMonth->month }}">
                <input type="hidden" name="year" value="{{ $selectedMonth->year }}">
                
                <div class="mb-0">
                    <label class="form-label small fw-bold text-muted">TOTAL ANGGARAN (RP)</label>
                    <input type="number" name="amount" class="form-control form-control-lg" required min="1" value="{{ $globalBudget ? (int)$globalBudget->amount : '' }}" placeholder="0">
                    <div class="form-text mt-2">Anggaran utama untuk seluruh pengeluaran di bulan {{ $selectedMonth->translatedFormat('F Y') }}.</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 p-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4">Simpan Anggaran</button>
            </div>
        </form>
    </div>
</div>

@endsection
