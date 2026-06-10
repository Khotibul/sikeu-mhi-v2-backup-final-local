@extends('layouts.app')

@section('title', 'Tambah Pengeluaran - SIKEU MHI V2')

@section('page_title', 'Tambah Pengeluaran')

@section('page_subtitle', 'Input catatan pengeluaran yayasan.')

@section('content')
    <div class="card">
        <form action="{{ route('pengeluaran.store') }}" method="POST" enctype="multipart/form-data"
            class="js-pengeluaran-submit">
            @csrf

            @include('pengeluaran.form', ['button' => 'Simpan'])
        </form>
    </div>
@endsection
