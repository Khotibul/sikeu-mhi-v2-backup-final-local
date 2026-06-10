@extends('layouts.app')

@section('title', 'Kelas Formal - SIKEU MHI V2')

@section('page_title', 'Kelas Formal')

@section('page_subtitle', 'Kelola data kelas formal dan nominal SPP formal.')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="page-actions">
        <form action="{{ route('kelas-formal.index') }}" method="GET" class="search-box">
            <span>🔍</span>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama kelas formal..."
            >
            <button type="submit" class="btn btn-light">Cari</button>

            @if(request('search'))
                <a href="{{ route('kelas-formal.index') }}" class="btn btn-warning">Reset</a>
            @endif
        </form>

        <a href="{{ route('kelas-formal.create') }}" class="btn btn-primary">
            + Tambah Kelas Formal
        </a>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="70">No</th>
                        <th>Nama Kelas</th>
                        <th>Nominal SPP</th>
                        <th width="170">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($kelasFormal as $index => $kelas)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="student-name">{{ $kelas->nama_kelas }}</div>
                                <div class="muted">Kelas Formal</div>
                            </td>
                            <td>
                                <strong>
                                    Rp {{ number_format($kelas->nominal_spp ?? 0, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="{{ route('kelas-formal.edit', $kelas->id_kelas) }}" class="btn btn-warning">
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('kelas-formal.destroy', $kelas->id_kelas) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus kelas formal ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 30px;">
                                <strong>Data kelas formal belum tersedia.</strong>
                                <div class="muted">Silakan tambahkan kelas formal baru.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection