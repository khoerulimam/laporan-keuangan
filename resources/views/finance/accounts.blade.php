@extends('layouts.app')

@section('title', 'Akun Saya')

@section('content')
<div class="header-section">
    <div class="header-title">
        <h1>Akun & Saldo</h1>
        <p>Pantau semua dompet dan rekening bank Anda.</p>
    </div>
    <div class="header-action">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAccountModal">
            <i class="fas fa-plus me-2"></i> Tambah Akun
        </button>
    </div>
</div>

<div class="row g-4 mb-4">
    @foreach($accounts as $account)
    <div class="col-md-4">
        <div class="card border-0 shadow-sm overflow-hidden h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: {{ $account->color }}20; color: {{ $account->color }};">
                        <i class="fas {{ $account->type === 'bank' ? 'fa-university' : ($account->type === 'ewallet' ? 'fa-mobile-alt' : 'fa-wallet') }} fa-lg"></i>
                    </div>
                    <span class="badge text-uppercase bg-light text-muted">{{ $account->type }}</span>
                </div>
                <h5 class="fw-bold mb-1">{{ $account->name }}</h5>
                <p class="text-muted small mb-3">Saldo Saat Ini</p>
                <h3 class="fw-bold mb-0">Rp {{ number_format($account->balance, 0, ',', '.') }}</h3>
            </div>
            <div class="card-footer bg-light border-0 px-4 py-3 d-flex justify-content-between align-items: center">
                <div class="small text-muted">
                    {{ count($account->transactions) }} Transaksi
                </div>
                <div style="width: 20px; height: 20px; border-radius: 50%; background-color: {{ $account->color }}; border: 3px solid white; box-shadow: 0 0 0 1px {{ $account->color }}30;"></div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Add Account Modal -->
<div class="modal fade" id="addAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Akun Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('accounts.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NAMA AKUN</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Bank BCA, Dana, Cash" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">TIPE</label>
                        <select name="type" class="form-select" required>
                            <option value="bank">Bank</option>
                            <option value="ewallet">E-Wallet</option>
                            <option value="cash">Tunai / Cash</option>
                            <option value="saving">Tabungan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">SALDO AWAL (RP)</label>
                        <input type="number" name="opening_balance" class="form-control" value="0" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">WARNA IDENTITAS</label>
                        <input type="color" name="color" class="form-control form-control-color w-100" value="#4f46e5" title="Pilih warna untuk akun ini">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
