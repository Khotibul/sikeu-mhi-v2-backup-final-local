@extends('layouts.app')

@section('title', 'Edit Kelas Diniyah - SIKEU MHI V2')

@section('page_title', 'Edit Kelas Diniyah')

@section('page_subtitle', 'Perbarui data kelas diniyah dan nominal SPP.')

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

    <form action="{{ route('kelas-diniyah.update', $kelas->id_diniyah) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <h3 class="section-title">Form Edit Kelas Diniyah</h3>

            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Kelas <span style="color: var(--pink)">*</span></label>
                    <input
                        type="text"
                        name="nama_kelas"
                        class="form-control"
                        value="{{ old('nama_kelas', $kelas->nama_kelas) }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Nominal SPP <span style="color: var(--pink)">*</span></label>
                    <input
                        type="number"
                        name="nominal_spp"
                        class="form-control"
                        value="{{ old('nominal_spp', $kelas->nominal_spp) }}"
                        min="0"
                        required
                    >
                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('kelas-diniyah.index') }}" class="btn btn-light">Kembali</a>
                <button type="submit" class="btn btn-primary">Update Kelas</button>
            </div>
        </div>
    </form>
@endsection