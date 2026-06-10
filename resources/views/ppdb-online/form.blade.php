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

    <style>
        :root {
            --tosca: #0f9f92;
            --tosca-dark: #0f766e;
            --tosca-soft: #e7faf7;
            --pink: #e3456d;
            --pink-dark: #be185d;
            --pink-soft: #ffe7ee;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --bg: #f4f8fb;
            --white: #ffffff;
            --shadow: 0 20px 50px rgba(15, 23, 42, .10);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(15, 159, 146, .14), transparent 34%),
                radial-gradient(circle at top right, rgba(227, 69, 109, .12), transparent 32%),
                var(--bg);
            color: var(--text);
        }

        body,
        input,
        select,
        textarea,
        button {
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
        }

        .hero {
            min-height: 330px;
            background:
                linear-gradient(135deg, rgba(15, 118, 110, .96), rgba(15, 159, 146, .92)),
                radial-gradient(circle at 15% 20%, rgba(255, 255, 255, .20), transparent 28%),
                radial-gradient(circle at 82% 8%, rgba(255, 255, 255, .16), transparent 22%);
            color: white;
            padding: 34px 18px 96px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-bottom-left-radius: 38px;
            border-bottom-right-radius: 38px;
        }

        .hero::before,
        .hero::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
        }

        .hero::before {
            width: 260px;
            height: 260px;
            left: -95px;
            top: -95px;
        }

        .hero::after {
            width: 230px;
            height: 230px;
            right: -78px;
            bottom: -86px;
        }

        .hero-inner {
            max-width: 960px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .ppdb-logo-wrap {
            width: 88px;
            height: 88px;
            margin: 0 auto 16px;
            border-radius: 28px;
            background: rgba(255, 255, 255, .95);
            border: 1px solid rgba(255, 255, 255, .65);
            box-shadow: 0 18px 38px rgba(0, 0, 0, .17);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }

        .ppdb-logo-wrap img {
            width: 68px;
            height: 68px;
            object-fit: contain;
            display: block;
            background: transparent !important;
        }

        .yayasan-name {
            margin: 0;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: .09em;
            text-transform: uppercase;
            opacity: .96;
        }

        .pesantren-name {
            margin: 6px 0 0;
            font-size: 22px;
            line-height: 1.18;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .02em;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .18);
            border: 1px solid rgba(255, 255, 255, .22);
            font-size: 12px;
            font-weight: 800;
        }

        .hero-title {
            margin: 16px 0 10px;
            font-size: 36px;
            line-height: 1.12;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .hero-subtitle {
            max-width: 760px;
            margin: 0 auto;
            font-size: 14px;
            line-height: 1.8;
            opacity: .94;
            font-weight: 500;
        }

        .form-shell {
            width: min(1040px, calc(100% - 32px));
            margin: -70px auto 32px;
            position: relative;
            z-index: 3;
        }

        .notice {
            border-radius: 22px;
            padding: 15px 17px;
            margin-bottom: 16px;
            font-size: 13px;
            line-height: 1.6;
            font-weight: 700;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
        }

        .notice-danger {
            background: #fff1f4;
            color: #9f1239;
            border: 1px solid #fecdd3;
        }

        .notice-info {
            background: #ecfeff;
            color: #0f766e;
            border: 1px solid rgba(15, 118, 110, .18);
        }

        .form-card {
            background: rgba(255, 255, 255, .96);
            border: 1px solid rgba(226, 232, 240, .92);
            border-radius: 32px;
            padding: 22px;
            box-shadow: var(--shadow);
        }

        .section-card {
            border: 1px solid var(--border);
            background: #ffffff;
            border-radius: 26px;
            padding: 20px;
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
        }

        .section-card::after {
            content: "";
            position: absolute;
            width: 130px;
            height: 130px;
            border-radius: 999px;
            right: -52px;
            top: -60px;
            background: var(--tosca-soft);
            opacity: .70;
            pointer-events: none;
        }

        .section-head {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
            position: relative;
            z-index: 2;
        }

        .section-icon {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--tosca-soft), var(--pink-soft));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            flex: 0 0 auto;
        }

        .section-title {
            margin: 0;
            color: var(--tosca-dark);
            font-size: 17px;
            font-weight: 950;
        }

        .section-desc {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.55;
            font-weight: 600;
        }

        .grid-2,
        .grid-3,
        .upload-grid {
            display: grid;
            gap: 14px;
            position: relative;
            z-index: 2;
        }

        .grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        .upload-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .field {
            display: grid;
            gap: 7px;
        }

        label {
            color: #334155;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .035em;
        }

        .required {
            color: var(--pink);
        }

        input,
        select,
        textarea {
            width: 100%;
            min-height: 48px;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 12px 14px;
            color: var(--text);
            background: #ffffff;
            font-size: 15px;
            font-weight: 600;
            outline: none;
            transition: .15s ease;
        }

        textarea {
            min-height: 112px;
            resize: vertical;
            line-height: 1.6;
        }

        input::placeholder,
        textarea::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--tosca);
            box-shadow: 0 0 0 4px rgba(15, 159, 146, .12);
        }

        .field-hint {
            color: var(--muted);
            font-size: 11px;
            line-height: 1.55;
            font-weight: 600;
        }

        .choice-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .choice-card {
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 14px;
            background: #ffffff;
            cursor: pointer;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .choice-card input {
            width: auto;
            min-height: auto;
            margin-top: 3px;
        }

        .choice-card strong {
            display: block;
            color: var(--text);
            font-size: 14px;
            font-weight: 900;
        }

        .choice-card span {
            display: block;
            margin-top: 3px;
            color: var(--muted);
            font-size: 11px;
            line-height: 1.55;
            font-weight: 600;
        }

        .upload-box {
            min-height: 112px;
            border: 1px dashed #b6c7d3;
            background: #fbfdff;
            border-radius: 20px;
            padding: 14px;
            cursor: pointer;
            display: grid;
            gap: 8px;
            align-content: center;
        }

        .upload-box input {
            min-height: auto;
            padding: 0;
            border: none;
            border-radius: 0;
            background: transparent;
            font-size: 12px;
        }

        .upload-title {
            color: var(--tosca-dark);
            font-size: 13px;
            font-weight: 950;
        }

        .upload-note {
            color: var(--muted);
            font-size: 11px;
            line-height: 1.55;
            font-weight: 600;
        }

        .submit-area {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        .submit-note {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.7;
            font-weight: 600;
            max-width: 640px;
        }

        .submit-btn {
            border: none;
            border-radius: 18px;
            min-height: 54px;
            padding: 0 24px;
            background: linear-gradient(135deg, var(--tosca), var(--pink));
            color: white;
            font-size: 14px;
            font-weight: 950;
            cursor: pointer;
            box-shadow: 0 15px 28px rgba(15, 118, 110, .22);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
        }

        .submit-btn:disabled {
            opacity: .75;
            cursor: wait;
        }

        .footer-note {
            text-align: center;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.7;
            padding: 0 18px 32px;
            font-weight: 600;
        }

        .loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .58);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            padding: 20px;
        }

        .loading-overlay.show {
            display: flex;
        }

        .loading-box {
            width: 360px;
            max-width: 100%;
            border-radius: 28px;
            background: #ffffff;
            padding: 28px;
            text-align: center;
            box-shadow: 0 28px 80px rgba(15, 23, 42, .24);
        }

        .spinner {
            width: 52px;
            height: 52px;
            border-radius: 999px;
            border: 5px solid #dffaf6;
            border-top-color: var(--tosca);
            margin: 0 auto 16px;
            animation: spin .85s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading-box h3 {
            margin: 0 0 8px;
            color: var(--tosca-dark);
            font-size: 20px;
            font-weight: 950;
        }

        .loading-box p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.7;
            font-weight: 600;
        }

        .hidden {
            display: none !important;
        }

        @media (max-width: 900px) {
            .hero {
                min-height: 310px;
                padding: 28px 16px 92px;
                border-bottom-left-radius: 28px;
                border-bottom-right-radius: 28px;
            }

            .ppdb-logo-wrap {
                width: 76px;
                height: 76px;
                border-radius: 24px;
            }

            .ppdb-logo-wrap img {
                width: 58px;
                height: 58px;
            }

            .yayasan-name {
                font-size: 12px;
                letter-spacing: .06em;
            }

            .pesantren-name {
                font-size: 17px;
            }

            .hero-title {
                font-size: 28px;
            }

            .hero-subtitle {
                font-size: 13px;
            }

            .form-shell {
                width: calc(100% - 22px);
                margin-top: -70px;
            }

            .form-card {
                border-radius: 26px;
                padding: 14px;
            }

            .section-card {
                border-radius: 22px;
                padding: 16px;
            }

            .grid-2,
            .grid-3,
            .upload-grid,
            .choice-group {
                grid-template-columns: 1fr;
            }

            input,
            select,
            textarea {
                font-size: 16px;
            }

            .submit-area {
                display: grid;
                grid-template-columns: 1fr;
            }

            .submit-btn {
                width: 100%;
            }
        }

        @media (max-width: 420px) {
            .hero-title {
                font-size: 24px;
            }

            .section-head {
                gap: 10px;
            }

            .section-icon {
                width: 38px;
                height: 38px;
                border-radius: 14px;
            }

            .section-title {
                font-size: 15px;
            }
        }
    </style>
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
                        <select name="jenjang_sekolah" id="jenjangSekolah" required>
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
                        <select name="kelas_diniyah" id="kelasDiniyah">
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

            <div class="submit-area">
                <div class="submit-note">
                    Dengan mengirim formulir ini, wali santri menyatakan bahwa data yang diisi adalah benar dan siap
                    diverifikasi oleh panitia PPDB.
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">
                    <span id="submitIcon">✈️</span>
                    <span id="submitText">Kirim Formulir Pendaftaran</span>
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

    <script>
        const jenjangSekolah = document.getElementById('jenjangSekolah');
        const jurusanWrap = document.getElementById('jurusanWrap');
        const jurusanSelect = document.getElementById('jurusanSelect');
        const kelasDiniyah = document.getElementById('kelasDiniyah');
        const kelasDiniyahWrap = document.getElementById('kelasDiniyahWrap');
        const form = document.getElementById('ppdbOnlineForm');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitIcon = document.getElementById('submitIcon');

        const oldJurusan = @json($oldJurusan);
        const defaultKelasDiniyah = @json($defaultKelasDiniyah);

        const jurusanOptions = {
            SMK: [
                'TKJ',
                'AK',
                'BDP'
            ],
            MA: [
                'IPA',
                'IPS',
                'Keagamaan',
                'Lainnya'
            ]
        };

        function renderJurusan() {
            const jenjang = jenjangSekolah.value;

            jurusanSelect.innerHTML = '<option value="">-- Pilih Jurusan --</option>';

            if (jenjang === 'SMK' || jenjang === 'MA') {
                jurusanWrap.classList.remove('hidden');
                jurusanSelect.required = true;

                jurusanOptions[jenjang].forEach(function(item) {
                    const option = document.createElement('option');
                    option.value = item;
                    option.textContent = item;

                    if (oldJurusan === item) {
                        option.selected = true;
                    }

                    jurusanSelect.appendChild(option);
                });
            } else {
                jurusanWrap.classList.add('hidden');
                jurusanSelect.required = false;
                jurusanSelect.value = '';
            }
        }

        function updateDiniyah() {
            const status = document.querySelector('input[name="status_pondok"]:checked')?.value || 'Mukim';

            if (status === 'Pulang Pergi') {
                kelasDiniyah.value = '';
                kelasDiniyah.disabled = true;
                kelasDiniyah.required = false;
                kelasDiniyahWrap.style.opacity = '.55';
            } else {
                kelasDiniyah.disabled = false;
                kelasDiniyah.required = true;
                kelasDiniyahWrap.style.opacity = '1';

                if (!kelasDiniyah.value) {
                    kelasDiniyah.value = defaultKelasDiniyah;
                }
            }
        }

        if (jenjangSekolah) {
            jenjangSekolah.addEventListener('change', renderJurusan);
            renderJurusan();
        }

        document.querySelectorAll('input[name="status_pondok"]').forEach(function(item) {
            item.addEventListener('change', updateDiniyah);
        });

        updateDiniyah();

        let ppdbIsSubmitting = false;

        if (form) {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    return;
                }

                if (ppdbIsSubmitting) {
                    e.preventDefault();
                    return false;
                }

                ppdbIsSubmitting = true;

                if (loadingOverlay) {
                    loadingOverlay.classList.add('show');
                }

                if (submitBtn) {
                    submitBtn.disabled = true;
                }

                if (submitIcon) {
                    submitIcon.textContent = '⏳';
                }

                if (submitText) {
                    submitText.textContent = 'Mengirim...';
                }
            });
        }
    </script>
</body>

</html>
