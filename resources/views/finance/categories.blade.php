@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="header-section">
    <div class="header-title">
        <h1>Kategori Transaksi</h1>
        <p>Kelola kategori untuk pemasukan dan pengeluaran Anda.</p>
    </div>
    <div class="header-action">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus me-2"></i> Tambah Kategori
        </button>
    </div>
</div>

@if($errors->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 py-3">Nama Kategori</th>
                        <th class="border-0 py-3">Tipe</th>
                        <th class="text-center pe-4 border-0 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px; background-color: {{ $category->color ?? '#64748b' }}15; color: {{ $category->color ?? '#64748b' }};">
                                        <i class="fas fa-{{ $category->icon ?? 'dot-circle' }}"></i>
                                    </div>
                                    <div class="fw-semibold text-dark">{{ $category->name }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $category->type === 'income' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $category->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-light btn-sm text-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus kategori ini? Penghapusan akan gagal jika kategori sudah digunakan dalam transaksi.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-sm text-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Category Modal -->
                        <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Edit Kategori</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('categories.update', $category) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-body p-4 text-start">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">NAMA KATEGORI</label>
                                                <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">TIPE</label>
                                                <select name="type" class="form-select" required>
                                                    <option value="expense" {{ $category->type === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                                                    <option value="income" {{ $category->type === 'income' ? 'selected' : '' }}>Pemasukan</option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-6 mb-3">
                                                    <label class="form-label small fw-bold text-muted">WARNA</label>
                                                    <input type="color" name="color" class="form-control form-control-color w-100" value="{{ $category->color ?? '#64748b' }}" title="Pilih warna">
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label class="form-label small fw-bold text-muted">IKON (FontAwesome)</label>
                                                    <input type="text" name="icon" class="form-control" placeholder="Contoh: shopping-cart" value="{{ $category->icon }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0 p-4">
                                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <i class="fas fa-tags fa-3x mb-3 opacity-25"></i>
                                <div>Belum ada kategori.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NAMA KATEGORI</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Belanja Bulanan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">TIPE</label>
                        <select name="type" class="form-select" required>
                            <option value="expense">Pengeluaran</option>
                            <option value="income">Pemasukan</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold text-muted">WARNA</label>
                            <input type="color" name="color" class="form-control form-control-color w-100" value="#64748b" title="Pilih warna">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold text-muted">IKON (FontAwesome)</label>
                            <input type="text" name="icon" class="form-control" placeholder="Contoh: shopping-cart">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
