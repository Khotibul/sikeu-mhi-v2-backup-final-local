@extends('layouts.app')

@section('title', 'Tambah Setoran Bebas - SIKEU MHI V2')
@section('page_title', 'Tambah Setoran Bebas')
@section('page_subtitle', 'Input pemasukan bebas seperti infaq, kitab satuan, donasi, dan setoran umum.')

@section('content')
@include('pembayaran-lain._tabs')

@if($errors->any())
    <div class="alert alert-danger">⚠️ {{ $errors->first() }}</div>
@endif

<form action="{{ route('pembayaran-lain.bebas.store') }}" method="POST" data-confirm="Simpan setoran bebas ini?">
    @csrf
    @include('pembayaran-lain.bebas._form', ['mode' => 'create', 'item' => $item ?? null])
</form>
@endsection
