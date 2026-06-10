<style>
    .pl-tabs-wrap{
        background:#ffffff;
        border:1px solid rgba(15,118,110,.14);
        border-radius:22px;
        padding:12px;
        display:flex;
        gap:10px;
        flex-wrap:wrap;
        margin-bottom:18px;
        box-shadow:0 14px 36px rgba(15,23,42,.06);
    }
    .pl-tab{
        min-height:42px;
        border-radius:16px;
        padding:10px 14px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        font-size:13px;
        font-weight:950;
        text-decoration:none;
        border:1px solid #dbe5ec;
        background:#f8fafc;
        color:#334155!important;
    }
    .pl-tab.active{
        background:linear-gradient(135deg,#0f9f8f,#087c73);
        color:#ffffff!important;
        border-color:#0f9f8f;
        box-shadow:0 12px 25px rgba(15,118,110,.20);
    }
</style>

<div class="pl-tabs-wrap">
    <a href="{{ route('pembayaran-lain.index') }}"
       class="pl-tab {{ request()->routeIs('pembayaran-lain.index') ? 'active' : '' }}">
        🧾 Tagihan Tetap
    </a>

    <a href="{{ route('pembayaran-lain.bebas.index') }}"
       class="pl-tab {{ request()->routeIs('pembayaran-lain.bebas.*') ? 'active' : '' }}">
        💰 Setoran Bebas
    </a>
</div>
