@extends('layouts.app')

@section('title', 'Dashboard - SIKEU MHI V2')

@section('page_title', 'Dashboard Admin')

@section('page_subtitle', "Yayasan Pendidikan Pesantren Mamba'ul Khoiriyatil Islamiyah")

@section('content')
    <style>
        /* Use app layout font by inheriting from parent */
        .dashboard-root {
            font-family: inherit;
        }

        /* Animasi */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .animate-slide-right {
            animation: slideInRight 0.6s ease-out forwards;
        }

        .delay-1 {
            animation-delay: 0.1s;
            opacity: 0;
        }

        .delay-2 {
            animation-delay: 0.2s;
            opacity: 0;
        }

        .delay-3 {
            animation-delay: 0.3s;
            opacity: 0;
        }

        /* Header Complex */
        .dashboard-header-complex {
            background: url('data:image/svg+xml;utf8,<svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="2" cy="2" r="1" fill="rgba(255,255,255,0.15)"/></pattern></defs><rect width="100%" height="100%" fill="url(%23dots)"/></svg>'),
                linear-gradient(135deg, var(--tosca-dark) 0%, var(--tosca) 50%, var(--pink) 100%);
            color: white;
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(15, 118, 110, 0.2);
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 30px;
        }

        .dashboard-header-complex::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .header-logo-box {
            position: relative;
            z-index: 2;
        }

        .logo-ring {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15), inset 0 0 0 4px rgba(18, 169, 154, 0.1);
            transform: rotate(-5deg);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .dashboard-header-complex:hover .logo-ring {
            transform: rotate(0deg) scale(1.05);
        }

        .logo-ring img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        .header-info-box {
            position: relative;
            z-index: 2;
            padding-right: 20px;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }

        .welcome-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .header-info-box h2 {
            margin: 0 0 8px;
            font-size: 32px;
            font-weight: 950;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-info-box p {
            margin: 0;
            font-size: 15px;
            opacity: 0.9;
            font-weight: 600;
            max-width: 600px;
        }

        .time-glass-widget {
            position: relative;
            z-index: 2;
            background: rgba(15, 23, 42, 0.25);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 20px 30px;
            border-radius: 24px;
            text-align: right;
            box-shadow: inset 0 0 20px rgba(255, 255, 255, 0.05);
        }

        .time-glass-widget .clock-display {
            font-size: 42px;
            font-weight: 900;
            letter-spacing: 2px;
            margin-bottom: 5px;
            font-variant-numeric: tabular-nums;
            background: linear-gradient(to bottom, #ffffff, #dffaf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .date-badge-group {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
        }

        .date-masehi-complex {
            font-size: 14px;
            font-weight: 800;
            color: #ffffff;
        }

        .date-hijri-complex {
            font-size: 13px;
            color: #ffb6c1;
            font-weight: 700;
            background: rgba(227, 69, 109, 0.3);
            padding: 2px 10px;
            border-radius: 10px;
        }

        /* Action Menu Ribbon */
        .action-ribbon {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            padding: 5px;
            overflow-x: auto;
            scrollbar-width: none;
            /* Firefox */
        }

        .action-ribbon::-webkit-scrollbar {
            display: none;
        }

        /* Chrome */

        .ribbon-btn {
            flex: 0 0 auto;
            background: #ffffff;
            border: 1px solid var(--border);
            padding: 14px 22px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text);
            font-weight: 800;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 4px 6px rgba(15, 23, 42, 0.02);
            position: relative;
            overflow: hidden;
        }

        .ribbon-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--tosca);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .ribbon-btn:nth-child(even)::before {
            background: var(--pink);
        }

        .ribbon-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px rgba(15, 23, 42, 0.06);
            border-color: transparent;
        }

        .ribbon-btn:hover::before {
            opacity: 1;
        }

        .ribbon-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--body);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: transform 0.3s;
        }

        .ribbon-btn:hover .ribbon-icon {
            transform: scale(1.1) rotate(5deg);
            background: var(--tosca-soft);
            color: var(--tosca-dark);
        }

        /* Complex Grid Layout */
        .complex-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.8fr) minmax(0, 1.2fr);
            gap: 26px;
            align-items: start;
        }

        /* Bento Box Finance Cards */
        .bento-finance-wrap {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 26px;
        }

        .bento-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 26px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-soft);
            position: relative;
        }

        .bento-card.primary {
            background: linear-gradient(145deg, #ffffff, #f4fffc);
            border-color: rgba(18, 169, 154, 0.2);
        }

        .bento-card.danger {
            background: linear-gradient(145deg, #ffffff, #fff0f4);
            border-color: rgba(227, 69, 109, 0.2);
        }

        .bento-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .bento-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 900;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .bento-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .primary .bento-icon {
            background: var(--tosca-soft);
            color: var(--tosca-dark);
        }

        .danger .bento-icon {
            background: var(--pink-soft);
            color: var(--pink-dark);
        }

        .bento-amount {
            font-size: 38px;
            font-weight: 950;
            color: var(--text);
            margin-bottom: 15px;
            letter-spacing: -1px;
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .bento-amount small {
            font-size: 18px;
            color: var(--muted);
        }

        .bento-progress-wrap {
            margin-top: 15px;
        }

        .progress-bar-bg {
            height: 8px;
            background: var(--border);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 4px;
            position: relative;
        }

        .progress-bar-fill::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.4) 50%, rgba(255, 255, 255, 0) 100%);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }

        .primary .progress-bar-fill {
            background: var(--tosca);
            width: 85%;
        }

        .danger .progress-bar-fill {
            background: var(--pink);
            width: 30%;
        }

        .progress-stats {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 800;
            color: var(--muted);
        }

        .trend-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
        }

        .trend-up {
            background: #e7f9f6;
            color: var(--tosca-dark);
        }

        .trend-down {
            background: #ffe4ec;
            color: var(--pink-dark);
        }

        /* Detail Breakdown Card */
        .glass-panel {
            background: #ffffff;
            border-radius: 28px;
            padding: 30px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-soft);
        }

        /* Activity rectangle variant */
        .glass-panel.activity-rect {
            border-radius: 10px;
            padding: 18px;
        }

        .transaction-rect-list .trx-complex-item {
            border-radius: 8px;
            padding: 12px 14px;
            background: #ffffff;
            border: 1px solid var(--border);
            box-shadow: 0 6px 12px rgba(12, 18, 26, 0.04);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .panel-header-complex {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px dashed var(--border);
        }

        .panel-header-complex h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 950;
            color: var(--text);
        }

        .panel-header-complex p {
            margin: 4px 0 0;
            font-size: 13px;
            color: var(--muted);
            font-weight: 600;
        }

        .mini-chart-wrap {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            height: 50px;
        }

        .bar-col {
            width: 16px;
            background: var(--body);
            border-radius: 4px;
            position: relative;
            height: 100%;
            display: flex;
            align-items: flex-end;
        }

        .bar-col-fill {
            width: 100%;
            background: var(--tosca);
            border-radius: 4px;
            transition: height 1s ease-out;
        }

        .bar-col:nth-child(even) .bar-col-fill {
            background: var(--pink);
        }

        .breakdown-grid {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 35px;
            align-items: center;
        }

        .complex-pie {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: conic-gradient({{ $diagram['gradient'] }});
            position: relative;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08), inset 0 0 0 2px rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .complex-pie::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.4), transparent 60%);
        }

        .complex-pie::after {
            content: "";
            position: absolute;
            width: 120px;
            height: 120px;
            background: #ffffff;
            border-radius: 50%;
            box-shadow: inset 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .pie-inner-text {
            position: absolute;
            z-index: 10;
            text-align: center;
        }

        .pie-inner-text strong {
            display: block;
            font-size: 24px;
            font-weight: 950;
            color: var(--text);
            line-height: 1;
        }

        .pie-inner-text span {
            font-size: 10px;
            color: var(--muted);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .detailed-legend {
            display: grid;
            gap: 16px;
        }

        .legend-empty-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 18px;
            border-radius: 16px;
            background: linear-gradient(180deg, #fbfdff, #f3faf8);
            border: 1px dashed var(--border);
            color: var(--muted);
            text-align: center;
        }

        .legend-suggestion-row {
            display: flex;
            gap: 12px;
            align-items: center;
            width: 100%;
            justify-content: space-between;
            padding: 10px 12px;
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .legend-row {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 15px;
            align-items: center;
            padding: 12px;
            background: #f8fafc;
            border-radius: 16px;
            border: 1px solid transparent;
            transition: 0.2s;
        }

        .legend-row:hover {
            background: #ffffff;
            border-color: var(--border);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
            transform: scale(1.02);
        }

        .leg-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }

        .diagram-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .summary-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
        }

        .summary-card span {
            display: block;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 10px;
        }

        .summary-card strong {
            display: block;
            font-size: 24px;
            font-weight: 950;
            color: var(--tosca-dark);
            line-height: 1.1;
        }

        .summary-card p {
            margin: 12px 0 0;
            font-size: 13px;
            color: var(--muted);
            line-height: 1.6;
        }

        .diagram-footnote {
            margin-top: 22px;
            padding: 18px 20px;
            border-radius: 22px;
            background: linear-gradient(180deg, #ffffff, #f3faf8);
            border: 1px solid rgba(18, 169, 154, 0.18);
            color: var(--tosca-dark);
            font-size: 13px;
            font-weight: 700;
            line-height: 1.6;
        }

        .leg-details h4 {
            margin: 0 0 2px;
            font-size: 14px;
            font-weight: 800;
            color: var(--text);
        }

        .leg-details .leg-bar-bg {
            height: 4px;
            width: 100px;
            background: #e2e8f0;
            border-radius: 2px;
            margin-top: 6px;
        }

        .leg-details .leg-bar-fill {
            height: 100%;
            border-radius: 2px;
        }

        .leg-amount {
            text-align: right;
        }

        .leg-amount strong {
            display: block;
            font-size: 15px;
            font-weight: 900;
            color: var(--text);
        }

        .leg-amount span {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
        }

        /* Right Column Data */
        .stats-bento-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 26px;
        }

        .stat-bento {
            background: #ffffff;
            border-radius: 24px;
            padding: 22px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
        }

        .stat-bento .icon-bg {
            position: absolute;
            right: -10px;
            bottom: -10px;
            font-size: 80px;
            opacity: 0.04;
            transform: rotate(-15deg);
        }

        .stat-bento .st-value {
            font-size: 32px;
            font-weight: 950;
            color: var(--tosca-dark);
            margin-bottom: 5px;
        }

        .stat-bento:nth-child(2) .st-value {
            color: var(--pink-dark);
        }

        .stat-bento .st-label {
            font-size: 13px;
            font-weight: 800;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .transaction-complex-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .trx-complex-item {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 15px;
            padding: 16px;
            border-radius: 20px;
            background: #ffffff;
            border: 1px solid var(--border);
            transition: all 0.3s;
            position: relative;
        }

        .trx-complex-item:hover {
            border-color: var(--tosca);
            box-shadow: 0 10px 25px rgba(18, 169, 154, 0.08);
            transform: translateY(-2px);
            z-index: 2;
        }

        .trx-av-box {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            border: 1px solid #e2e8f0;
        }

        .trx-info h4 {
            margin: 0 0 4px;
            font-size: 14px;
            font-weight: 900;
            color: var(--text);
        }

        .trx-tags {
            display: flex;
            gap: 6px;
            align-items: center;
            flex-wrap: wrap;
        }

        .badge-type {
            font-size: 10px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 6px;
            background: var(--body);
            color: var(--muted);
            border: 1px solid var(--border);
        }

        .badge-status {
            font-size: 10px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 6px;
            background: #dcfce7;
            color: #059669;
        }

        .trx-money {
            text-align: right;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .trx-money strong {
            font-size: 15px;
            font-weight: 950;
            color: var(--tosca-dark);
        }

        .trx-money span {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            margin-top: 2px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
        }

        .empty-state-complex {
            padding: 40px 20px;
            text-align: center;
            background: #f8fafc;
            border-radius: 20px;
            border: 1px dashed var(--border);
        }

        .empty-state-complex .icon {
            font-size: 48px;
            opacity: 0.5;
            margin-bottom: 15px;
        }

        .empty-state-complex h4 {
            margin: 0 0 5px;
            font-size: 16px;
            font-weight: 800;
            color: var(--text);
        }

        .empty-state-complex p {
            margin: 0;
            font-size: 13px;
            color: var(--muted);
        }

        @media(max-width: 1200px) {
            .complex-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-header-complex {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 20px;
            }

            .header-info-box {
                border-right: none;
                padding-right: 0;
            }

            .time-glass-widget {
                text-align: center;
            }

            .date-badge-group {
                align-items: center;
            }
        }

        @media(max-width: 768px) {
            .bento-finance-wrap {
                grid-template-columns: 1fr;
            }

            .breakdown-grid {
                grid-template-columns: 1fr;
                justify-items: center;
            }

            .stats-bento-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="dashboard-root">
        <div class="dashboard-header-complex animate-fade-in">
            <div class="header-logo-box">
                <div class="logo-ring">
                    <img src="{{ asset('images/logo-mhi.png') }}" alt="Logo MHI">
                </div>
            </div>

            <div class="header-info-box">
                <div class="welcome-badge">✦ Admin Area</div>
                <h2>Assalamu'alaikum Warahmatullahi Wabarakatuh, {{ session('admin_nama') ?? 'Administrator' }}</h2>
                <p>Semoga berkah dan kemudahan senantiasa menyertai setiap langkah pengelolaan keuangan di Pesantren
                    Mamba'ul Khoiriyatil Islamiyah.</p>
            </div>

            <div class="time-glass-widget">
                <div class="clock-display" id="realtime-clock">00:00:00</div>
                <div class="date-badge-group">
                    <div class="date-masehi-complex" id="date-masehi">Memuat...</div>
                    <div class="date-hijri-complex" id="date-hijriyah">Memuat Hijriyah...</div>
                </div>
            </div>
        </div>

        <div class="action-ribbon animate-fade-in delay-1">
            <a href="{{ route('pembayaran-spp.index') }}" class="ribbon-btn">
                <div class="ribbon-icon">💳</div>
                <span>Transaksi SPP</span>
            </a>
            <a href="{{ route('pembayaran-lain.index') }}" class="ribbon-btn">
                <div class="ribbon-icon">🧾</div>
                <span>Pembayaran Lain</span>
            </a>
            <a href="{{ route('pengeluaran.index') }}" class="ribbon-btn">
                <div class="ribbon-icon">📤</div>
                <span>Input Kas Keluar</span>
            </a>
            <a href="{{ route('tunggakan.index') }}" class="ribbon-btn">
                <div class="ribbon-icon">🔎</div>
                <span>Cek Tunggakan</span>
            </a>
            <a href="{{ route('siswa.index') }}" class="ribbon-btn">
                <div class="ribbon-icon">👨‍🎓</div>
                <span>Database Santri</span>
            </a>
            <a href="{{ route('laporan-pemasukan.index') }}" class="ribbon-btn">
                <div class="ribbon-icon">📈</div>
                <span>Laporan Global</span>
            </a>
        </div>

        <div class="complex-grid">
            <!-- Main Content Column -->
            <div class="main-column animate-slide-right delay-2">

                <div class="bento-finance-wrap">
                    <div class="bento-card primary">
                        <div class="bento-header">
                            <div class="bento-title">Pemasukan Bulan Ini</div>
                            <div class="bento-icon">💰</div>
                        </div>
                        <div class="bento-amount">
                            <small>Rp</small><span
                                id="totalPemasukanBulanIni">{{ number_format($totalPemasukanBulanIni ?? 0, 0, ',', '.') }}</span>
                        </div>

                        <div class="progress-stats">
                            <span>Pemasukan Hari Ini</span>
                            <span class="trend-badge trend-up">↑ Rp <span
                                    id="totalPemasukanHariIni">{{ number_format($totalPemasukanHariIni ?? 0, 0, ',', '.') }}</span></span>
                        </div>
                        <div class="bento-progress-wrap">
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill"></div>
                            </div>
                            <div style="font-size: 11px; color: var(--muted); text-align: right; font-weight: 700;">
                                Aktivitas Tinggi
                            </div>
                        </div>
                    </div>

                    <div class="bento-card danger">
                        <div class="bento-header">
                            <div class="bento-title">Pengeluaran Bulan Ini</div>
                            <div class="bento-icon">📉</div>
                        </div>
                        <div class="bento-amount">
                            <small>Rp</small><span
                                id="totalPengeluaranBulanIni">{{ number_format($totalPengeluaranBulanIni ?? 0, 0, ',', '.') }}</span>
                        </div>

                        <div class="progress-stats">
                            <span>Pengeluaran Hari Ini</span>
                            <span class="trend-badge trend-down">↓ Rp <span
                                    id="totalPengeluaranHariIni">{{ number_format($totalPengeluaranHariIni ?? 0, 0, ',', '.') }}</span></span>
                        </div>
                        <div class="bento-progress-wrap">
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill"
                                    style="width: {{ ($totalPengeluaranBulanIni ?? 0) > 0 ? min(100, (($totalPengeluaranHariIni ?? 0) / ($totalPengeluaranBulanIni ?? 1)) * 100) : 0 }}%">
                                </div>
                            </div>
                            <div style="font-size: 11px; color: var(--muted); text-align: right; font-weight: 700;">
                                Pengeluaran Terpantau
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass-panel">
                    <div class="panel-header-complex">
                        <div>
                            <h3>Analisis Pemasukan</h3>
                            <p>Distribusi sumber dana berdasarkan kategori pembayaran bulan ini</p>
                        </div>

                        <!-- Simulasi Grafik Mini (Hanya UI) -->
                        <div class="mini-chart-wrap" title="Tren 7 Hari Terakhir (Simulasi)">
                            <div class="bar-col">
                                <div class="bar-col-fill" style="height: 40%"></div>
                            </div>
                            <div class="bar-col">
                                <div class="bar-col-fill" style="height: 60%"></div>
                            </div>
                            <div class="bar-col">
                                <div class="bar-col-fill" style="height: 30%"></div>
                            </div>
                            <div class="bar-col">
                                <div class="bar-col-fill" style="height: 80%"></div>
                            </div>
                            <div class="bar-col">
                                <div class="bar-col-fill" style="height: 50%"></div>
                            </div>
                            <div class="bar-col">
                                <div class="bar-col-fill" style="height: 90%"></div>
                            </div>
                            <div class="bar-col">
                                <div class="bar-col-fill" style="height: 100%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="breakdown-grid">
                        <div class="complex-pie" id="complex-pie"
                            style="background: conic-gradient({{ $diagram['gradient'] }});">
                            <div class="pie-inner-text">
                                @if (isset($diagram['total']) && $diagram['total'] > 0)
                                    <strong id="diagram-count">Rp
                                        {{ number_format($diagram['total'], 0, ',', '.') }}</strong>
                                    <span>{{ count($diagram['items']) }} Kategori Aktif</span>
                                @else
                                    <strong style="font-size:14px">Belum ada data</strong>
                                    <span style="font-size:11px">Tambahkan transaksi untuk melihat grafik</span>
                                @endif
                            </div>
                        </div>

                        <div class="detailed-legend" id="detailed-legend">
                            @php
                                $expected = ['SPP Formal', 'Syahriyah Pondok', 'Infaq/Lainnya'];
                                $itemsMap = [];
                                foreach ($diagram['items'] ?? [] as $it) {
                                    $itemsMap[$it['label']] = $it;
                                }
                            @endphp

                            @if (isset($diagram['total']) && $diagram['total'] > 0)
                                @foreach ($expected as $label)
                                    @php
                                        $it = $itemsMap[$label] ?? [
                                            'label' => $label,
                                            'nominal' => 0,
                                            'persen' => 0,
                                            'color' =>
                                                $label == 'SPP Formal'
                                                    ? '#12a99a'
                                                    : ($label == 'Syahriyah Pondok'
                                                        ? '#e3456d'
                                                        : '#f59e0b'),
                                        ];
                                    @endphp
                                    <div class="legend-row">
                                        <div class="leg-icon" style="background: {{ $it['color'] ?? '#e5e7eb' }}">
                                            @if (strpos($label, 'Formal') !== false)
                                                🏫
                                            @elseif (strpos($label, 'Pondok') !== false)
                                                🕌
                                            @else
                                                🧾
                                            @endif
                                        </div>
                                        <div class="leg-details">
                                            <h4>{{ $it['label'] }}</h4>
                                            <div class="leg-bar-bg">
                                                <div class="leg-bar-fill"
                                                    style="background: {{ $it['color'] ?? '#e5e7eb' }}; width: {{ $it['persen'] }}%">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="leg-amount">
                                            <strong>Rp {{ number_format($it['nominal'] ?? 0, 0, ',', '.') }}</strong>
                                            <span>{{ $it['persen'] ?? 0 }}% dari total</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="legend-empty-card">
                                    <div style="font-weight:800; margin-bottom:6px;">Belum Ada Data Pemasukan</div>
                                    <div style="font-size:13px; margin-bottom:12px; color:var(--muted);">Transaksi bulan
                                        ini belum tersedia. Berikut contoh kategori yang akan muncul:</div>
                                    <div style="width:100%; display:flex; flex-direction:column; gap:8px;">
                                        @foreach ($expected as $label)
                                            @php
                                                $color =
                                                    $label == 'SPP Formal'
                                                        ? '#12a99a'
                                                        : ($label == 'Syahriyah Pondok'
                                                            ? '#e3456d'
                                                            : '#f59e0b');
                                            @endphp
                                            <div class="legend-suggestion-row">
                                                <div style="display:flex; gap:10px; align-items:center;">
                                                    <div
                                                        style="width:36px; height:36px; border-radius:10px; background:{{ $color }}; display:flex; align-items:center; justify-content:center; color:#fff;">
                                                        @if (strpos($label, 'Formal') !== false)
                                                            🏫
                                                        @elseif(strpos($label, 'Pondok') !== false)
                                                        🕌@else🧾
                                                        @endif
                                                    </div>
                                                    <div style="font-weight:800;">{{ $label }}</div>
                                                </div>
                                                <div style="text-align:right; color:var(--muted);">Rp 0</div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div style="margin-top:12px; font-size:12px; color:var(--muted);">Masukkan transaksi
                                        untuk melihat analisis dan diagram.</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @php
                        $diagramTotal = $diagram['total'] ?? 0;
                        $diagramItems = $diagram['items'] ?? [];
                        $saldoBersih = ($totalPemasukanBulanIni ?? 0) - ($totalPengeluaranBulanIni ?? 0);
                        $averageDaily = $diagramTotal > 0 ? round($diagramTotal / max(1, now()->day), 0) : 0;
                    @endphp

                    <div class="diagram-summary-grid">
                        <div class="summary-card">
                            <span>Total Dana Bulan Ini</span>
                            <strong>Rp {{ number_format($diagramTotal, 0, ',', '.') }}</strong>
                            <p>Rekap sumber penerimaan yang sudah masuk ke kas pesantren bulan ini.</p>
                        </div>
                        <div class="summary-card">
                            <span>Rasio Saldo</span>
                            <strong>{{ $saldoBersih >= 0 ? '+' : '-' }}Rp
                                {{ number_format(abs($saldoBersih), 0, ',', '.') }}</strong>
                            <p>Saldo bersih setelah pengeluaran menunjukkan kesehatan kas pondok.</p>
                        </div>
                        <div class="summary-card">
                            <span>Rata-rata Setoran</span>
                            <strong>Rp {{ number_format($averageDaily, 0, ',', '.') }}</strong>
                            <p>Perkiraan arus kas harian berdasarkan total pemasukan saat ini.</p>
                        </div>
                    </div>

                    <div class="diagram-footnote">
                        Data ini mencerminkan arus keuangan utama di pesantren: SPP formal, syahriyah pondok, dan
                        infaq/lainnya. Pastikan setiap transaksi tercatat agar laporan lebih akurat dan mudah dilaporkan.
                    </div>

                    <!-- Pindahkan Aktivitas Terkini ke bawah Analisis Pemasukan -->
                    <div class="glass-panel activity-rect">
                        <div class="panel-header-complex"
                            style="border-bottom: none; padding-bottom: 0; margin-bottom: 20px;">
                            <div>
                                <h3>Aktivitas Terkini</h3>
                                <p>Riwayat pembayaran santri terbaru</p>
                            </div>
                            <a href="{{ route('riwayat-transaksi.index') }}"
                                style="font-size: 12px; font-weight: 800; color: var(--tosca); text-decoration: none; background: var(--tosca-soft); padding: 6px 12px; border-radius: 8px;">Lihat
                                Semua</a>
                        </div>

                        <div class="transaction-complex-list transaction-rect-list" id="transaksi-list">
                            @forelse($transaksiTerakhir as $trx)
                                <div class="trx-complex-item">
                                    <div class="trx-av-box">
                                        @if ($trx['badge'] === 'Formal')
                                            🏫
                                        @elseif($trx['badge'] === 'Pondok')
                                            🕌
                                        @else
                                            🧾
                                        @endif
                                    </div>

                                    <div class="trx-info">
                                        <h4>{{ $trx['nama'] }}</h4>
                                        <div class="trx-tags">
                                            <span class="badge-type">{{ $trx['jenis'] }}</span>
                                            @if ($trx['periode'] && $trx['periode'] !== '-')
                                                <span class="badge-type"
                                                    style="background: #f1f5f9;">{{ $trx['periode'] }}</span>
                                            @endif
                                            <span class="badge-status">✓ Sukses</span>
                                        </div>
                                    </div>

                                    <div class="trx-money">
                                        <strong>Rp {{ number_format($trx['nominal'], 0, ',', '.') }}</strong>
                                        <span>🕛 {{ \Carbon\Carbon::parse($trx['tanggal'])->format('d M Y') }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state-complex">
                                    <div class="icon">📭</div>
                                    <h4>Belum Ada Transaksi Baru</h4>
                                    <p>Data pembayaran santri akan otomatis muncul di sini setelah transaksi dilakukan.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

            <!-- Side Column -->
            <div class="side-column animate-slide-right delay-3">

                <div class="stats-bento-grid">
                    <div class="stat-bento">
                        <div class="icon-bg">👨‍🎓</div>
                        <div class="st-value" id="santriAktif">{{ number_format($santriAktif ?? 0, 0, ',', '.') }}</div>
                        <div class="st-label">Santri Aktif</div>
                        <div style="margin-top: 10px; font-size: 11px; color: var(--muted); font-weight: 700;">
                            Dari total <span id="totalSantri">{{ number_format($totalSantri ?? 0, 0, ',', '.') }}</span>
                            data
                            santri
                        </div>
                    </div>

                    <div class="stat-bento">
                        <div class="icon-bg">🏫</div>
                        <div class="st-value" id="totalKelas">
                            {{ number_format(($totalKelasFormal ?? 0) + ($totalKelasDiniyah ?? 0), 0, ',', '.') }}</div>
                        <div class="st-label">Total Kelas</div>
                        <div style="margin-top: 10px; font-size: 11px; color: var(--muted); font-weight: 700;">
                            {{ $totalKelasFormal }} Formal & {{ $totalKelasDiniyah }} Diniyah
                        </div>
                    </div>
                </div>

                <!-- Additional pondok-themed info cards -->
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:16px;">
                    <div class="stat-bento" style="background: linear-gradient(135deg,#e6fff9,#ffffff);">
                        <div class="icon-bg">🧾</div>
                        @php
                            $saldo = ($totalPemasukanBulanIni ?? 0) - ($totalPengeluaranBulanIni ?? 0);
                            $ratio =
                                $totalPemasukanBulanIni > 0 ? round(($saldo / $totalPemasukanBulanIni) * 100, 1) : 0;
                        @endphp
                        <div class="st-value" id="saldoBersih">{{ number_format($saldo ?? 0, 0, ',', '.') }}</div>
                        <div class="st-label">Saldo Bersih Bulan Ini</div>
                        <div id="saldoRatio"
                            style="margin-top:10px; font-size:13px; font-weight:700; color: {{ $ratio >= 0 ? '#047857' : '#b91c1c' }};">
                            {{ $ratio >= 0 ? '+' : '' }}{{ number_format($ratio, 1, ',', '.') }}% dari pemasukan
                        </div>
                    </div>

                    <div class="stat-bento"
                        style="background: linear-gradient(135deg,#fff7ed,#ffffff); position: relative;">
                        <div class="icon-bg">📜</div>
                        <div style="font-size:14px; font-weight:900; margin-bottom:6px; color:var(--tosca-dark);">Doa
                            Harian
                            Pesantren</div>
                        <div style="font-size:13px; color:var(--muted); font-weight:700;">
                            <strong>Rabbi zidni ilma</strong>
                        </div>
                        <div style="margin-top:10px; font-size:12px; color:var(--muted); line-height:1.6;">
                            Semoga setiap langkah keuangan dan pembelajaran di pesantren diberkahi, terjaga, dan bermanfaat
                            untuk seluruh santri.
                        </div>
                        <div style="margin-top:14px; font-size:11px; color:var(--muted);">
                            “Barang siapa menempuh jalan untuk menuntut ilmu, Allah akan memudahkan baginya jalan menuju
                            surga.”
                        </div>
                    </div>
                </div>

                <div class="glass-panel">
                    <div class="panel-header-complex"
                        style="border-bottom: none; padding-bottom: 0; margin-bottom: 20px;">
                        <div>
                            <h3>Waktu Shalat & Agenda Harian</h3>
                            <p>Jadwal otomatis untuk santri dan rutinitas harian pesantren</p>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 18px;">
                        <div
                            style="background: #f8fafc; border-radius: 18px; padding: 18px; border: 1px solid var(--border);">
                            <div style="font-size:14px; font-weight:900; margin-bottom:12px; color:var(--text);">Shalat
                                Hari
                                Ini</div>
                            <div style="font-size:12px; color:var(--muted); margin-bottom: 12px;">
                                Lokasi: {{ $prayerTimes['city'] ?? 'Pati' }}, {{ $prayerTimes['country'] ?? 'Indonesia' }}
                            </div>
                            <div id="prayer-times-list">
                                @foreach ($prayerTimes['timings'] ?? [] as $label => $time)
                                    <div class="legend-row" style="padding: 12px 14px;">
                                        <div class="leg-details"
                                            style="display:flex; justify-content:space-between; gap:12px; align-items:center;">
                                            <strong
                                                style="font-size:13px; color:var(--text);">{{ $label }}</strong>
                                            <span style="font-size:13px; color:var(--muted);">{{ $time }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div style="margin-top: 10px; font-size:11px; color:var(--muted);">
                                Sumber: {{ $prayerTimes['source'] ?? 'Fallback Lokal' }}
                            </div>
                        </div>

                        <div
                            style="background: #fff7ed; border-radius: 18px; padding: 18px; border: 1px solid var(--border);">
                            <div style="font-size:14px; font-weight:900; margin-bottom:12px; color:var(--text);">Agenda
                                Harian
                                Pondok</div>
                            <div id="daily-agenda-list">
                                @foreach ($dailyAgenda as $agenda)
                                    <div class="legend-row" style="padding: 12px 14px;">
                                        <div class="leg-details"
                                            style="display:flex; justify-content:space-between; gap:12px; align-items:center;">
                                            <strong
                                                style="font-size:13px; color:var(--text);">{{ $agenda['title'] }}</strong>
                                            <span style="font-size:13px; color:var(--muted);">{{ $agenda['time'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();

            // Format time
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            // Add blinking colon effect manually if desired, or just show text
            document.getElementById('realtime-clock').innerHTML =
                `${hours}<span style="opacity: 0.8">:</span>${minutes}<span style="opacity: 0.5; font-size: 0.8em; margin-left: 2px">${seconds}</span>`;

            // Format Masehi Date
            const daysMasehi = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const monthsMasehi = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];

            const dayName = daysMasehi[now.getDay()];
            const date = now.getDate();
            const monthName = monthsMasehi[now.getMonth()];
            const year = now.getFullYear();

            document.getElementById('date-masehi').textContent = `${dayName}, ${date} ${monthName} ${year} M`;

            // Hijriyah Approximation
            const g = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            let d = g.getDate();
            let m = g.getMonth() + 1;
            let y = g.getFullYear();

            if (m < 3) {
                y -= 1;
                m += 12;
            }

            let a = Math.floor(y / 100);
            let b = 2 - a + Math.floor(a / 4);

            if (y < 1583) b = 0;
            if (y == 1582) {
                if (m > 10) b = -10;
                if (m == 10) {
                    b = 0;
                    if (d > 4) b = -10;
                }
            }

            let jd = Math.floor(365.25 * (y + 4716)) + Math.floor(30.6001 * (m + 1)) + d + b - 1524;

            b = 0;
            if (jd > 2299160) {
                a = Math.floor((jd - 1867216.25) / 36524.25);
                b = 1 + a - Math.floor(a / 4);
            }

            let bb = jd + b + 1524;
            let cc = Math.floor((bb - 122.1) / 365.25);
            let dd = Math.floor(365.25 * cc);
            let ee = Math.floor((bb - dd) / 30.6001);

            d = (bb - dd) - Math.floor(30.6001 * ee);
            m = ee - 1;

            if (ee > 13) {
                cc += 1;
                m = ee - 13;
            }
            y = cc - 4716;

            let wd = g.getDay() + 1;
            let iyear = 10631 / 30;
            let epochastro = 1948084;
            let epochcivil = 1948085;
            let shift1 = 8.01 / 60;

            let z = jd - epochastro;
            let cyc = Math.floor(z / 10631);
            z = z - 10631 * cyc;
            let j = Math.floor((z - shift1) / iyear);
            let iy = 30 * cyc + j;
            z = z - Math.floor(j * iyear + shift1);
            let im = Math.floor((z + 28.5001) / 29.5);

            if (m == 13) im = 12;
            let id = z - Math.floor(29.5001 * im - 29);

            const monthsHijri = [
                "Muharram", "Safar", "Rabi'ul Awal", "Rabi'ul Akhir",
                "Jumadil Awal", "Jumadil Akhir", "Rajab", "Sya'ban",
                "Ramadhan", "Syawal", "Dzulqa'dah", "Dzulhijjah"
            ];

            document.getElementById('date-hijriyah').innerHTML = `🌙 ${id} ${monthsHijri[im - 1]} ${iy} H`;
        }

        updateClock();
        setInterval(updateClock, 1000);

        // Simple animation trigger for mini chart bars
        setTimeout(() => {
            const bars = document.querySelectorAll('.bar-col-fill');
            bars.forEach(bar => {
                const height = bar.style.height;
                bar.style.height = '0';
                setTimeout(() => {
                    bar.style.transition = 'height 1.5s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                    bar.style.height = height;
                }, 100);
            });
        }, 500);

        // Realtime data fetch and DOM updater
        const dashboardDataUrl = "{{ route('dashboard.data') }}";

        function formatIDR(value) {
            try {
                return new Intl.NumberFormat('id-ID').format(Number(value || 0));
            } catch (e) {
                return value;
            }
        }

        function renderTransactions(items) {
            const container = document.getElementById('transaksi-list');
            if (!container) return;
            if (!items || items.length === 0) {
                container.innerHTML = `
                    <div class="empty-state-complex">
                        <div class="icon">📭</div>
                        <h4>Belum Ada Transaksi Baru</h4>
                        <p>Data pembayaran santri akan otomatis muncul di sini setelah transaksi dilakukan.</p>
                    </div>`;
                return;
            }

            container.innerHTML = items.map(trx => {
                const badge = trx.badge === 'Formal' ? '🏫' : (trx.badge === 'Pondok' ? '🕌' : '🧾');
                const periodeTag = trx.periode && trx.periode !== '-' ?
                    `<span class="badge-type" style="background: #f1f5f9;">${trx.periode}</span>` : '';
                const tanggal = trx.tanggal ? new Date(trx.tanggal).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }) : '-';
                return `
                    <div class="trx-complex-item">
                        <div class="trx-av-box">${badge}</div>
                        <div class="trx-info">
                            <h4>${trx.nama || '-'}</h4>
                            <div class="trx-tags">
                                <span class="badge-type">${trx.jenis || '-'}</span>
                                ${periodeTag}
                                <span class="badge-status">✓ Sukses</span>
                            </div>
                        </div>
                        <div class="trx-money">
                            <strong>Rp ${formatIDR(trx.nominal)}</strong>
                            <span>🕛 ${tanggal}</span>
                        </div>
                    </div>`;
            }).join('');
        }

        function renderDiagramAndLegend(diagram) {
            const pie = document.getElementById('complex-pie');
            const countEl = document.getElementById('diagram-count');
            const legend = document.getElementById('detailed-legend');

            const hasItems = diagram && Array.isArray(diagram.items) && diagram.items.length > 0;
            const hasGradient = diagram && diagram.gradient && diagram.gradient.toString().trim() !== '';

            if (pie) {
                if (!hasItems || !hasGradient) {
                    pie.style.background = '';
                    pie.innerHTML =
                        `<div class="pie-inner-text"><strong style="font-size:14px">Belum ada data</strong><span style="font-size:11px">Tambahkan transaksi</span></div>`;
                } else {
                    pie.style.background = `conic-gradient(${diagram.gradient})`;
                    pie.innerHTML =
                        `<div class="pie-inner-text"><strong id="diagram-count">${diagram.items.length}</strong><span>Sumber</span></div>`;
                }
            }

            if (countEl) {
                countEl.textContent = hasItems ? diagram.items.length : 0;
            }

            if (legend) {
                if (!hasItems) {
                    legend.innerHTML = `
                        <div class="empty-state-complex">
                            <div class="icon">📭</div>
                            <h4>Belum ada data pemasukan</h4>
                            <p>Transaksi belum tersedia untuk bulan ini.</p>
                        </div>`;
                } else {
                    legend.innerHTML = diagram.items.map(item => `
                    <div class="legend-row">
                        <div class="leg-icon" style="background: ${item.color || '#e5e7eb'}">
                            ${item.label === 'SPP Formal' ? '🏫' : (item.label.includes('Pondok') ? '🕌' : '🧾')}
                        </div>
                        <div class="leg-details">
                            <h4>${item.label}</h4>
                            <div class="leg-bar-bg">
                                <div class="leg-bar-fill" style="background: ${item.color || '#e5e7eb'}; width: ${item.persen}%"></div>
                            </div>
                        </div>
                        <div class="leg-amount">
                            <strong>Rp ${formatIDR(item.nominal)}</strong>
                            <span>${item.persen}% dari total</span>
                        </div>
                    </div>
                `).join('');
                }
            }
        }

        function renderPrayerTimes(prayerTimes) {
            const list = document.getElementById('prayer-times-list');
            if (!list) return;
            const timings = prayerTimes && prayerTimes.timings ? prayerTimes.timings : null;
            if (!timings) {
                list.innerHTML =
                    '<div class="empty-state-complex"><div class="icon">🌙</div><h4>Waktu shalat belum tersedia</h4><p>Silakan refresh atau periksa koneksi API.</p></div>';
                return;
            }

            list.innerHTML = Object.entries(timings).map(([label, time]) => `
                <div class="legend-row" style="padding: 12px 14px;">
                    <div class="leg-details" style="display:flex; justify-content:space-between; gap:12px; align-items:center;">
                        <strong style="font-size:13px; color:var(--text);">${label}</strong>
                        <span style="font-size:13px; color:var(--muted);">${time}</span>
                    </div>
                </div>`).join('');
        }

        function renderDailyAgenda(agenda) {
            const list = document.getElementById('daily-agenda-list');
            if (!list) return;
            if (!agenda || agenda.length === 0) {
                list.innerHTML =
                    '<div class="empty-state-complex"><div class="icon">📅</div><h4>Agenda kosong</h4><p>Data agenda harian belum tersedia.</p></div>';
                return;
            }

            list.innerHTML = agenda.map(item => `
                <div class="legend-row" style="padding: 12px 14px;">
                    <div class="leg-details" style="display:flex; justify-content:space-between; gap:12px; align-items:center;">
                        <strong style="font-size:13px; color:var(--text);">${item.title}</strong>
                        <span style="font-size:13px; color:var(--muted);">${item.time}</span>
                    </div>
                </div>`).join('');
        }

        async function fetchDashboardData() {
            try {
                const res = await fetch(dashboardDataUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) return;
                const data = await res.json();

                // Update top metrics
                const elTotalPemasukanBulan = document.getElementById('totalPemasukanBulanIni');
                const elTotalPemasukanHari = document.getElementById('totalPemasukanHariIni');
                const elTotalPengeluaranBulan = document.getElementById('totalPengeluaranBulanIni');
                const elTotalPengeluaranHari = document.getElementById('totalPengeluaranHariIni');
                const elSantri = document.getElementById('santriAktif');
                const elTotalSantri = document.getElementById('totalSantri');
                const elTotalKelas = document.getElementById('totalKelas');
                const elSaldoBersih = document.getElementById('saldoBersih');
                const elSaldoRatio = document.getElementById('saldoRatio');

                if (elTotalPemasukanBulan) elTotalPemasukanBulan.textContent = formatIDR(data.totalPemasukanBulanIni);
                if (elTotalPemasukanHari) elTotalPemasukanHari.textContent = formatIDR(data.totalPemasukanHariIni);
                if (elTotalPengeluaranBulan) elTotalPengeluaranBulan.textContent = formatIDR(data
                    .totalPengeluaranBulanIni);
                if (elTotalPengeluaranHari) elTotalPengeluaranHari.textContent = formatIDR(data
                    .totalPengeluaranHariIni);
                if (elSantri) elSantri.textContent = formatIDR(data.santriAktif);
                if (elTotalSantri) elTotalSantri.textContent = formatIDR(data.totalSantri);
                if (elTotalKelas) elTotalKelas.textContent = formatIDR(data.totalKelas);
                if (elSaldoBersih) elSaldoBersih.textContent = formatIDR(data.saldoBersih);
                if (elSaldoRatio && typeof data.saldoRatio !== 'undefined') {
                    elSaldoRatio.textContent = `${data.saldoRatio >= 0 ? '+' : ''}${data.saldoRatio}% dari pemasukan`;
                    elSaldoRatio.style.color = data.isPositiveSaldo ? '#047857' : '#b91c1c';
                }

                renderTransactions(data.transaksiTerakhir || []);
                renderDiagramAndLegend(data.diagram || {
                    items: [],
                    gradient: ''
                });
                renderPrayerTimes(data.prayerTimes || null);
                renderDailyAgenda(data.dailyAgenda || []);
            } catch (e) {
                console.error('Failed to fetch dashboard data', e);
            }
        }

        // Initial fetch and polling every 8 seconds
        fetchDashboardData();
        setInterval(fetchDashboardData, 8000);
    </script>
@endsection
