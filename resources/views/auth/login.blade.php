<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIKEU MHI V2</title>
    @include('partials.favicon')

    <style>
        :root {
            --tosca: #12a99a;
            --tosca-dark: #08786f;
            --tosca-deep: #075f59;
            --tosca-soft: #e3faf7;

            --pink: #e3456d;
            --pink-dark: #c92d57;
            --pink-soft: #ffe5ec;

            --text: #111827;
            --muted: #6b7280;
            --border: #e5e7eb;
            --white: #ffffff;
            --shadow: 0 24px 70px rgba(15, 23, 42, .14);
            --shadow-soft: 0 14px 36px rgba(15, 23, 42, .08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(18, 169, 154, .25), transparent 34%),
                radial-gradient(circle at bottom right, rgba(227, 69, 109, .22), transparent 32%),
                linear-gradient(135deg, #f7fffd, #fff5f8);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 26px;
            overflow-x: hidden;
        }

        .login-wrapper {
            width: 100%;
            max-width: 1100px;
            min-height: 620px;
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            background: rgba(255, 255, 255, .72);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, .65);
            border-radius: 34px;
            box-shadow: var(--shadow);
            overflow: hidden;
            position: relative;
        }

        .login-wrapper::before {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 999px;
            background: rgba(227, 69, 109, .12);
            right: -80px;
            top: -80px;
        }

        .login-wrapper::after {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 999px;
            background: rgba(18, 169, 154, .12);
            left: -110px;
            bottom: -120px;
        }

        .brand-side {
            position: relative;
            z-index: 1;
            padding: 48px;
            background:
                linear-gradient(135deg, rgba(7, 95, 89, .95), rgba(18, 169, 154, .82)),
                url("{{ asset('images/logo-mhi.png') }}") center 72% / 360px no-repeat;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .brand-side::after {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 999px;
            background: rgba(227, 69, 109, .35);
            right: -90px;
            bottom: -80px;
        }

        .brand-overlay {
            position: absolute;
            inset: 0;
            background: rgba(7, 95, 89, .76);
            z-index: 0;
        }

        .brand-content {
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 42px;
        }

        .brand-logo-box {
            width: 68px;
            height: 68px;
            border-radius: 22px;
            background: rgba(255, 255, 255, .92);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 18px 38px rgba(0, 0, 0, .14);
            padding: 8px;
        }

        .brand-logo-box img {
            width: 54px;
            height: 54px;
            object-fit: contain;
        }

        .brand-logo-text strong {
            display: block;
            font-size: 22px;
            font-weight: 950;
            letter-spacing: .02em;
        }

        .brand-logo-text span {
            display: block;
            font-size: 11px;
            font-weight: 800;
            opacity: .86;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-top: 3px;
        }

        .brand-title {
            max-width: 520px;
        }

        .brand-title h1 {
            font-size: 42px;
            line-height: 1.08;
            margin: 0 0 18px;
            font-weight: 950;
        }

        .brand-title p {
            margin: 0;
            font-size: 16px;
            line-height: 1.75;
            opacity: .92;
        }

        .brand-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 28px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 13px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .18);
            font-size: 12px;
            font-weight: 900;
        }

        .brand-footer {
            position: relative;
            z-index: 1;
            font-size: 12px;
            opacity: .84;
        }

        .form-side {
            position: relative;
            z-index: 1;
            padding: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 30px;
            padding: 34px;
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: "";
            position: absolute;
            height: 7px;
            left: 0;
            right: 0;
            top: 0;
            background: linear-gradient(90deg, var(--tosca), var(--pink));
        }

        .login-header {
            text-align: center;
            margin-bottom: 26px;
        }

        .login-mini-logo {
            width: 76px;
            height: 76px;
            margin: 0 auto 16px;
            border-radius: 26px;
            background: linear-gradient(135deg, var(--tosca-soft), var(--pink-soft));
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            border: 1px solid var(--border);
        }

        .login-mini-logo img {
            width: 58px;
            height: 58px;
            object-fit: contain;
        }

        .login-header h2 {
            margin: 0 0 8px;
            font-size: 28px;
            color: var(--tosca-dark);
            font-weight: 950;
        }

        .login-header p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.5;
        }

        .alert {
            padding: 13px 14px;
            border-radius: 16px;
            font-size: 13px;
            margin-bottom: 18px;
            font-weight: 700;
        }

        .alert-danger {
            background: var(--pink-soft);
            color: var(--pink-dark);
            border: 1px solid #ffc9d7;
        }

        .alert-success {
            background: var(--tosca-soft);
            color: var(--tosca-dark);
            border: 1px solid #b9f2ea;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text);
            font-size: 13px;
            font-weight: 900;
        }

        .input-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid var(--border);
            border-radius: 17px;
            padding: 0 14px;
            background: #fbfffe;
            transition: .2s ease;
        }

        .input-wrap:focus-within {
            border-color: var(--tosca);
            box-shadow: 0 0 0 4px rgba(18, 169, 154, .10);
            background: white;
        }

        .input-icon {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            background: var(--tosca-soft);
            color: var(--tosca-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .input-wrap input {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            padding: 15px 0;
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
        }

        .input-wrap input::placeholder {
            color: #a0aec0;
            font-weight: 600;
        }

        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin: 8px 0 22px;
            font-size: 13px;
            color: var(--muted);
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            user-select: none;
        }

        .remember input {
            width: 16px;
            height: 16px;
            accent-color: var(--tosca);
        }

        .login-button {
            width: 100%;
            border: none;
            border-radius: 18px;
            padding: 15px 18px;
            background: linear-gradient(135deg, var(--tosca), var(--tosca-dark));
            color: white;
            font-size: 15px;
            font-weight: 950;
            cursor: pointer;
            box-shadow: 0 18px 34px rgba(18, 169, 154, .24);
            transition: .2s ease;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 42px rgba(18, 169, 154, .30);
        }

        .login-note {
            margin-top: 22px;
            padding: 14px;
            border-radius: 18px;
            background: linear-gradient(135deg, #fff7fa, #f7fffd);
            border: 1px solid var(--border);
            font-size: 12px;
            color: var(--muted);
            line-height: 1.55;
            text-align: center;
        }

        .login-note strong {
            color: var(--pink-dark);
        }

        @media (max-width: 960px) {
            body {
                padding: 16px;
            }

            .login-wrapper {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .brand-side {
                padding: 34px;
                min-height: 360px;
            }

            .brand-title h1 {
                font-size: 32px;
            }

            .form-side {
                padding: 28px;
            }
        }

        @media (max-width: 520px) {
            .brand-side {
                padding: 26px;
                min-height: 320px;
            }

            .brand-logo {
                margin-bottom: 28px;
            }

            .brand-title h1 {
                font-size: 26px;
            }

            .brand-title p {
                font-size: 14px;
            }

            .form-side {
                padding: 18px;
            }

            .login-card {
                padding: 28px 22px;
                border-radius: 24px;
            }
        }
    </style>
</head>

<body>
    <main class="login-wrapper">
        <section class="brand-side">
            <div class="brand-overlay"></div>

            <div class="brand-content">
                <div class="brand-logo">
                    <div class="brand-logo-box">
                        <img src="{{ asset('images/logo-mhi.png') }}" alt="Logo MHI">
                    </div>

                    <div class="brand-logo-text">
                        <strong>SIKEU MHI</strong>
                        <span>Sistem Keuangan Terpadu</span>
                    </div>
                </div>

                <div class="brand-title">
                    <h1>Yayasan Pendidikan Pesantren Mamba'ul Khoiriyatil Islamiyah</h1>
                    <p>
                        Kelola pembayaran santri, laporan pemasukan, tunggakan,
                        dan administrasi keuangan pesantren dalam satu sistem yang rapi.
                    </p>
                </div>

                <div class="brand-badges">
                    <span class="brand-badge">💳 Pembayaran</span>
                    <span class="brand-badge">📊 Laporan</span>
                    <span class="brand-badge">🔎 Tunggakan</span>
                    <span class="brand-badge">🕌 Pondok</span>
                </div>
            </div>

            <div class="brand-footer">
                © {{ date('Y') }} SIKEU MHI V2 — Sistem Informasi Keuangan Terpadu
            </div>
        </section>

        <section class="form-side">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-mini-logo">
                        <img src="{{ asset('images/logo-mhi.png') }}" alt="Logo">
                    </div>

                    <h2>Login Admin</h2>
                    <p>Masukkan akun admin untuk mengakses dashboard keuangan.</p>
                </div>

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ Route::has('login.process') ? route('login.process') : url('/login') }}"
                    method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-wrap">
                            <div class="input-icon">👤</div>
                            <input type="text" id="username" name="username" value="{{ old('username') }}"
                                placeholder="Masukkan username admin" autocomplete="username" autofocus required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <div class="input-icon">🔒</div>
                            <input type="password" id="password" name="password" placeholder="Masukkan password"
                                autocomplete="current-password" required>
                        </div>
                    </div>

                    <div class="login-options">
                        <label class="remember">
                            <input type="checkbox" name="remember" value="1">
                            <span>Ingat saya</span>
                        </label>

                        <span>SIKEU MHI V2</span>
                    </div>

                    <button type="submit" class="login-button">
                        Masuk Dashboard
                    </button>
                </form>

                <div class="login-note">
                    <strong>Keamanan:</strong>
                    Pastikan akun hanya digunakan oleh admin atau petugas keuangan yang berwenang.
                </div>
            </div>
        </section>
    </main>
</body>

</html>
