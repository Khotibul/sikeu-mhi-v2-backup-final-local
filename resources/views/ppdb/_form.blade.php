@php
    $item = $item ?? null;
    $statusOptions = $statusOptions ?? ['Pending', 'Diterima', 'Ditolak'];

    $val = function ($field, $default = '') use ($item) {
        return old($field, $item->{$field} ?? $default);
    };

    $dateVal = function ($field, $default = '') use ($item) {
        $value = old($field, $item->{$field} ?? $default);
        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '';
    };
@endphp

<h4 class="section-title">Identitas Calon Santri</h4>

<div class="grid3">
    <div class="field">
        <label>No Daftar</label>
        <input type="text" name="no_daftar" class="control" value="{{ $val('no_daftar') }}"
            placeholder="Kosongkan jika otomatis">
    </div>

    <div class="field">
        <label>Tanggal Daftar</label>
        <input type="date" name="tgl_daftar" class="control" value="{{ $dateVal('tgl_daftar', date('Y-m-d')) }}">
    </div>

    <div class="field">
        <label>Tahun Ajaran</label>
        <input type="text" name="tahun_ajaran" class="control" value="{{ $val('tahun_ajaran', '2025/2026') }}"
            required>
    </div>
</div>

<div class="grid2">
    <div class="field">
        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="control" value="{{ $val('nama_lengkap') }}" required>
    </div>

    <div class="field">
        <label>NISN</label>
        <input type="text" name="nisn" class="control" value="{{ $val('nisn') }}">
    </div>
</div>

<div class="grid3">
    <div class="field">
        <label>Jenis Kelamin</label>
        <select name="jk" class="control" required>
            <option value="L" {{ $val('jk') == 'L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ $val('jk') == 'P' ? 'selected' : '' }}>Perempuan</option>
        </select>
    </div>

    <div class="field">
        <label>Tempat Lahir</label>
        <input type="text" name="tempat_lahir" class="control" value="{{ $val('tempat_lahir') }}" required>
    </div>

    <div class="field">
        <label>Tanggal Lahir</label>
        <input type="date" name="tgl_lahir" class="control" value="{{ $dateVal('tgl_lahir') }}" required>
    </div>
</div>

<div class="field">
    <label>Alamat</label>
    <textarea name="alamat" class="control" required>{{ $val('alamat') }}</textarea>
</div>

<h4 class="section-title">Data Pendidikan</h4>

<div class="grid3">
    <div class="field">
        <label>Asal Sekolah</label>
        <input type="text" name="asal_sekolah" class="control" value="{{ $val('asal_sekolah') }}">
    </div>

    <div class="field">
        <label>Jenjang Sekolah</label>
        <select name="jenjang_sekolah" class="control" required>
            @foreach (['SMP', 'MTS', 'SMK', 'MA', 'Non Formal'] as $jenjang)
                <option value="{{ $jenjang }}" {{ $val('jenjang_sekolah') == $jenjang ? 'selected' : '' }}>
                    {{ $jenjang }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label>Jurusan</label>
        <input type="text" name="jurusan" class="control" value="{{ $val('jurusan') }}"
            placeholder="Contoh: TKJ / IPA / IPS">
    </div>
</div>

<div class="grid2">
    <div class="field">
        <label>Kelas Diniyah</label>
        <input type="text" name="kelas_diniyah" class="control" value="{{ $val('kelas_diniyah') }}">
    </div>

    <div class="field">
        <label>Status Pondok</label>
        <select name="status_pondok" class="control" required>
            <option value="Ya" {{ $val('status_pondok', 'Ya') == 'Ya' ? 'selected' : '' }}>Ya</option>
            <option value="Tidak" {{ $val('status_pondok') == 'Tidak' ? 'selected' : '' }}>Tidak</option>
        </select>
    </div>
</div>

<h4 class="section-title">Data Orang Tua</h4>

<div class="grid3">
    <div class="field">
        <label>Nama Ayah</label>
        <input type="text" name="nama_ayah" class="control" value="{{ $val('nama_ayah') }}" required>
    </div>

    <div class="field">
        <label>Nama Ibu</label>
        <input type="text" name="nama_ibu" class="control" value="{{ $val('nama_ibu') }}">
    </div>

    <div class="field">
        <label>No HP Orang Tua</label>
        <input type="text" name="no_hp_ortu" class="control" value="{{ $val('no_hp_ortu') }}" required>
    </div>
</div>

<h4 class="section-title">Status Seleksi</h4>

<div class="grid2">
    <div class="field">
        <label>Status Seleksi</label>
        <select name="status_seleksi" class="control">
            @foreach ($statusOptions as $status)
                <option value="{{ $status }}"
                    {{ $val('status_seleksi', 'Pending') == $status ? 'selected' : '' }}>
                    {{ $status }}
                </option>
            @endforeach
        </select>
    </div>
</div>
