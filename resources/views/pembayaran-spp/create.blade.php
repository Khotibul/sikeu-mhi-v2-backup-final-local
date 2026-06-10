@extends('layouts.app')

@section('title', 'Input Pembayaran SPP - SIKEU MHI V2')

@section('page_title', 'Input Pembayaran SPP')

@section('page_subtitle', 'Input pembayaran SPP formal atau diniyah berdasarkan nominal kelas dan potongan santri.')

@section('content')
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali data yang diisi:</strong>
            <ul style="margin: 8px 0 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pembayaran-spp.store') }}" method="POST">
        @csrf

        <div class="card">
            <h3 class="section-title">Form Pembayaran SPP</h3>

            <div class="form-grid">
                <div class="form-group">
                    <label>Jenis Pembayaran <span style="color: var(--pink)">*</span></label>
                    <select name="jenis" class="form-control" required>
                        <option value="formal" {{ old('jenis', $jenis) === 'formal' ? 'selected' : '' }}>
                            SPP Formal
                        </option>
                        <option value="diniyah" {{ old('jenis', $jenis) === 'diniyah' ? 'selected' : '' }}>
                            SPP Diniyah
                        </option>
                    </select>
                    <div class="muted">
                        Formal memakai potongan_formal. Diniyah memakai potongan_diniyah.
                    </div>
                </div>

                <div class="form-group">
                    <label>Santri <span style="color: var(--pink)">*</span></label>
                    <select name="id_siswa" class="form-control" required>
                        <option value="">-- Pilih Santri --</option>
                        @foreach($siswa as $item)
                            <option value="{{ $item->id_siswa }}" {{ old('id_siswa') == $item->id_siswa ? 'selected' : '' }}>
                                {{ $item->nama_siswa }}
                                - NIS: {{ $item->nis }}
                                - Formal: {{ $item->kelas_formal ?: '-' }}
                                - Diniyah: {{ $item->kelas_diniyah ?: '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Bulan Bayar <span style="color: var(--pink)">*</span></label>
                    <select name="bulan_bayar" class="form-control" required>
                        <option value="">-- Pilih Bulan --</option>
                        @foreach($bulanList as $bulan)
                            <option value="{{ $bulan }}" {{ old('bulan_bayar') === $bulan ? 'selected' : '' }}>
                                {{ $bulan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Tahun Bayar <span style="color: var(--pink)">*</span></label>
                    <input
                        type="text"
                        name="tahun_bayar"
                        class="form-control"
                        value="{{ old('tahun_bayar', date('Y')) }}"
                        placeholder="Contoh: 2025"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Tanggal Bayar <span style="color: var(--pink)">*</span></label>
                    <input
                        type="date"
                        name="tgl_bayar"
                        class="form-control"
                        value="{{ old('tgl_bayar', date('Y-m-d')) }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Tahun Ajaran <span style="color: var(--pink)">*</span></label>
                    <input
                        type="text"
                        name="tahun_ajaran"
                        class="form-control"
                        value="{{ old('tahun_ajaran', '2025/2026') }}"
                        placeholder="Contoh: 2025/2026"
                        required
                    >
                </div>
            </div>

            <div class="alert alert-success" style="margin-top: 20px;">
                <strong>Catatan logika nominal:</strong><br>
                Jika santri punya potongan SPP, maka nominal yang dibayar mengikuti potongan tersebut.
                Contoh: potongan_diniyah = 75000, maka pembayaran SPP diniyah per bulan = Rp 75.000.
            </div>

            <div class="form-footer">
                <a href="{{ route('pembayaran-spp.index', ['jenis' => $jenis]) }}" class="btn btn-light">
                    Kembali
                </a>

                <button type="submit" class="btn btn-primary">
                    Simpan Pembayaran
                </button>
            </div>
        </div>
    </form>
@endsection