@php
    $isEdit = ($mode ?? 'create') === 'edit';
    $siswa = $siswa ?? null;
    $value = function ($field, $default = '') use ($siswa) {
        return old($field, $siswa->{$field} ?? $default);
    };
@endphp

<div class="siswa-card" style="box-shadow:none;padding:0;border:none;margin-bottom:20px;">
    <h3 class="siswa-section-title">A. Identitas Santri</h3>

    <div class="siswa-form-grid">
        <div class="siswa-field">
            <label>NIS</label>
            <input type="text" class="siswa-control" value="{{ $nisOtomatis ?? '-' }}" readonly>
            <small
                class="siswa-note">{{ $isEdit ? 'NIS tidak dapat diedit.' : 'NIS otomatis dari NIS terakhir + 1 saat data disimpan.' }}</small>
        </div>

        <div class="siswa-field">
            <label>NISN</label>
            <input type="text" name="nisn" class="siswa-control" value="{{ $value('nisn') }}"
                placeholder="Boleh dikosongkan">
        </div>
    </div>

    <div class="siswa-field">
        <label>Nama Santri *</label>
        <input type="text" name="nama_siswa" class="siswa-control" value="{{ $value('nama_siswa') }}" required>
    </div>

    <div class="siswa-form-grid-3">
        <div class="siswa-field">
            <label>Jenis Kelamin</label>
            <select name="jk" class="siswa-control">
                <option value="">-- Pilih --</option>
                <option value="L" {{ $value('jk') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ $value('jk') == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </div>

        <div class="siswa-field">
            <label>Tempat Lahir</label>
            <input type="text" name="tempat_lahir" class="siswa-control" value="{{ $value('tempat_lahir') }}">
        </div>

        <div class="siswa-field">
            <label>Tanggal Lahir</label>
            <input type="date" name="tgl_lahir" class="siswa-control" value="{{ $value('tgl_lahir') }}">
        </div>
    </div>

    <div class="siswa-field">
        <label>Alamat</label>
        <textarea name="alamat" class="siswa-control" placeholder="Alamat lengkap santri">{{ $value('alamat') }}</textarea>
    </div>

    <div class="siswa-field">
        <label>Asal Sekolah</label>
        <input type="text" name="asal_sekolah" class="siswa-control" value="{{ $value('asal_sekolah') }}">
    </div>
</div>

<div class="siswa-card" style="box-shadow:none;padding:0;border:none;margin-bottom:20px;">
    <h3 class="siswa-section-title">B. Kelas & Status</h3>

    <div class="siswa-form-grid">
        <div class="siswa-field">
            <label>Kelas Formal</label>
            <select name="kelas_formal" class="siswa-control">
                <option value="">-- Pilih Kelas Formal --</option>
                @foreach ($kelasFormal ?? collect() as $kelas)
                    @php
                        $nama =
                            $kelas->nama_kelas ??
                            ($kelas->nama_kelas_formal ??
                                ($kelas->kelas_formal ?? ($kelas->nama ?? ($kelas->kelas ?? '-'))));
                    @endphp
                    <option value="{{ $nama }}" {{ $value('kelas_formal') == $nama ? 'selected' : '' }}>
                        {{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="siswa-field">
            <label>Kelas Diniyah</label>
            <select name="kelas_diniyah" class="siswa-control">
                <option value="">-- Pilih Kelas Diniyah --</option>
                @foreach ($kelasDiniyah ?? collect() as $kelas)
                    @php
                        $nama =
                            $kelas->nama_kelas ??
                            ($kelas->nama_kelas_diniyah ??
                                ($kelas->kelas_diniyah ?? ($kelas->nama ?? ($kelas->kelas ?? '-'))));
                    @endphp
                    <option value="{{ $nama }}" {{ $value('kelas_diniyah') == $nama ? 'selected' : '' }}>
                        {{ $nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="siswa-form-grid-3">
        <div class="siswa-field">
            <label>Status Mukim</label>
            <select name="status_mukim" class="siswa-control">
                <option value="">-- Pilih --</option>
                @foreach (['Mukim', 'Pulang Pergi', 'Asrama', 'Non Mukim'] as $status)
                    <option value="{{ $status }}" {{ $value('status_mukim') == $status ? 'selected' : '' }}>
                        {{ $status }}</option>
                @endforeach
            </select>
        </div>

        <div class="siswa-field">
            <label>Tahun Ajaran</label>
            <input type="text" name="tahun_ajaran" class="siswa-control"
                value="{{ $value('tahun_ajaran', '2026/2027') }}">
        </div>

        <div class="siswa-field">
            <label>Status Aktif</label>
            <select name="status_aktif" class="siswa-control">
                @foreach (['Aktif', 'Nonaktif', 'Alumni'] as $status)
                    <option value="{{ $status }}"
                        {{ $value('status_aktif', 'Aktif') == $status ? 'selected' : '' }}>{{ $status }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="siswa-card" style="box-shadow:none;padding:0;border:none;margin-bottom:20px;">
    <h3 class="siswa-section-title">C. Wali & Pembayaran</h3>

    <div class="siswa-form-grid">
        <div class="siswa-field">
            <label>Nama Wali / Ayah</label>
            <input type="text" name="nama_wali" class="siswa-control" value="{{ $value('nama_wali') }}">
        </div>

        <div class="siswa-field">
            <label>Nama Ibu</label>
            <input type="text" name="nama_ibu" class="siswa-control" value="{{ $value('nama_ibu') }}">
        </div>
    </div>

    <div class="siswa-form-grid-3">
        <div class="siswa-field">
            <label>No HP / WhatsApp</label>
            <input type="text" name="no_hp" class="siswa-control" value="{{ $value('no_hp') }}"
                placeholder="08123456789">
        </div>

        <div class="siswa-field">
            <label>Nominal SPP Formal Khusus</label>
            <input type="number" name="potongan_formal" class="siswa-control"
                value="{{ $value('potongan_formal', 0) }}" min="0" placeholder="Contoh: 120000">
            <small class="siswa-note">
                Isi nominal bayar formal bulanan khusus santri ini. Isi 0 jika ikut nominal default kelas.
            </small>
        </div>

        <div class="siswa-field">
            <label>Nominal SPP Pondok/Diniyah Khusus</label>
            <input type="number" name="potongan_diniyah" class="siswa-control"
                value="{{ $value('potongan_diniyah', 0) }}" min="0" placeholder="Contoh: 65000">
            <small class="siswa-note">
                Isi nominal bayar pondok/diniyah bulanan khusus santri ini. Isi 0 jika ikut nominal default kelas.
            </small>
        </div>
    </div>
</div>

<div class="siswa-card" style="box-shadow:none;padding:0;border:none;margin-bottom:0;">
    <h3 class="siswa-section-title">D. Foto Santri</h3>

    <div class="siswa-field">
        <label>Upload Foto</label>
        <input type="file" name="foto" class="siswa-control" accept="image/*">
        <small class="siswa-note">Format: JPG, PNG, WEBP. Maksimal 2 MB.</small>

        @if ($isEdit && !empty($siswa->foto))
            <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto Santri" class="siswa-photo-preview">
        @endif
    </div>
</div>
