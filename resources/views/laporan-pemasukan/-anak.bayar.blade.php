{{-- 
    Partial: Daftar Santri yang Membayar
    Simpan sebagai:
    resources/views/laporan-pemasukan/_anak-bayar.blade.php

    Lalu panggil di index.blade.php:
    @include('laporan-pemasukan._anak-bayar')
--}}

@php
    $anakBayar = $anakBayar ?? collect();
@endphp

<div class="card" style="margin-top:20px;">
    <div
        style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:16px;">
        <div>
            <h3 style="margin:0; color:var(--tosca-dark); font-size:22px; font-weight:950;">
                Daftar Santri yang Membayar
            </h3>
            <p style="margin:5px 0 0; color:var(--muted);">
                Detail santri yang melakukan pembayaran sesuai filter laporan.
            </p>
        </div>

        <strong style="color:var(--pink-dark); font-size:18px;">
            {{ number_format($anakBayar->count(), 0, ',', '.') }} Transaksi
        </strong>
    </div>

    <div style="overflow-x:auto; border:1px solid #d7e1e7; border-radius:18px;">
        <table style="width:100%; border-collapse:collapse; min-width:1050px;">
            <thead>
                <tr>
                    <th width="55">No</th>
                    <th>Tanggal</th>
                    <th>Nama Santri</th>
                    <th>NIS</th>
                    <th>Kelas Formal</th>
                    <th>Kelas Diniyah</th>
                    <th>Jenis</th>
                    <th>Keterangan Bayar</th>
                    <th>Status</th>
                    <th style="text-align:right;">Nominal</th>
                </tr>
            </thead>

            <tbody>
                @forelse($anakBayar as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td>
                            @if (!empty($item->tanggal))
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                            @else
                                -
                            @endif
                        </td>

                        <td><strong>{{ $item->nama_siswa ?? '-' }}</strong></td>
                        <td>{{ $item->nis ?? '-' }}</td>
                        <td>{{ $item->kelas_formal ?? '-' }}</td>
                        <td>{{ $item->kelas_diniyah ?? '-' }}</td>

                        <td>
                            <span
                                style="display:inline-flex; padding:5px 10px; border-radius:999px; background:var(--tosca-soft); color:var(--tosca-dark); font-size:11px; font-weight:950; text-transform:uppercase;">
                                {{ $item->jenis ?? '-' }}
                            </span>
                        </td>

                        <td>{{ $item->keterangan_bayar ?? '-' }}</td>

                        <td>
                            @if (($item->status_pembayaran ?? '') === 'Tunggakan')
                                <span
                                    style="display:inline-flex; padding:5px 10px; border-radius:999px; background:var(--pink-soft); color:var(--pink-dark); font-size:11px; font-weight:950;">
                                    Tunggakan
                                </span>
                            @else
                                <span
                                    style="display:inline-flex; padding:5px 10px; border-radius:999px; background:var(--tosca-soft); color:var(--tosca-dark); font-size:11px; font-weight:950;">
                                    Lancar
                                </span>
                            @endif
                        </td>

                        <td style="text-align:right; font-weight:950; color:var(--tosca-dark); white-space:nowrap;">
                            Rp {{ number_format((int) ($item->nominal ?? 0), 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align:center; padding:35px; color:var(--muted);">
                            Belum ada santri yang membayar sesuai filter ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr>
                    <th colspan="9" style="text-align:right;">Total</th>
                    <th style="text-align:right; color:var(--pink-dark); font-size:17px;">
                        Rp {{ number_format($anakBayar->sum('nominal'), 0, ',', '.') }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
