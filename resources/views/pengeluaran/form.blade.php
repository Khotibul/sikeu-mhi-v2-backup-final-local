@php
    $pengeluaranToken = old('_pengeluaran_token', (string) \Illuminate\Support\Str::uuid());
@endphp

<input type="hidden" name="_pengeluaran_token" value="{{ $pengeluaranToken }}">

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Periksa kembali data:</strong>
        <ul style="margin:8px 0 0; padding-left:20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-grid">
    <div class="form-group">
        <label>Tanggal Keluar</label>
        <input type="date" name="tgl_keluar" class="form-control"
            value="{{ old('tgl_keluar', $pengeluaran->tgl_keluar ?? date('Y-m-d')) }}" required>
    </div>

    <div class="form-group">
        <label>Penerima</label>
        <input type="text" name="penerima" class="form-control"
            value="{{ old('penerima', $pengeluaran->penerima ?? '') }}" placeholder="Contoh: Ahmad / Toko ATK / Tukang"
            required>
    </div>

    <div class="form-group">
        <label>Unit</label>
        <input type="text" name="unit" class="form-control" value="{{ old('unit', $pengeluaran->unit ?? '') }}"
            placeholder="Contoh: Yayasan, Dapur, Kantor, Bangunan" required>
    </div>

    <div class="form-group">
        <label>Jumlah</label>
        <input type="number" name="jumlah" class="form-control"
            value="{{ old('jumlah', $pengeluaran->jumlah ?? '') }}" placeholder="0" required>
    </div>

    <div class="form-group" style="grid-column:1 / -1;">
        <label>Uraian</label>
        <textarea name="uraian" class="form-control" rows="4" placeholder="Tuliskan uraian pengeluaran..." required>{{ old('uraian', $pengeluaran->uraian ?? '') }}</textarea>
    </div>

    <div class="form-group" style="grid-column:1 / -1;">
        <label>Bukti Foto</label>
        <input type="file" name="bukti_foto" class="form-control" accept="image/*">

        @if (!empty($pengeluaran->bukti_foto))
            <div style="margin-top:10px;">
                <a href="{{ asset('uploads/pengeluaran/' . $pengeluaran->bukti_foto) }}" target="_blank"
                    class="btn btn-light">
                    Lihat Bukti Lama
                </a>
            </div>
        @endif
    </div>
</div>

<div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
    <a href="{{ route('pengeluaran.index') }}" class="btn btn-light">
        Kembali
    </a>

    <button type="submit" class="btn btn-primary">
        {{ $button }}
    </button>
</div>
@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.js-pengeluaran-submit').forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.dataset.submitting === '1') {
                            event.preventDefault();
                            return false;
                        }

                        form.dataset.submitting = '1';

                        form.querySelectorAll('button[type="submit"]').forEach(function(button) {
                            button.disabled = true;
                            button.dataset.originalText = button.innerHTML;
                            button.innerHTML = '⏳ Menyimpan...';
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce
