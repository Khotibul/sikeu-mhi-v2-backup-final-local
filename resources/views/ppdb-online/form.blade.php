<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir PPDB Online - YPP MHI</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    @vite(['resources/css/ppdb-online/form.css', 'resources/js/ppdb-online/form.js'])
</head>

<body>
    @php
        $tahunAjaran = $tahunAjaran ?? old('tahun_ajaran', '');
        $oldJenjang = old('jenjang_sekolah');
        $oldStatusPondok = old('status_pondok', 'Mukim');
        $oldJurusan = old('jurusan');

        $kelasDiniyahList = collect($kelasDiniyahList ?? ['IBTIDAIYAH INDUK PA'])
            ->filter()
            ->values();

        $defaultKelasDiniyah = $kelasDiniyahList->first() ?: 'IBTIDAIYAH INDUK PA';
        $oldDiniyah = old('kelas_diniyah', $defaultKelasDiniyah);
        $verifiedEmail = $verifiedEmail ?? null;
        $isEmailVerified = !empty($verifiedEmail);
        $verificationEmail = old('email', session('verification_email', $verifiedEmail));
    @endphp

    <div class="hero">
        <div class="hero-inner">
            <div class="ppdb-logo-wrap">
                <img src="{{ asset('images/logo-mhi.png') }}" alt="Logo YPP MHI">
            </div>

            <p class="yayasan-name">Yayasan Pendidikan Pesantren</p>
            <h1 class="pesantren-name">Mamba'ul Khoiriyatil Islamiyah</h1>

            <div class="hero-badge">
                📝 PPDB Online Tahun Ajaran {{ $tahunAjaran ?: 'Baru' }}
            </div>

            <h2 class="hero-title">Formulir Pendaftaran Santri Baru</h2>

            <p class="hero-subtitle">
                Silakan isi data calon santri dengan benar. Data yang dikirim akan masuk ke sistem PPDB
                dan diverifikasi oleh panitia penerimaan santri baru.
            </p>
        </div>
    </div>

    <main class="form-shell">
        @if (session('error'))
            <div class="notice notice-danger">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="notice notice-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="notice notice-danger">
                <strong>Mohon periksa kembali data berikut:</strong>
                <ul style="margin:8px 0 0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="verification-card {{ $isEmailVerified ? 'verified' : '' }}">
            <div class="section-head">
                <div class="section-icon">@</div>
                <div>
                    <h3 class="section-title">Verifikasi Email</h3>
                    <p class="section-desc">
                        Formulir PPDB akan terbuka setelah email wali santri berhasil diverifikasi.
                    </p>
                </div>
            </div>

            @if ($isEmailVerified)
                <div class="notice notice-success" style="margin:0;">
                    Email <strong>{{ $verifiedEmail }}</strong> sudah terverifikasi. Silakan lanjut mengisi formulir.
                </div>
            @else
                <form action="{{ route('ppdb-online.email-verification.send') }}" method="POST">
                    @csrf
                    <div class="verification-actions">
                        <div class="field">
                            <label>Email Wali / Pendaftar <span class="required">*</span></label>
                            <input type="email" name="email" value="{{ $verificationEmail }}"
                                placeholder="nama@email.com" required>
                            <div class="field-hint">
                                Pastikan email aktif karena kode verifikasi akan dikirim ke alamat ini.
                            </div>
                        </div>

                        <button type="submit" class="verify-btn">
                            Kirim Kode
                        </button>
                    </div>
                </form>

                <form action="{{ route('ppdb-online.email-verification.confirm') }}" method="POST">
                    @csrf
                    <div class="verification-code-row">
                        <input type="hidden" name="email" value="{{ $verificationEmail }}">

                        <div class="field">
                            <label>Kode Verifikasi</label>
                            <input type="text" name="verification_code" inputmode="numeric" pattern="[0-9]{6}"
                                maxlength="6" placeholder="6 digit kode email">
                        </div>

                        <button type="submit" class="verify-btn secondary">
                            Verifikasi
                        </button>
                    </div>
                </form>
            @endif
        </section>

        <div class="notice notice-info">
            Berkas foto JPG/PNG/WEBP boleh berukuran besar dan akan dikompres otomatis maksimal sekitar 2 MB.
            Untuk PDF, ukuran maksimal tetap 2 MB.
        </div>

        <form action="{{ route('ppdb-online.submit') }}" method="POST" enctype="multipart/form-data" class="form-card"
            id="ppdbOnlineForm">
            @csrf

            @php
                $ppdbSubmitToken = old('ppdb_submit_token') ?: (session()->token() . '-' . \Illuminate\Support\Str::uuid());
            @endphp
            <input type="hidden" name="ppdb_submit_token" value="{{ $ppdbSubmitToken }}">

            <input type="hidden" name="tahun_ajaran" value="{{ old('tahun_ajaran', $tahunAjaran) }}">
            <input type="hidden" name="verified_email" value="{{ $verifiedEmail }}">

            @unless ($isEmailVerified)
                <div class="lock-message">
                    Formulir masih terkunci. Masukkan email aktif, kirim kode, lalu verifikasi email terlebih dahulu.
                </div>
            @endunless

            <fieldset class="{{ $isEmailVerified ? '' : 'form-locked' }}" {{ $isEmailVerified ? '' : 'disabled' }}>

            <section class="section-card">
                <div class="section-head">
                    <div class="section-icon">👤</div>
                    <div>
                        <h3 class="section-title">Data Calon Santri</h3>
                        <p class="section-desc">Isi identitas calon santri sesuai dokumen resmi.</p>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="field">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                            placeholder="Nama lengkap calon santri" required>
                    </div>

                    <div class="field">
                        <label>NISN</label>
                        <input type="text" name="nisn" value="{{ old('nisn') }}"
                            placeholder="NISN jika sudah ada">
                    </div>

                    <div class="field">
                        <label>Jenis Kelamin <span class="required">*</span></label>
                        <select name="jk" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jk') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jk') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>Asal Sekolah</label>
                        <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah') }}"
                            placeholder="Contoh: MI MHI / SDN ...">
                    </div>

                    <div class="field">
                        <label>Tempat Lahir <span class="required">*</span></label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                            placeholder="Tempat lahir" required>
                    </div>

                    <div class="field">
                        <label>Tanggal Lahir <span class="required">*</span></label>
                        <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir') }}" required>
                    </div>
                </div>

                <div class="field" style="margin-top:14px;">
                    <label>Alamat Lengkap <span class="required">*</span></label>
                    <textarea name="alamat" placeholder="Dusun, RT/RW, Desa, Kecamatan, Kabupaten..." required>{{ old('alamat') }}</textarea>
                </div>
            </section>

            <section class="section-card">
                <div class="section-head">
                    <div class="section-icon">👨‍👩‍👧</div>
                    <div>
                        <h3 class="section-title">Data Orang Tua / Wali</h3>
                        <p class="section-desc">Nomor HP digunakan panitia untuk konfirmasi pendaftaran.</p>
                    </div>
                </div>

                <div class="grid-3">
                    <div class="field">
                        <label>Nama Ayah <span class="required">*</span></label>
                        <input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}"
                            placeholder="Nama ayah/wali" required>
                    </div>

                    <div class="field">
                        <label>Nama Ibu</label>
                        <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}" placeholder="Nama ibu">
                    </div>

                    <div class="field">
                        <label>No. HP Orang Tua <span class="required">*</span></label>
                        <input type="text" name="no_hp_ortu" value="{{ old('no_hp_ortu') }}"
                            placeholder="08xxxxxxxxxx" required>
                    </div>
                </div>
            </section>

            <section class="section-card">
                <div class="section-head">
                    <div class="section-icon">🏫</div>
                    <div>
                        <h3 class="section-title">Pilihan Pendidikan</h3>
                        <p class="section-desc">Jurusan hanya muncul untuk pilihan SMK atau MA.</p>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="field">
                        <label>Jenjang Sekolah <span class="required">*</span></label>
                        <select name="jenjang_sekolah" id="jenjangSekolah" required data-old-jurusan="{{ $oldJenjang }}">
                            <option value="">-- Pilih Jenjang --</option>
                            <option value="MTS" {{ $oldJenjang == 'MTS' ? 'selected' : '' }}>MTS</option>
                            <option value="SMP" {{ $oldJenjang == 'SMP' ? 'selected' : '' }}>SMP</option>
                            <option value="SPM ULYA" {{ $oldJenjang == 'SPM ULYA' ? 'selected' : '' }}>SPM ULYA
                            </option>
                            <option value="MA" {{ $oldJenjang == 'MA' ? 'selected' : '' }}>MA</option>
                            <option value="SMK" {{ $oldJenjang == 'SMK' ? 'selected' : '' }}>SMK</option>
                        </select>
                        <div class="field-hint">Saat diterima jadi santri, kelas formal otomatis mengikuti jenjang.
                        </div>
                    </div>

                    <div class="field {{ in_array($oldJenjang, ['SMK', 'MA']) ? '' : 'hidden' }}" id="jurusanWrap">
                        <label>Jurusan <span class="required">*</span></label>
                        <select name="jurusan" id="jurusanSelect">
                            <option value="">-- Pilih Jurusan --</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top:14px;">
                    <label>Status Pondok <span class="required">*</span></label>
                    <div class="choice-group" style="margin-top:8px;">
                        <label class="choice-card">
                            <input type="radio" name="status_pondok" value="Mukim"
                                {{ $oldStatusPondok == 'Mukim' ? 'checked' : '' }}>
                            <span>
                                <strong>Mukim</strong>
                                <span>Santri tinggal di pondok. Kelas diniyah dapat dipilih.</span>
                            </span>
                        </label>

                        <label class="choice-card">
                            <input type="radio" name="status_pondok" value="Pulang Pergi"
                                {{ $oldStatusPondok == 'Pulang Pergi' ? 'checked' : '' }}>
                            <span>
                                <strong>Pulang Pergi</strong>
                                <span>Santri tidak mukim. Kelas diniyah otomatis tidak dipilih.</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="grid-2" style="margin-top:14px;">
                    <div class="field" id="kelasDiniyahWrap">
                        <label>Kelas Diniyah</label>
                        <select name="kelas_diniyah" id="kelasDiniyah" data-default-kelas="{{ $defaultKelasDiniyah }}">
                            <option value="">-- Pilih Kelas Diniyah --</option>

                            @foreach ($kelasDiniyahList as $namaKelasDiniyah)
                                <option value="{{ $namaKelasDiniyah }}"
                                    {{ $oldDiniyah == $namaKelasDiniyah ? 'selected' : '' }}>
                                    {{ $namaKelasDiniyah }}
                                </option>
                            @endforeach
                        </select>
                        <div class="field-hint">Kelas diniyah mengikuti data kelas diniyah di sistem.</div>
                    </div>
                </div>
            </section>

            <section class="section-card">
                <div class="section-head">
                    <div class="section-icon">📎</div>
                    <div>
                        <h3 class="section-title">Upload Berkas</h3>
                        <p class="section-desc">Berkas dapat difoto langsung dari HP. File gambar akan dikompres
                            otomatis oleh sistem.</p>
                    </div>
                </div>

                <div class="upload-grid">
                    <label class="upload-box">
                        <span class="upload-title">Kartu Keluarga</span>
                        <span class="upload-note">JPG/PNG/WEBP/PDF</span>
                        <input type="file" name="file_kk"
                            accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf">
                    </label>

                    <label class="upload-box">
                        <span class="upload-title">KTP Orang Tua</span>
                        <span class="upload-note">JPG/PNG/WEBP/PDF</span>
                        <input type="file" name="file_ktp"
                            accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf">
                    </label>

                    <label class="upload-box">
                        <span class="upload-title">Foto Santri</span>
                        <span class="upload-note">Bisa langsung buka kamera HP</span>
                        <input type="file" name="file_foto" accept="image/*" capture="environment">
                    </label>

                    <label class="upload-box">
                        <span class="upload-title">Ijazah / SKL</span>
                        <span class="upload-note">JPG/PNG/WEBP/PDF</span>
                        <input type="file" name="file_ijazah"
                            accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf">
                    </label>
                </div>
            </section>

            </fieldset>

            <div class="submit-area">
                <div class="submit-note">
                    @if ($isEmailVerified)
                        Dengan mengirim formulir ini, wali santri menyatakan bahwa data yang diisi adalah benar dan siap
                        diverifikasi oleh panitia PPDB.
                    @else
                        Tombol kirim akan aktif setelah email berhasil diverifikasi.
                    @endif
                </div>

                <button type="submit" class="submit-btn" id="submitBtn" {{ $isEmailVerified ? '' : 'disabled' }}>
                    <span id="submitIcon">✈️</span>
                    <span id="submitText">{{ $isEmailVerified ? 'Kirim Formulir Pendaftaran' : 'Verifikasi Email Dulu' }}</span>
                </button>
            </div>
        </form>
    </main>

    <div class="footer-note">
        © {{ date('Y') }} Yayasan Pendidikan Pesantren Mamba'ul Khoiriyatil Islamiyah.
        Formulir online PPDB resmi.
    </div>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-box">
            <div class="spinner"></div>
            <h3>Mengirim Formulir</h3>
            <p>
                Mohon tunggu sebentar. Data dan berkas sedang diproses.
                Jangan menutup halaman ini sampai proses selesai.
            </p>
        </div>
    </div>
</body>

</html>

