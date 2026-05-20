@extends('layouts.app')

@section('title', 'Target Tabungan')

@section('content')
<div class="header-section">
    <div class="header-title">
        <h1>Target Tabungan</h1>
        <p>Wujudkan impian Anda dengan menabung secara konsisten.</p>
    </div>
    <div class="header-action">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGoalModal">
            <i class="fas fa-plus me-2"></i> Buat Target Baru
        </button>
    </div>
</div>

<div class="row g-4 mb-4">
    @forelse($savingGoals as $goal)
    @php 
        $progress = $goal->target_amount > 0 ? min(100, round(($goal->current_amount / $goal->target_amount) * 100)) : 0; 
    @endphp
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">{{ $goal->name }}</h5>
                        <p class="text-muted small mb-0">Target: Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-end d-flex flex-column align-items-end gap-2">
                        <span class="badge rounded-pill bg-light text-primary px-3 py-2 border">
                            {{ $goal->target_date ? \Carbon\Carbon::parse($goal->target_date)->diffForHumans() : 'Tanpa tenggat' }}
                        </span>
                        <form action="{{ route('goals.destroy', $goal) }}" method="POST" onsubmit="return confirm('Hapus target ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger p-0 small" style="text-decoration: none;">
                                <i class="fas fa-trash-alt me-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="fw-medium">Progress</span>
                        <span class="fw-bold text-primary">{{ $progress }}%</span>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 6px; background-color: #f1f5f9;">
                        <div class="progress-bar rounded-pill" role="progressbar" 
                            style="width: {{ $progress }}%; background-color: {{ $goal->color }};" 
                            aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">Terkumpul</div>
                        <div class="fw-bold h5 mb-0">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-md-6">
                        <form action="{{ route('saving-goals.update', $goal) }}" method="POST" class="d-flex gap-2">
                            @csrf
                            @method('PATCH')
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0">Rp</span>
                                <input type="number" name="amount" class="form-control border-start-0" placeholder="0" required>
                                <button type="submit" class="btn btn-primary" title="Tambah Dana">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center text-muted">
                <i class="fas fa-bullseye fa-3x opacity-25 mb-3"></i>
                <p class="mb-0">Belum ada target tabungan yang dibuat.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Add Goal Modal -->
<div class="modal fade" id="addGoalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Buat Target Tabungan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('goals.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NAMA TARGET</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Liburan ke Jepang, Beli Laptop" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">TARGET JUMLAH (RP)</label>
                        <input type="number" name="target_amount" class="form-control form-control-lg" placeholder="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">SALDO AWAL (RP)</label>
                        <input type="number" name="current_amount" class="form-control" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">TANGGAL TARGET (OPSIONAL)</label>
                        <input type="date" name="target_date" class="form-control">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">WARNA IDENTITAS</label>
                        <input type="color" name="color" class="form-control form-control-color w-100" value="#14b8a6">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Target</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
