@extends('layouts.app')

@section('title', 'Edit Setoran Bebas - SIKEU MHI V2')
@section('page_title', 'Edit Setoran Bebas')
@section('page_subtitle', 'Perbarui data setoran bebas.')

@section('content')
@include('pembayaran-lain._tabs')

@if($errors->any())
    <div class="alert alert-danger">⚠️ {{ $errors->first() }}</div>
@endif

<form action="{{ route('pembayaran-lain.bebas.update', $item->id_masuk) }}" method="POST" data-confirm="Simpan perubahan setoran bebas ini?">
    @csrf
    @method('PUT')
    @include('pembayaran-lain.bebas._form', ['mode' => 'edit', 'item' => $item])
</form>
@endsection
