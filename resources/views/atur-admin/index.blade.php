@extends('layouts.app')

@section('title', 'Atur Admin - SIKEU MHI V2')

@section('page_title', 'Atur Admin')

@section('page_subtitle', 'Kelola akun admin, level akses, unit, username, dan password.')

@section('content')
<style>
    .admin-hero {
        background: linear-gradient(135deg, var(--tosca), var(--pink));
        color: #ffffff;
        border-radius: 30px;
        padding: 30px;
        margin-bottom: 22px;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .admin-hero::after {
        content: "";
        position: absolute;
        width: 240px;
        height: 240px;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        right: -70px;
        top: -85px;
    }

    .admin-hero h2 {
        margin: 0 0 10px;
        font-size: 32px;
        font-weight: 950;
        position: relative;
        z-index: 1;
    }

    .admin-hero p {
        margin: 0;
        line-height: 1.8;
        max-width: 900px;
        font-weight: 700;
        position: relative;
        z-index: 1;
    }

    .admin-summary {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    .summary-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 20px;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .summary-card::after {
        content: "";
        position: absolute;
        width: 96px;
        height: 96px;
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
        font-weight: 950;
        text-transform: uppercase;
        margin-bottom: 8px;
        position: relative;
        z-index: 1;
    }

    .summary-card strong {
        display: block;
        color: var(--tosca-dark);
        font-size: 26px;
        font-weight: 950;
        position: relative;
        z-index: 1;
    }

    .summary-card.pink strong {
        color: var(--pink-dark);
    }

    .admin-panel {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 28px;
        padding: 22px;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .admin-panel::after {
        content: "";
        position: absolute;
        width: 320px;
        height: 320px;
        background: url("{{ asset('images/logo-mhi.png') }}") center/contain no-repeat;
        opacity: .028;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
    }

    .panel-content {
        position: relative;
        z-index: 1;
    }

    .admin-toolbar {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 14px;
        align-items: end;
        margin-bottom: 18px;
    }

    .search-form {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 10px;
        align-items: end;
    }

    .field-group {
        display: grid;
        gap: 7px;
    }

    .field-group label {
        color: var(--muted);
        font-size: 12px;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .field-control {
        width: 100%;
        height: 46px;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 0 14px;
        outline: none;
        font-size: 14px;
        background: #ffffff;
        color: var(--text);
        font-weight: 800;
    }

    .field-control::placeholder {
        color: #94a3b8;
        font-weight: 700;
    }

    .field-control:focus {
        border-color: var(--tosca);
        box-shadow: 0 0 0 4px rgba(18,169,154,.10);
    }

    .table-wrap {
        overflow-x: auto;
        border: 1px solid #d7e1e7;
        border-radius: 20px;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 980px;
        background: rgba(255,255,255,.98);
        font-size: 14px;
    }

    .admin-table th,
    .admin-table td {
        border: 1px solid #d7e1e7;
        padding: 13px 12px;
        text-align: left;
        vertical-align: middle;
    }

    .admin-table th {
        background: #e7f9f6;
        color: var(--tosca-dark);
        font-size: 12px;
        font-weight: 950;
        text-transform: uppercase;
    }

    .admin-name {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar-mini {
        width: 44px;
        height: 44px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--tosca-soft), var(--pink-soft));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--tosca-dark);
        font-weight: 950;
        font-size: 17px;
        flex: 0 0 auto;
    }

    .admin-name strong {
        display: block;
        color: var(--text);
        font-weight: 950;
        text-transform: uppercase;
        margin-bottom: 3px;
    }

    .admin-name small {
        color: var(--muted);
        font-weight: 800;
    }

    .badge-admin {
        display: inline-flex;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 950;
        text-transform: uppercase;
    }

    .badge-tosca {
        background: var(--tosca-soft);
        color: var(--tosca-dark);
    }

    .badge-pink {
        background: var(--pink-soft);
        color: var(--pink-dark);
    }

    .action-group {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .icon-btn {
        width: 38px;
        height: 38px;
        border-radius: 13px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 15px;
        cursor: pointer;
        font-weight: 950;
    }

    .icon-edit {
        background: #eefbf8;
        color: var(--tosca-dark);
    }

    .icon-delete {
        background: linear-gradient(135deg, #ef476f, #e11d48);
        color: #ffffff;
    }

    .empty-state {
        text-align: center;
        padding: 42px 18px;
        color: var(--muted);
    }

    .empty-state h3 {
        color: var(--tosca-dark);
        margin: 0 0 8px;
        font-size: 22px;
        font-weight: 950;
    }
        .admin-modal {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .58);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
    }

    .admin-modal.show {
        display: flex;
    }

    .modal-box {
        width: 620px;
        max-width: 100%;
        max-height: 92vh;
        overflow-y: auto;
        background: #ffffff;
        border-radius: 28px;
        box-shadow: 0 30px 80px rgba(15,23,42,.28);
        border: 1px solid rgba(15, 118, 110, .16);
        position: relative;
    }

    .modal-header {
        padding: 22px 24px;
        background: linear-gradient(135deg, var(--tosca), var(--pink));
        color: #ffffff;
        border-radius: 28px 28px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 22px;
        font-weight: 950;
    }

    .modal-header p {
        margin: 5px 0 0;
        opacity: .92;
        font-size: 13px;
        line-height: 1.5;
    }

    .modal-close {
        width: 38px;
        height: 38px;
        border-radius: 14px;
        border: none;
        background: rgba(255,255,255,.22);
        color: #ffffff;
        font-size: 18px;
        cursor: pointer;
        font-weight: 950;
    }

    .modal-body {
        padding: 22px 24px;
    }

    .modal-form {
        display: grid;
        gap: 14px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .password-note {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        padding: 12px 14px;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.6;
        font-weight: 800;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 8px;
    }

    .btn-disabled {
        opacity: .45;
        cursor: not-allowed !important;
    }

    .admin-table button:disabled {
        pointer-events: none;
    }

    .content-wrap a.btn-primary,
    .content-wrap button.btn-primary {
        color: #ffffff !important;
    }

    .content-wrap a.btn-light,
    .content-wrap button.btn-light {
        color: #334155 !important;
    }

    @media(max-width: 1000px) {
        .admin-summary,
        .admin-toolbar,
        .search-form,
        .form-grid {
            grid-template-columns: 1fr;
        }

        .admin-hero h2 {
            font-size: 26px;
        }

        .modal-footer {
            flex-direction: column;
        }

        .modal-footer .btn {
            width: 100%;
        }
    }
</style>

@php
    $admins = $admins ?? collect();
    $search = $search ?? request('search');

    $units = $units ?? [
        'SEMUA',
        'SMP',
        'MTS',
        'SMK',
        'MA',
        'SPM ULYA',
        "MA'HAD ALY",
        'MADIN NUHA',
        'YAYASAN',
        'PONDOK PA',
        'PONDOK PI',
    ];

    $levels = $levels ?? [
        'superadmin',
        'admin',
        'bendahara',
        'operator',
    ];

    $adminLoginId = session('admin_id');

    $totalAdmin = $admins->count();
    $totalSuperadmin = $admins->where('level', 'superadmin')->count();
    $totalUnit = $admins->pluck('unit')->filter()->unique()->count();
@endphp

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
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
<div class="admin-hero">
    <h2>Atur Admin</h2>
    <p>
        Kelola akun administrator sistem, level akses, unit kerja, username, serta password.
        Gunakan fitur ini dengan hati-hati karena admin memiliki akses ke data sistem dan keuangan.
    </p>
</div>

<div class="admin-summary">
    <div class="summary-card">
        <span>Total Admin</span>
        <strong>{{ number_format($totalAdmin, 0, ',', '.') }}</strong>
    </div>

    <div class="summary-card pink">
        <span>Superadmin</span>
        <strong>{{ number_format($totalSuperadmin, 0, ',', '.') }}</strong>
    </div>

    <div class="summary-card">
        <span>Unit Aktif</span>
        <strong>{{ number_format($totalUnit, 0, ',', '.') }}</strong>
    </div>

    <div class="summary-card pink">
        <span>Admin Login</span>
        <strong style="font-size:18px;">
            {{ session('admin_nama') ?? session('admin_username') ?? '-' }}
        </strong>
    </div>
</div>

<div class="admin-panel">
    <div class="panel-content">
        <div class="admin-toolbar">
            <form action="{{ route('atur-admin.index') }}" method="GET" class="search-form">
                <div class="field-group">
                    <label>Cari Admin</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        class="field-control"
                        placeholder="Ketik nama, username, level, atau unit..."
                    >
                </div>

                <button type="submit" class="btn btn-primary">
                    🔎 Cari
                </button>

                <a href="{{ route('atur-admin.index') }}" class="btn btn-light">
                    Reset
                </a>
            </form>

            <button type="button" class="btn btn-primary" onclick="openAdminModal('modalTambahAdmin')">
                + Tambah Admin
            </button>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Admin</th>
                        <th>Username</th>
                        <th>Level</th>
                        <th>Unit</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($admins as $index => $admin)
                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td>
                                <div class="admin-name">
                                    <div class="avatar-mini">
                                        {{ strtoupper(substr($admin->nama_lengkap ?? $admin->username ?? 'A', 0, 1)) }}
                                    </div>

                                    <div>
                                        <strong>{{ $admin->nama_lengkap ?? '-' }}</strong>
                                        <small>ID Admin: {{ $admin->id_admin }}</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <strong>{{ $admin->username ?? '-' }}</strong>
                            </td>

                            <td>
                                <span class="badge-admin badge-tosca">
                                    {{ $admin->level ?? '-' }}
                                </span>
                            </td>

                            <td>
                                <span class="badge-admin badge-pink">
                                    {{ $admin->unit ?? '-' }}
                                </span>
                            </td>

                            <td>
                                <div class="action-group">
                                    <button
                                        type="button"
                                        class="icon-btn icon-edit"
                                        title="Edit Admin"
                                        onclick="openAdminModal('modalEditAdmin{{ $admin->id_admin }}')"
                                    >
                                        ✏️
                                    </button>

                                    @if((int) $adminLoginId === (int) $admin->id_admin)
                                        <button
                                            type="button"
                                            class="icon-btn icon-delete btn-disabled"
                                            title="Tidak bisa hapus admin yang sedang login"
                                            disabled
                                        >
                                            🗑
                                        </button>
                                    @else
                                        <form
                                            action="{{ route('atur-admin.destroy', $admin->id_admin) }}"
                                            method="POST"
                                            data-confirm="Hapus admin {{ $admin->nama_lengkap ?? $admin->username }}?"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="icon-btn icon-delete" title="Hapus Admin">
                                                🗑
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <h3>Belum ada data admin</h3>
                                    <p>Tambahkan admin baru untuk mulai mengatur akses sistem.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{-- Modal Tambah Admin --}}
<div class="admin-modal" id="modalTambahAdmin">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <h3>Tambah Admin</h3>
                <p>Buat akun admin baru untuk mengakses sistem.</p>
            </div>

            <button type="button" class="modal-close" onclick="closeAdminModal('modalTambahAdmin')">
                ×
            </button>
        </div>

        <div class="modal-body">
            <form
                action="{{ route('atur-admin.store') }}"
                method="POST"
                class="modal-form"
                data-confirm="Simpan admin baru ini?"
            >
                @csrf

                <div class="field-group">
                    <label>Nama Lengkap</label>
                    <input
                        type="text"
                        name="nama_lengkap"
                        class="field-control"
                        placeholder="Contoh: A. Rizal Rosyiful Huda"
                        value="{{ old('nama_lengkap') }}"
                        required
                    >
                </div>

                <div class="form-grid">
                    <div class="field-group">
                        <label>Username</label>
                        <input
                            type="text"
                            name="username"
                            class="field-control"
                            placeholder="Contoh: admin"
                            value="{{ old('username') }}"
                            required
                        >
                    </div>

                    <div class="field-group">
                        <label>Password</label>
                        <input
                            type="password"
                            name="password"
                            class="field-control"
                            placeholder="Minimal 4 karakter"
                            required
                        >
                    </div>
                </div>

                <div class="form-grid">
                    <div class="field-group">
                        <label>Level</label>
                        <select name="level" class="field-control" required>
                            @foreach($levels as $level)
                                <option value="{{ $level }}" {{ old('level') == $level ? 'selected' : '' }}>
                                    {{ strtoupper($level) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field-group">
                        <label>Unit</label>
                        <select name="unit" class="field-control" required>
                            @foreach($units as $unit)
                                <option value="{{ $unit }}" {{ old('unit', 'SEMUA') == $unit ? 'selected' : '' }}>
                                    {{ $unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" onclick="closeAdminModal('modalTambahAdmin')">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Simpan Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Admin --}}
@foreach($admins as $admin)
    <div class="admin-modal" id="modalEditAdmin{{ $admin->id_admin }}">
        <div class="modal-box">
            <div class="modal-header">
                <div>
                    <h3>Edit Admin</h3>
                    <p>Perbarui data admin {{ $admin->nama_lengkap ?? $admin->username }}.</p>
                </div>

                <button
                    type="button"
                    class="modal-close"
                    onclick="closeAdminModal('modalEditAdmin{{ $admin->id_admin }}')"
                >
                    ×
                </button>
            </div>

            <div class="modal-body">
                <form
                    action="{{ route('atur-admin.update', $admin->id_admin) }}"
                    method="POST"
                    class="modal-form"
                    data-confirm="Simpan perubahan admin ini?"
                >
                    @csrf
                    @method('PUT')

                    <div class="field-group">
                        <label>Nama Lengkap</label>
                        <input
                            type="text"
                            name="nama_lengkap"
                            class="field-control"
                            value="{{ old('nama_lengkap', $admin->nama_lengkap) }}"
                            required
                        >
                    </div>

                    <div class="form-grid">
                        <div class="field-group">
                            <label>Username</label>
                            <input
                                type="text"
                                name="username"
                                class="field-control"
                                value="{{ old('username', $admin->username) }}"
                                required
                            >
                        </div>

                        <div class="field-group">
                            <label>Password Baru</label>
                            <input
                                type="password"
                                name="password"
                                class="field-control"
                                placeholder="Kosongkan jika tidak diganti"
                            >
                        </div>
                    </div>

                    <div class="password-note">
                        Password boleh dikosongkan kalau tidak ingin diganti. Jika diisi, password lama akan diganti dengan password baru.
                    </div>

                    <div class="form-grid">
                        <div class="field-group">
                            <label>Level</label>
                            <select name="level" class="field-control" required>
                                @foreach($levels as $level)
                                    <option value="{{ $level }}" {{ old('level', $admin->level) == $level ? 'selected' : '' }}>
                                        {{ strtoupper($level) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field-group">
                            <label>Unit</label>
                            <select name="unit" class="field-control" required>
                                @foreach($units as $unit)
                                    <option value="{{ $unit }}" {{ old('unit', $admin->unit) == $unit ? 'selected' : '' }}>
                                        {{ $unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-light"
                            onclick="closeAdminModal('modalEditAdmin{{ $admin->id_admin }}')"
                        >
                            Batal
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<script>
    function openAdminModal(id) {
        const modal = document.getElementById(id);

        if (modal) {
            modal.classList.add('show');
        }
    }

    function closeAdminModal(id) {
        const modal = document.getElementById(id);

        if (modal) {
            modal.classList.remove('show');
        }
    }

    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('admin-modal')) {
            event.target.classList.remove('show');
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.admin-modal.show').forEach(function (modal) {
                modal.classList.remove('show');
            });
        }
    });
</script>
@endsection