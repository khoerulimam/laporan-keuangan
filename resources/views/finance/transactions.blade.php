@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
<div class="header-section">
    <div class="header-title">
        <h1>Transaksi</h1>
        <p>Kelola semua pemasukan dan pengeluaran Anda.</p>
    </div>
    <div class="header-action d-flex gap-3">
        <form action="{{ route('transactions.index') }}" method="GET" class="d-flex gap-2">
            <input type="month" name="month" class="form-control" value="{{ $selectedMonth->format('Y-m') }}" onchange="this.form.submit()">
        </form>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
            <i class="fas fa-plus me-2"></i> Tambah Transaksi
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 py-3">Tanggal</th>
                        <th class="border-0 py-3">Deskripsi & Akun</th>
                        <th class="border-0 py-3">Kategori</th>
                        <th class="border-0 py-3">Merchant</th>
                        <th class="text-end border-0 py-3">Jumlah</th>
                        <th class="text-center pe-4 border-0 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-medium">{{ $transaction->transaction_date->format('d M Y') }}</div>
                                <div class="small text-muted">{{ $transaction->transaction_date->format('H:i') }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $transaction->description }}</div>
                                <div class="small text-muted text-uppercase">{{ $transaction->account->name }}</div>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2" style="background-color: {{ $transaction->category->color }}20; color: {{ $transaction->category->color }};">
                                    <i class="fas fa-{{ $transaction->category->icon }} me-1"></i> {{ $transaction->category->name }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $transaction->merchant ?? '-' }}
                            </td>
                            <td class="text-end fw-bold {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </td>
                            <td class="text-center pe-4">
                                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm text-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fas fa-receipt fa-3x opacity-25"></i></div>
                                Belum ada transaksi di bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transactions->hasPages())
    <div class="card-footer bg-transparent border-0 px-4 py-3">
        {{ $transactions->withQueryString()->links() }}
    </div>
    @endif
</div>

<!-- Add Transaction Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Transaksi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">TIPE</label>
                            <div class="d-flex gap-3">
                                <div class="flex-fill">
                                    <input type="radio" class="btn-check" name="type" id="type-expense" value="expense" checked required>
                                    <label class="btn btn-outline-danger w-100 py-3" for="type-expense">
                                        <i class="fas fa-minus-circle me-2"></i> Pengeluaran
                                    </label>
                                </div>
                                <div class="flex-fill">
                                    <input type="radio" class="btn-check" name="type" id="type-income" value="income" required>
                                    <label class="btn btn-outline-success w-100 py-3" for="type-income">
                                        <i class="fas fa-plus-circle me-2"></i> Pemasukan
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">JUMLAH (RP)</label>
                            <input type="number" name="amount" class="form-control form-control-lg" placeholder="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">TANGGAL</label>
                            <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">AKUN</label>
                            <select name="account_id" class="form-select" required>
                                <option value="" disabled selected>Pilih Akun</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">KATEGORI</label>
                            <select name="category_id" class="form-select" required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" data-type="{{ $category->type }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">MERCHANT / SUMBER</label>
                            <input type="text" name="merchant" class="form-control" placeholder="Contoh: Indomaret, Gaji Kantor">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">DESKRIPSI</label>
                            <input type="text" name="description" class="form-control" placeholder="Tulis catatan singkat..." required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
