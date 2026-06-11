<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIKEU MHI V2')</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --tosca: #12a99a;
            --tosca-dark: #0f766e;
            --tosca-soft: #dffaf6;
            --pink: #e3456d;
            --pink-dark: #be185d;
            --pink-soft: #ffe4ec;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --body: #f4f8fb;
            --shadow-soft: 0 18px 45px rgba(15, 23, 42, .08);
            --sidebar-width: 292px;
        }

        * {
            box-sizing: border-box;
        }

        body,
        button,
        input,
        select,
        textarea,
        table,
        a {
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
            letter-spacing: -0.025em;
        }

        p,
        span,
        label,
        small,
        td,
        th,
        div {
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(18, 169, 154, .13), transparent 30%),
                radial-gradient(circle at top right, rgba(227, 69, 109, .10), transparent 28%),
                var(--body);
            color: var(--text);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }

        .app-shell {
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(18px);
            border-right: 1px solid rgba(226, 232, 240, .9);
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            box-shadow: 14px 0 34px rgba(15, 23, 42, .07);
        }

        .sidebar-brand {
            padding: 18px 16px 14px;
            border-bottom: 1px solid var(--border);
        }

        .brand-box {
            display: grid;
            grid-template-columns: 56px 1fr;
            gap: 12px;
            align-items: center;
        }

        .brand-logo {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--tosca-soft), var(--pink-soft));
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid rgba(18, 169, 154, .18);
        }

        .brand-logo img {
            width: 46px;
            height: 46px;
            object-fit: contain;
        }

        .brand-title {
            margin: 0;
            font-size: 16px;
            line-height: 1.15;
            color: var(--tosca-dark);
            font-weight: 950;
            text-transform: uppercase;
        }

        .brand-subtitle {
            margin: 4px 0 0;
            font-size: 10.5px;
            line-height: 1.4;
            color: var(--muted);
            font-weight: 700;
        }

        .sidebar-user {
            margin-top: 14px;
            padding: 12px;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(18, 169, 154, .13), rgba(255, 255, 255, .9));
            border: 1px solid rgba(18, 169, 154, .16);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--tosca), var(--pink));
            color: #ffffff;
            font-weight: 950;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .user-info {
            min-width: 0;
        }

        .user-info strong {
            display: block;
            font-size: 13px;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-info span {
            display: block;
            margin-top: 2px;
            font-size: 11px;
            color: var(--muted);
            font-weight: 700;
        }

        .sidebar-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 14px 13px 18px;
        }

        .nav-section {
            margin-bottom: 16px;
        }

        .nav-section-title {
            padding: 10px 10px 8px;
            font-size: 10.5px;
            font-weight: 950;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .09em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-section-title::after {
            content: "";
            height: 1px;
            flex: 1;
            background: var(--border);
        }

        .nav-list {
            display: grid;
            gap: 6px;
        }

        .nav-link {
            min-height: 45px;
            padding: 10px 11px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #334155;
            font-size: 13.5px;
            font-weight: 850;
            transition: .18s ease;
            border: 1px solid transparent;
            position: relative;
        }

        .nav-link:hover {
            background: #ffffff;
            border-color: var(--border);
            transform: translateX(2px);
            box-shadow: 0 10px 20px rgba(15, 23, 42, .04);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--tosca), var(--pink));
            color: #ffffff !important;
            box-shadow: 0 14px 28px rgba(18, 169, 154, .23);
        }

        .nav-icon {
            width: 32px;
            height: 32px;
            border-radius: 12px;
            background: var(--tosca-soft);
            color: var(--tosca-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex: 0 0 auto;
        }

        .nav-link.active .nav-icon {
            background: rgba(255, 255, 255, .22);
            color: #ffffff;
        }

        .nav-text {
            flex: 1;
            min-width: 0;
        }

        .nav-mini {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 22px;
            border-radius: 999px;
            background: var(--pink-soft);
            color: var(--pink-dark);
            font-size: 10px;
            font-weight: 950;
            padding: 0 7px;
        }

        .nav-disabled {
            opacity: .86;
            cursor: not-allowed;
        }

        .sidebar-footer {
            padding: 14px;
            border-top: 1px solid var(--border);
            background: rgba(255, 255, 255, .90);
        }

        .logout-form {
            margin: 0;
        }

        .logout-btn {
            width: 100%;
            border: none;
            border-radius: 16px;
            padding: 12px 14px;
            background: linear-gradient(135deg, #ef476f, #e11d48);
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
        }

        .main {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            padding: 24px;
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .topbar {
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(226, 232, 240, .9);
            border-radius: 28px;
            padding: 20px 22px;
            box-shadow: var(--shadow-soft);
            margin-bottom: 22px;
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            position: relative;
            overflow: hidden;
            z-index: 5;
        }

        .topbar::after {
            content: "";
            position: absolute;
            width: 160px;
            height: 160px;
            border-radius: 999px;
            background: var(--tosca-soft);
            right: -55px;
            top: -75px;
            opacity: .85;
        }

        .main-header {
            position: relative;
            z-index: 1;
        }

        .main-header h1 {
            margin: 0;
            font-size: 26px;
            color: var(--tosca-dark);
            font-weight: 950;
            letter-spacing: -.02em;
        }

        .main-header p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .topbar-actions {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .date-pill {
            padding: 10px 13px;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid var(--border);
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .mobile-menu-btn {
            display: none;
            border: none;
            border-radius: 14px;
            padding: 11px 13px;
            background: linear-gradient(135deg, var(--tosca), #087c73);
            color: #ffffff;
            font-size: 18px;
            cursor: pointer;
            font-weight: 900;
        }

        .content-wrap {
            width: 100%;
            position: relative;
            z-index: 2;
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .btn {
            border: none;
            border-radius: 14px;
            padding: 11px 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 900;
            cursor: pointer;
            line-height: 1;
            transition: .16s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--tosca), #087c73);
            color: #ffffff !important;
            box-shadow: 0 10px 20px rgba(15, 118, 110, .14);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef476f, #e11d48);
            color: #ffffff !important;
            box-shadow: 0 10px 20px rgba(225, 29, 72, .14);
        }

        .btn-light {
            background: #f1f5f9;
            color: #334155 !important;
            border: 1px solid var(--border);
        }

        .content-wrap a.btn-primary,
        .content-wrap a.btn-danger,
        .content-wrap a.icon-print,
        .content-wrap a.logout-btn {
            color: #ffffff !important;
        }

        .content-wrap a.btn-light {
            color: #334155 !important;
        }

        .card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 26px;
            padding: 22px;
            box-shadow: var(--shadow-soft);
        }

        .alert {
            padding: 14px 16px;
            border-radius: 18px;
            margin-bottom: 16px;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.5;
        }

        .alert-success {
            background: var(--tosca-soft);
            color: var(--tosca-dark);
            border: 1px solid rgba(18, 169, 154, .18);
        }

        .alert-danger {
            background: var(--pink-soft);
            color: var(--pink-dark);
            border: 1px solid rgba(227, 69, 109, .18);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .form-group {
            display: grid;
            gap: 7px;
        }

        .form-group label {
            font-size: 12px;
            font-weight: 900;
            color: var(--muted);
            text-transform: uppercase;
        }

        .form-control,
        .content-wrap input[type="text"],
        .content-wrap input[type="search"],
        .content-wrap input[type="number"],
        .content-wrap input[type="date"],
        .content-wrap input[type="file"],
        .content-wrap input[type="password"],
        .content-wrap select,
        .content-wrap textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 14px;
            outline: none;
            background: #ffffff;
            color: var(--text);
        }

        .form-control:focus,
        .content-wrap input:focus,
        .content-wrap select:focus,
        .content-wrap textarea:focus {
            border-color: var(--tosca);
            box-shadow: 0 0 0 4px rgba(18, 169, 154, .10);
        }

        .content-wrap a {
            font-weight: 800;
            color: var(--tosca-dark);
        }

        .content-wrap>table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
            width: 100%;
        }

        .content-wrap table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border);
            font-size: 14px;
        }

        .content-wrap table th,
        .content-wrap table td {
            border: 1px solid #d7e1e7;
            padding: 11px 12px;
            text-align: left;
            vertical-align: middle;
        }

        .content-wrap table th {
            background: #e7f9f6;
            color: var(--tosca-dark);
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .content-wrap table tr:nth-child(even) td {
            background: #fbfffe;
        }

        .content-wrap table tr:hover td {
            background: #f4fffc;
        }

        .content-wrap td form {
            display: inline-block;
            margin: 0 4px 4px 0;
        }

        .sidebar-backdrop {
            display: none;
        }

        .mhi-confirm-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .56);
            backdrop-filter: blur(4px);
            z-index: 999999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
        }

        .mhi-confirm-overlay.show {
            display: flex;
        }

        .mhi-confirm-box {
            width: 420px;
            max-width: 100%;
            background: #ffffff;
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 30px 80px rgba(15, 23, 42, .28);
            border: 1px solid rgba(15, 118, 110, .16);
            animation: mhiPop .18s ease forwards;
            position: relative;
            overflow: hidden;
        }

        .mhi-confirm-box::after {
            content: "";
            position: absolute;
            width: 130px;
            height: 130px;
            border-radius: 999px;
            background: rgba(236, 72, 153, .12);
            right: -42px;
            top: -46px;
        }

        @keyframes mhiPop {
            from {
                transform: scale(.94);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .mhi-confirm-content {
            position: relative;
            z-index: 2;
        }

        .mhi-confirm-icon {
            width: 62px;
            height: 62px;
            border-radius: 22px;
            background: linear-gradient(135deg, rgba(20, 184, 166, .18), rgba(236, 72, 153, .14));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 29px;
            margin-bottom: 14px;
        }

        .mhi-confirm-title {
            margin: 0 0 8px;
            color: #0f766e;
            font-size: 22px;
            font-weight: 950;
        }

        .mhi-confirm-message {
            color: #64748b;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .mhi-confirm-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .mhi-confirm-btn {
            border: none;
            border-radius: 14px;
            padding: 11px 18px;
            font-size: 13px;
            font-weight: 950;
            cursor: pointer;
            transition: .18s ease;
        }

        .mhi-confirm-cancel {
            background: #f1f5f9;
            color: #334155;
        }

        .mhi-confirm-ok {
            background: linear-gradient(135deg, #0f766e, #0b5f59);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(15, 118, 110, .20);
        }

        @media(max-width: 1100px) {
            .sidebar {
                transform: translateX(-100%);
                transition: .22s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, .45);
                z-index: 999;
                display: none;
            }

            .sidebar-backdrop.show {
                display: block;
            }

            .main {
                margin-left: 0;
                width: 100%;
                padding: 16px;
            }

            .mobile-menu-btn {
                display: inline-flex;
            }

            .topbar {
                border-radius: 22px;
            }

            .main-header h1 {
                font-size: 22px;
            }
        }

        @media(max-width: 700px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .topbar-actions {
                width: 100%;
                justify-content: space-between;
            }

            .date-pill {
                font-size: 11px;
            }

            .content-wrap table {
                font-size: 12px;
            }

            .content-wrap table th,
            .content-wrap table td {
                padding: 8px;
            }
        }

        @media print {

            .sidebar,
            .topbar,
            .sidebar-backdrop,
            .mhi-confirm-overlay {
                display: none !important;
            }

            .main {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 0 !important;
                background: #ffffff !important;
            }

            body {
                background: #ffffff !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    @php
        $adminName = session('admin_nama') ?? (session('admin_username') ?? 'Admin');
        $adminLevel = \App\Support\AdminRole::level();
        $adminUnit = \App\Support\AdminRole::unit();

        $roleLabels = [
            'superadmin' => 'Super Admin',
            'admin' => 'Admin',
            'bendahara' => 'Bendahara',
            'operator' => 'Operator',
        ];

        $adminRoleLabel = $roleLabels[$adminLevel] ?? ucfirst($adminLevel ?: 'Operator');

        $safeRoute = function ($name, $fallback = '#') {
            return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
        };

        $canAccess = function ($items) {
            foreach ((array) $items as $item) {
                if (\App\Support\AdminRole::is($item) || \App\Support\AdminRole::can($item)) {
                    return true;
                }
            }

            return false;
        };
    @endphp
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-box">
                    <div class="brand-logo">
                        <img src="{{ asset('images/logo-mhi.png') }}" alt="Logo MHI">
                    </div>

                    <div>
                        <h1 class="brand-title">SIKEU MHI V2</h1>
                        <p class="brand-subtitle">Sistem Informasi Keuangan Terpadu</p>
                    </div>
                </div>

                <div class="sidebar-user">
                    <div class="user-avatar">
                        {{ strtoupper(substr($adminName, 0, 1)) }}
                    </div>

                    <div class="user-info">
                        <strong>{{ $adminName }}</strong>
                        <span>
                            {{ $adminRoleLabel }}
                            @if ($adminUnit)
                                · {{ $adminUnit }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <nav class="sidebar-scroll">
                <div class="nav-section">
                    <div class="nav-section-title">Utama</div>

                    <div class="nav-list">
                        <a href="{{ $safeRoute('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span class="nav-icon">🏠</span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </div>
                </div>

                @if ($canAccess(['siswa.view', 'kelas.view', 'jenis-pembayaran.view']))
                    <div class="nav-section">
                        <div class="nav-section-title">Data Master</div>

                        <div class="nav-list">
                            <a href="{{ $safeRoute('siswa.index') }}"
                                class="nav-link {{ request()->routeIs('siswa.*') ? 'active' : '' }}">
                                <span class="nav-icon">👨‍🎓</span>
                                <span class="nav-text">Data Santri</span>
                            </a>

                            @if ($canAccess(['superadmin', 'admin']))
                                <a href="{{ $safeRoute('kelas-formal.index') }}"
                                    class="nav-link {{ request()->routeIs('kelas-formal.*') ? 'active' : '' }}">
                                    <span class="nav-icon">🏫</span>
                                    <span class="nav-text">Kelas Formal</span>
                                </a>

                                <a href="{{ $safeRoute('kelas-diniyah.index') }}"
                                    class="nav-link {{ request()->routeIs('kelas-diniyah.*') ? 'active' : '' }}">
                                    <span class="nav-icon">🕌</span>
                                    <span class="nav-text">Kelas Diniyah</span>
                                </a>

                                <a href="{{ $safeRoute('jenis-pembayaran.index') }}"
                                    class="nav-link {{ request()->routeIs('jenis-pembayaran.*') ? 'active' : '' }}">
                                    <span class="nav-icon">🏷️</span>
                                    <span class="nav-text">Jenis Pembayaran</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    @if ($canAccess(['ppdb.view']))
                        <div class="nav-section">
                            <div class="nav-section-title">PPDB</div>

                            <div class="nav-list">
                                <a href="{{ $safeRoute('ppdb.index') }}"
                                    class="nav-link {{ request()->routeIs('ppdb.*') ? 'active' : '' }}">
                                    <span class="nav-icon">📝</span>
                                    <span class="nav-text">Data PPDB</span>
                                </a>
                            </div>
                        </div>
                    @endif
                @endif

                @if ($canAccess(['pembayaran.view']))
                    <div class="nav-section">
                        <div class="nav-section-title">Pembayaran</div>

                        <div class="nav-list">
                            <a href="{{ $safeRoute('pembayaran-spp.index') }}"
                                class="nav-link {{ request()->routeIs('pembayaran-spp.*') ? 'active' : '' }}">
                                <span class="nav-icon">💳</span>
                                <span class="nav-text">Pembayaran SPP</span>
                            </a>

                            <a href="{{ $safeRoute('pembayaran-lain.index') }}"
                                class="nav-link {{ request()->routeIs('pembayaran-lain.*') ? 'active' : '' }}">
                                <span class="nav-icon">🧾</span>
                                <span class="nav-text">Pembayaran Lain</span>
                            </a>

                            @if ($canAccess(['pengeluaran.view']))
                                <a href="{{ $safeRoute('pengeluaran.index') }}"
                                    class="nav-link {{ request()->routeIs('pengeluaran.*') ? 'active' : '' }}">
                                    <span class="nav-icon">📤</span>
                                    <span class="nav-text">Pengeluaran</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($canAccess(['riwayat.view', 'laporan.view', 'tunggakan.view']))
                    <div class="nav-section">
                        <div class="nav-section-title">Laporan & Monitoring</div>

                        <div class="nav-list">
                            <a href="{{ $safeRoute('riwayat-transaksi.index') }}"
                                class="nav-link {{ request()->routeIs('riwayat-transaksi.*') ? 'active' : '' }}">
                                <span class="nav-icon">📜</span>
                                <span class="nav-text">Riwayat Transaksi</span>
                            </a>

                            <a href="{{ $safeRoute('laporan-pemasukan.index') }}"
                                class="nav-link {{ request()->routeIs('laporan-pemasukan.*') ? 'active' : '' }}">
                                <span class="nav-icon">📈</span>
                                <span class="nav-text">Laporan Pemasukan</span>
                            </a>

                            <a href="{{ $safeRoute('tunggakan.index') }}"
                                class="nav-link {{ request()->routeIs('tunggakan.*') ? 'active' : '' }}">
                                <span class="nav-icon">🔎</span>
                                <span class="nav-text">Cek Tunggakan</span>
                            </a>
                        </div>
                    </div>
                @endif

                <div class="nav-section">
                    <div class="nav-section-title">Pengaturan</div>

                    <div class="nav-list">
                        @if ($canAccess(['admin.manage']))
                            <a href="{{ $safeRoute('atur-admin.index') }}"
                                class="nav-link {{ request()->routeIs('atur-admin.*') ? 'active' : '' }}">
                                <span class="nav-icon">👤</span>
                                <span class="nav-text">Atur Admin</span>
                            </a>
                        @endif

                        <a href="#" class="nav-link nav-disabled" onclick="event.preventDefault();">
                            <span class="nav-icon">💾</span>
                            <span class="nav-text">Backup Data</span>
                            <span class="nav-mini">Soon</span>
                        </a>
                    </div>
                </div>
            </nav>

            <div class="sidebar-footer">
                <form action="{{ $safeRoute('logout') }}" method="POST" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span>🚪</span>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <main class="main">
            <header class="topbar">
                <div class="main-header">
                    <h1>@yield('page_title', 'Dashboard')</h1>
                    <p>@yield('page_subtitle', "Yayasan Pendidikan Pesantren Mamba'ul Khoiriyatil Islamiyah")</p>
                </div>

                <div class="topbar-actions">
                    <button type="button" class="mobile-menu-btn" id="mobileMenuBtn">☰</button>
                    <div class="date-pill">{{ now()->format('d-m-Y') }}</div>
                </div>
            </header>

            <section class="content-wrap">
                @yield('content')
            </section>
        </main>
    </div>

    <div class="mhi-confirm-overlay" id="mhiConfirmOverlay">
        <div class="mhi-confirm-box">
            <div class="mhi-confirm-content">
                <div class="mhi-confirm-icon" id="mhiConfirmIcon">❓</div>
                <h3 class="mhi-confirm-title" id="mhiConfirmTitle">Konfirmasi</h3>
                <div class="mhi-confirm-message" id="mhiConfirmMessage">Apakah Anda yakin?</div>
                <div class="mhi-confirm-actions">
                    <button type="button" class="mhi-confirm-btn mhi-confirm-cancel"
                        id="mhiConfirmCancel">Batal</button>
                    <button type="button" class="mhi-confirm-btn mhi-confirm-ok" id="mhiConfirmOk">Ya,
                        Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');

        if (mobileMenuBtn && sidebar && sidebarBackdrop) {
            mobileMenuBtn.addEventListener('click', function() {
                sidebar.classList.add('show');
                sidebarBackdrop.classList.add('show');
            });
        }

        if (sidebarBackdrop && sidebar) {
            sidebarBackdrop.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarBackdrop.classList.remove('show');
            });
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                if (sidebar && sidebarBackdrop) {
                    sidebar.classList.remove('show');
                    sidebarBackdrop.classList.remove('show');
                }

                document.querySelectorAll('.mhi-confirm-overlay.show').forEach(function(item) {
                    item.classList.remove('show');
                });
            }
        });

        (function() {
            const overlay = document.getElementById('mhiConfirmOverlay');
            const messageBox = document.getElementById('mhiConfirmMessage');
            const titleBox = document.getElementById('mhiConfirmTitle');
            const iconBox = document.getElementById('mhiConfirmIcon');
            const okBtn = document.getElementById('mhiConfirmOk');
            const cancelBtn = document.getElementById('mhiConfirmCancel');

            if (!overlay || !messageBox || !titleBox || !iconBox || !okBtn || !cancelBtn) return;

            let onConfirmCallback = null;

            function extractConfirmMessage(text) {
                if (!text) return null;

                const matchSingle = text.match(/confirm\('([^']*)'\)/);
                if (matchSingle && matchSingle[1]) return matchSingle[1];

                const matchDouble = text.match(/confirm\("([^"]*)"\)/);
                if (matchDouble && matchDouble[1]) return matchDouble[1];

                return null;
            }

            function openMhiConfirm(options) {
                titleBox.textContent = options.title || 'Konfirmasi';
                messageBox.textContent = options.message || 'Apakah Anda yakin?';
                iconBox.textContent = options.icon || '❓';
                okBtn.textContent = options.okText || 'Ya, Lanjutkan';
                cancelBtn.textContent = options.cancelText || 'Batal';
                onConfirmCallback = options.onConfirm || null;
                overlay.classList.add('show');
            }

            function closeMhiConfirm() {
                overlay.classList.remove('show');
                onConfirmCallback = null;
            }

            okBtn.addEventListener('click', function() {
                const callback = onConfirmCallback;
                closeMhiConfirm();

                if (typeof callback === 'function') callback();
            });

            cancelBtn.addEventListener('click', closeMhiConfirm);

            overlay.addEventListener('click', function(event) {
                if (event.target === overlay) closeMhiConfirm();
            });

            document.addEventListener('submit', function(event) {
                const form = event.target instanceof HTMLFormElement ? event.target : event.target.closest('form');

                if (!form || form.dataset.mhiConfirmDone === '1') return;

                const inlineConfirm = form.getAttribute('onsubmit');
                const dataConfirm = form.getAttribute('data-confirm');
                const message = dataConfirm || extractConfirmMessage(inlineConfirm);

                // Only intercept forms with an explicit confirmation message.
                // This avoids blocking normal GET filter or pagination actions.
                if (!message) return;

                event.preventDefault();
                event.stopImmediatePropagation();

                if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                openMhiConfirm({
                    title: 'Konfirmasi',
                    message: message,
                    icon: '🧾',
                    okText: 'Ya, Lanjutkan',
                    cancelText: 'Batal',
                    onConfirm: function() {
                        form.dataset.mhiConfirmDone = '1';

                        const submitter = event.submitter;
                        if (submitter && submitter.name) {
                            const hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = submitter.name;
                            hidden.value = submitter.value;
                            form.appendChild(hidden);
                        }

                        HTMLFormElement.prototype.submit.call(form);
                    }
                });
            }, true);

            window.mhiConfirm = openMhiConfirm;
        })();
    </script>

    @stack('scripts')
</body>

</html>
