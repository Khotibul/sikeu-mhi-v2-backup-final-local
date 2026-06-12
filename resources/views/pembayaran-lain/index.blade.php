@extends('layouts.app')

@section('title', 'Tagihan Tetap Santri - SIKEU MHI V2')
@section('page_title', 'Pembayaran Lain')
@section('page_subtitle', 'Kelola pembayaran tetap santri seperti SPP formal, syahriyah pondok, dan tagihan lainnya.')

@section('content')
    @include('pembayaran-lain._tabs')

    <style>
        .search-card {
            background: #fff;
            border: 1px solid rgba(15, 118, 110, .14);
            border-radius: 24px;
            padding: 20px;
            box-shadow: 0 18px 55px rgba(15, 23, 42, .08);
            margin-bottom: 18px;
        }

        .search-title {
            margin: 0;
            color: #0f766e;
            font-size: 22px;
            font-weight: 950;
        }

        .search-muted {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        .search-control {
            width: 100%;
            min-height: 42px;
            border: 1px solid #d5e1e8;
            border-radius: 14px;
            padding: 9px 12px;
            outline: none;
            font-weight: 750;
            color: #0f172a;
            background: #fff;
        }

        .search-btn {
            border: none;
            border-radius: 14px;
            padding: 10px 14px;
            font-weight: 900;
            font-size: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            white-space: nowrap;
            background: linear-gradient(135deg, #0f9f8f, #087c73);
            color: #fff !important;
            box-shadow: 0 12px 25px rgba(15, 118, 110, .2);
        }

        .search-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .search-item {
            padding: 12px;
            border: 1px solid #dbe5ec;
            border-radius: 10px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s ease;
        }

        .search-item:hover {
            background: #f8fffd;
            box-shadow: 0 4px 12px rgba(15, 118, 110, .1);
        }

        .search-item-name {
            font-weight: 950;
            color: #0f172a;
        }

        .search-item-detail {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .search-empty {
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
            font-weight: 850;
        }

        .search-btn-view {
            background: linear-gradient(135deg, #0f9f8f, #087c73);
            color: #fff !important;
            border: none;
            border-radius: 10px;
            padding: 8px 12px;
            font-weight: 900;
            font-size: 11px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }

        .search-btn-view:hover {
            opacity: 0.9;
        }
    </style>

    <div class="search-card">
        <h3 class="search-title">Cari Santri untuk Pembayaran</h3>
        <p class="search-muted">Cari santri berdasarkan nama, NIS, NISN, atau kelas untuk menambahkan pembayaran tagihan
            tetap.</p>
    </div>

    <div class="search-card">
        <form method="GET" action="{{ route('pembayaran-lain.index') }}"
            style="display: flex; gap: 8px; align-items: flex-end;">
            <div style="flex: 1;">
                <label
                    style="display: block; margin-bottom: 6px; color: #475569; font-size: 11px; font-weight: 900; text-transform: uppercase;">Kata
                    Kunci Pencarian</label>
                <input type="text" name="search" value="{{ $search ?? '' }}" class="search-control"
                    placeholder="Nama, NIS, NISN, kelas, nama wali..." required>
            </div>
            <button type="submit" class="search-btn">🔍 Cari</button>
            <a href="{{ route('pembayaran-lain.index') }}" class="search-btn"
                style="background: #f1f5f9; color: #334155 !important; box-shadow: none;">Reset</a>
        </form>
    </div>

    @if (!empty($search))
        <div class="search-card">
            @if ($hasilSiswa->count() > 0)
                <h4 style="margin: 0 0 16px 0; color: #0f172a; font-weight: 900;">Hasil Pencarian:
                    <strong>{{ $hasilSiswa->count() }}</strong> Santri Ditemukan</h4>
                <ul class="search-list">
                    @foreach ($hasilSiswa as $siswa)
                        <li class="search-item">
                            <div>
                                <div class="search-item-name">{{ $siswa->nama_siswa ?? '-' }}</div>
                                <div class="search-item-detail">NIS: {{ $siswa->nis ?? '-' }} | NISN:
                                    {{ $siswa->nisn ?? '-' }} | Kelas: {{ $siswa->kelas_formal ?? '-' }}</div>
                                @if ($siswa->kelas_diniyah)
                                    <div class="search-item-detail">Diniyah: {{ $siswa->kelas_diniyah }}</div>
                                @endif
                            </div>
                            <a href="{{ route('pembayaran-lain.siswa', $siswa->id_siswa) }}" class="search-btn-view">Kelola
                                Pembayaran →</a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="search-empty">
                    <div style="font-size: 48px; margin-bottom: 10px;">🔍</div>
                    <strong>Tidak Ada Hasil</strong>
                    <p>Santri dengan kata kunci "<strong>{{ $search }}</strong>" tidak ditemukan. Coba pencarian
                        lain.</p>
                </div>
            @endif
        </div>
    @else
        <div class="search-card" style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 56px; margin-bottom: 16px;">👥</div>
            <h4 style="margin: 0 0 8px 0; color: #0f766e; font-weight: 950; font-size: 18px;">Mulai Cari Santri</h4>
            <p style="color: #64748b; margin: 0; font-size: 14px;">Masukkan nama, NIS, atau kelas santri di atas untuk
                mengelola pembayaran tagihannya.</p>
        </div>
    @endif
@endsection
