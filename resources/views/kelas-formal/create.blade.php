@extends('layouts.app')

@section('title', 'Tambah Kelas Formal - SIKEU MHI V2')

@section('page_title', 'Tambah Kelas Formal')

@section('page_subtitle', 'Tambahkan kelas formal beserta nominal SPP.')

@section('content')
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

    <form action="{{ route('kelas-formal.store') }}" method="POST">
        @csrf

        <div class="card">
            <h3 class="section-title">Form Kelas Formal</h3>

            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Kelas <span style="color: var(--pink)">*</span></label>
                    <input
                        type="text"
                        name="nama_kelas"
                        class="form-control"
                        value="{{ old('nama_kelas') }}"
                        placeholder="Contoh: VII A, VIII B, X IPA"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Nominal SPP <span style="color: var(--pink)">*</span></label>
                    <input
                        type="number"
                        name="nominal_spp"
                        class="form-control"
                        value="{{ old('nominal_spp', 0) }}"
                        min="0"
                        required
                    >
                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('kelas-formal.index') }}" class="btn btn-light">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Kelas</button>
            </div>
        </div>
    </form>
@endsection