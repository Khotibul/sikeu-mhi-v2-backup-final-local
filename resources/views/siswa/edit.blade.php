@extends('layouts.app')

@section('title', 'Edit Santri - SIKEU MHI V2')
@section('page_title', 'Edit Santri')
@section('page_subtitle', 'NIS tidak dapat diedit agar urutan data tetap aman.')

@section('content')
    @include('siswa._form-style')

    @if ($errors->any())
        <div class="siswa-alert siswa-alert-error">⚠️ {{ $errors->first() }}</div>
    @endif

    <div class="siswa-card">
        <form action="{{ route('siswa.update', $siswa->id_siswa) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('siswa._form-fields', [
                'mode' => 'edit',
                'siswa' => $siswa,
                'nisOtomatis' => $siswa->nis ?? '-',
                'kelasFormal' => $kelasFormal ?? collect(),
                'kelasDiniyah' => $kelasDiniyah ?? collect(),
            ])

            <div class="siswa-form-actions">
                <a href="{{ route('siswa.index') }}" class="siswa-btn siswa-btn-light">← Kembali</a>
                <button type="submit" class="siswa-btn siswa-btn-primary">💾 Update Santri</button>
            </div>
        </form>
    </div>
@endsection
