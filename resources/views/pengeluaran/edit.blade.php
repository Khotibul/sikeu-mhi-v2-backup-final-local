@extends('layouts.app')

@section('title', 'Edit Pengeluaran - SIKEU MHI V2')

@section('page_title', 'Edit Pengeluaran')

@section('page_subtitle', 'Perbarui catatan pengeluaran yayasan.')

@section('content')
<div class="card">
    <form action="{{ route('pengeluaran.update', $pengeluaran->id_keluar) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('pengeluaran.form', ['button' => 'Update'])
    </form>
</div>
@endsection