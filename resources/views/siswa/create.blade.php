@extends('layouts.app')

@section('title', 'Tambah Santri - SIKEU MHI V2')
@section('page_title', 'Tambah Santri')
@section('page_subtitle', 'NIS dibuat otomatis dari NIS terakhir + 1.')

@section('content')
    @include('siswa._form-style')

    @if ($errors->any())
        <div class="siswa-alert siswa-alert-error">⚠️ {{ $errors->first() }}</div>
    @endif

    <div class="siswa-card">
        <form action="{{ route('siswa.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @include('siswa._form-fields', [
                'mode' => 'create',
                'siswa' => null,
                'nisOtomatis' => $nisOtomatis ?? '-',
                'kelasFormal' => $kelasFormal ?? collect(),
                'kelasDiniyah' => $kelasDiniyah ?? collect(),
            ])

            <div class="siswa-form-actions">
                <a href="{{ route('siswa.index') }}" class="siswa-btn siswa-btn-light">← Kembali</a>
                <button type="submit" class="siswa-btn siswa-btn-primary">💾 Simpan Santri</button>
            </div>
        </form>
    </div>
@endsection
