@extends('layouts.app')

@section('title', 'Kelas Diniyah - SIKEU MHI V2')

@section('page_title', 'Kelas Diniyah')

@section('page_subtitle', 'Kelola data kelas diniyah dan nominal SPP diniyah.')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="page-actions">
        <form action="{{ route('kelas-diniyah.index') }}" method="GET" class="search-box">
            <span>🔍</span>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama kelas diniyah..."
            >
            <button type="submit" class="btn btn-light">Cari</button>

            @if(request('search'))
                <a href="{{ route('kelas-diniyah.index') }}" class="btn btn-warning">Reset</a>
            @endif
        </form>

        <a href="{{ route('kelas-diniyah.create') }}" class="btn btn-primary">
            + Tambah Kelas Diniyah
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
                    @forelse($kelasDiniyah as $index => $kelas)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="student-name">{{ $kelas->nama_kelas }}</div>
                                <div class="muted">Kelas Diniyah</div>
                            </td>
                            <td>
                                <strong>
                                    Rp {{ number_format($kelas->nominal_spp ?? 0, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="{{ route('kelas-diniyah.edit', $kelas->id_diniyah) }}" class="btn btn-warning">
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('kelas-diniyah.destroy', $kelas->id_diniyah) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus kelas diniyah ini?')"
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
                                <strong>Data kelas diniyah belum tersedia.</strong>
                                <div class="muted">Silakan tambahkan kelas diniyah baru.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection