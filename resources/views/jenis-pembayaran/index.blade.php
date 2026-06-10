@extends('layouts.app')

@section('title', 'Jenis Pembayaran - SIKEU MHI V2')

@section('page_title', 'Jenis Pembayaran')

@section('page_subtitle', 'Atur tagihan non-bulanan santri seperti daftar ulang, UKT, seragam, kitab, dan lainnya.')

@section('content')
<style>
    .type-filter {
        background: white;
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 18px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 20px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 12px;
        align-items: end;
    }

    .filter-group {
        display: grid;
        gap: 7px;
    }

    .filter-group label,
    .modal-group label {
        font-size: 12px;
        font-weight: 900;
        color: var(--muted);
        text-transform: uppercase;
    }

    .filter-control,
    .modal-control {
        width: 100%;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 12px 13px;
        font-size: 14px;
        outline: none;
        background: white;
        color: var(--text);
    }

    .filter-control:focus,
    .modal-control:focus {
        border-color: var(--tosca);
        box-shadow: 0 0 0 4px rgba(18, 169, 154, .10);
    }

    .type-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 20px;
    }

    .summary-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 22px;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .summary-card::after {
        content: "";
        position: absolute;
        width: 105px;
        height: 105px;
        border-radius: 999px;
        background: var(--tosca-soft);
        right: -30px;
        top: -34px;
    }

    .summary-card.pink::after {
        background: var(--pink-soft);
    }

    .summary-card span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        margin-bottom: 8px;
        position: relative;
        z-index: 1;
    }

    .summary-card strong {
        display: block;
        color: var(--tosca-dark);
        font-size: 30px;
        font-weight: 950;
        position: relative;
        z-index: 1;
    }

    .summary-card.pink strong {
        color: var(--pink-dark);
    }

    .type-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 26px;
        padding: 22px;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .type-card::after {
        content: "";
        position: absolute;
        width: 280px;
        height: 280px;
        background: url("{{ asset('images/logo-mhi.png') }}") center/contain no-repeat;
        opacity: .035;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
    }

    .type-content {
        position: relative;
        z-index: 1;
    }

    .type-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
    }

    .type-header h3 {
        margin: 0;
        color: var(--tosca-dark);
        font-size: 20px;
        font-weight: 950;
    }

    .type-table-wrap {
        overflow-x: auto;
        border: 1px solid #d7e1e7;
        border-radius: 18px;
    }

    .type-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        background: rgba(255,255,255,.96);
    }

    .type-table th,
    .type-table td {
        border: 1px solid #d7e1e7;
        padding: 12px 10px;
        vertical-align: middle;
        text-align: left;
    }

    .type-table th {
        background: #e7f9f6;
        color: var(--tosca-dark);
        text-transform: uppercase;
        font-size: 12px;
        font-weight: 900;
    }

    .type-table td.number {
        text-align: right;
        white-space: nowrap;
        color: var(--tosca-dark);
        font-weight: 900;
    }

    .type-name {
        font-weight: 900;
        color: var(--text);
    }

    .type-badge {
        display: inline-flex;
        padding: 5px 10px;
        border-radius: 999px;
        background: var(--pink-soft);
        color: var(--pink-dark);
        font-size: 11px;
        font-weight: 900;
    }

    .action-group {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .mini-btn {
        border: none;
        border-radius: 12px;
        padding: 8px 12px;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        font-weight: 850;
        cursor: pointer;
        text-decoration: none;
        transition: .15s ease-in-out;
        line-height: 1;
    }

    .mini-btn:hover {
        transform: translateY(-1px);
    }

    .mini-btn.edit {
        background: #f3f4f6;
        color: #374151;
    }

    .mini-btn.delete {
        background: linear-gradient(135deg, #ef476f, #e11d48);
        color: white;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .55);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal-card {
        width: 100%;
        max-width: 560px;
        background: white;
        border-radius: 28px;
        box-shadow: 0 28px 80px rgba(15, 23, 42, .25);
        overflow: hidden;
    }

    .modal-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, var(--tosca), var(--pink));
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 950;
    }

    .modal-header p {
        margin: 4px 0 0;
        opacity: .9;
        font-size: 13px;
    }

    .modal-close {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        border: none;
        background: rgba(255,255,255,.20);
        color: white;
        font-size: 22px;
        cursor: pointer;
        font-weight: 900;
    }

    .modal-body {
        padding: 24px;
        display: grid;
        gap: 16px;
    }

    .modal-group {
        display: grid;
        gap: 7px;
    }

    .modal-footer {
        padding: 16px 24px 24px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    @media(max-width: 900px) {
        .filter-grid,
        .type-summary {
            grid-template-columns: 1fr;
        }

        .type-header {
            align-items: stretch;
            flex-direction: column;
        }
    }
</style>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Periksa kembali data:</strong>
        <ul style="margin:8px 0 0; padding-left:20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="type-filter">
    <form action="{{ route('jenis-pembayaran.index') }}" method="GET" class="filter-grid">
        <div class="filter-group">
            <label>Cari Jenis Pembayaran</label>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                class="filter-control"
                placeholder="Contoh: daftar ulang, UKT, seragam..."
            >
        </div>

        <button type="submit" class="btn btn-primary">
            Tampilkan
        </button>
    </form>
</div>

<div class="type-summary">
    <div class="summary-card">
        <span>Total Jenis Pembayaran</span>
        <strong>{{ number_format($jenisPembayaran->count(), 0, ',', '.') }}</strong>
    </div>

    <div class="summary-card">
        <span>Total Nominal Standar</span>
        <strong>Rp {{ number_format($jenisPembayaran->sum('nominal_standar'), 0, ',', '.') }}</strong>
    </div>

    <div class="summary-card pink">
        <span>Jenis Tagihan Santri</span>
        <strong>Non Bulanan</strong>
    </div>
</div>

<div class="type-card">
    <div class="type-content">
        <div class="type-header">
            <div>
                <h3>Data Jenis Pembayaran</h3>
                <p style="margin:5px 0 0; color:var(--muted);">
                    Dipakai untuk daftar ulang, UKT, seragam, kitab, kegiatan, dan pembayaran khusus santri.
                </p>
            </div>

            <button type="button" class="btn btn-danger js-open-create">
                + Tambah Jenis
            </button>
        </div>

        <div class="type-table-wrap">
            <table class="type-table">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Nama Jenis</th>
                        <th width="220">Nominal Standar</th>
                        <th width="180">Status</th>
                        <th width="190">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($jenisPembayaran as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td>
                                <div class="type-name">{{ $item->nama_jenis }}</div>
                            </td>

                            <td class="number">
                                Rp {{ number_format($item->nominal_standar, 0, ',', '.') }}
                            </td>

                            <td>
                                <span class="type-badge">Tagihan Santri</span>
                            </td>

                            <td>
                                <div class="action-group">
                                    <button
                                        type="button"
                                        class="mini-btn edit js-open-edit"
                                        data-id="{{ $item->id_jenis }}"
                                    >
                                        ✏️ Edit
                                    </button>

                                    <form
                                        action="{{ route('jenis-pembayaran.destroy', $item->id_jenis) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus jenis pembayaran ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="mini-btn delete">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:34px; color:var(--muted);">
                                Belum ada jenis pembayaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-overlay" id="createModal">
    <div class="modal-card">
        <form action="{{ route('jenis-pembayaran.store') }}" method="POST">
            @csrf

            <div class="modal-header">
                <div>
                    <h3>Tambah Jenis Pembayaran</h3>
                    <p>Atur nominal standar untuk tagihan khusus santri.</p>
                </div>

                <button type="button" class="modal-close js-close-modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="modal-group">
                    <label>Nama Jenis</label>
                    <input
                        type="text"
                        name="nama_jenis"
                        class="modal-control"
                        placeholder="Contoh: UKT Kuliah Semester 1"
                        required
                    >
                </div>

                <div class="modal-group">
                    <label>Nominal Standar</label>
                    <input
                        type="number"
                        name="nominal_standar"
                        class="modal-control"
                        placeholder="0"
                        min="0"
                        required
                    >
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light js-close-modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Jenis</button>
            </div>
        </form>
    </div>
</div>

@foreach($jenisPembayaran as $item)
    <div class="modal-overlay" id="editModal-{{ $item->id_jenis }}">
        <div class="modal-card">
            <form action="{{ route('jenis-pembayaran.update', $item->id_jenis) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <div>
                        <h3>Edit Jenis Pembayaran</h3>
                        <p>Perbarui nama dan nominal standar.</p>
                    </div>

                    <button type="button" class="modal-close js-close-modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="modal-group">
                        <label>Nama Jenis</label>
                        <input
                            type="text"
                            name="nama_jenis"
                            value="{{ $item->nama_jenis }}"
                            class="modal-control"
                            required
                        >
                    </div>

                    <div class="modal-group">
                        <label>Nominal Standar</label>
                        <input
                            type="number"
                            name="nominal_standar"
                            value="{{ $item->nominal_standar }}"
                            class="modal-control"
                            min="0"
                            required
                        >
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light js-close-modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Jenis</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

<script>
    const createModal = document.getElementById('createModal');

    document.querySelectorAll('.js-open-create').forEach(function (button) {
        button.addEventListener('click', function () {
            createModal.classList.add('show');
        });
    });

    document.querySelectorAll('.js-open-edit').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const modal = document.getElementById('editModal-' + id);

            if (modal) {
                modal.classList.add('show');
            }
        });
    });

    document.querySelectorAll('.js-close-modal').forEach(function (button) {
        button.addEventListener('click', function () {
            this.closest('.modal-overlay').classList.remove('show');
        });
    });

    document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) {
                overlay.classList.remove('show');
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.show').forEach(function (modal) {
                modal.classList.remove('show');
            });
        }
    });
</script>
@endsection