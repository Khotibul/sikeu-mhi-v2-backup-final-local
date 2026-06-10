@php
    $isEdit = ($mode ?? 'create') === 'edit';
    $item = $item ?? null;

    $value = function ($field, $default = '') use ($item) {
        return old($field, $item->{$field} ?? $default);
    };
@endphp

<style>
    .bebas-form-card{background:#fff;border:1px solid rgba(15,118,110,.14);border-radius:24px;padding:22px;box-shadow:0 18px 55px rgba(15,23,42,.08);margin-bottom:18px}
    .bebas-form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
    .bebas-field{display:grid;gap:7px}
    .bebas-field label{font-size:12px;font-weight:950;color:#475569;text-transform:uppercase}
    .bebas-control{width:100%;min-height:44px;border:1px solid #d5e1e8;border-radius:14px;padding:10px 13px;outline:none;font-weight:750;color:#0f172a;background:#fff}
    .bebas-control:focus{border-color:#0f9f8f;box-shadow:0 0 0 4px rgba(15,159,143,.12)}
    .bebas-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-top:16px}
    .bebas-btn{border:none;border-radius:14px;padding:11px 16px;font-weight:950;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer}
    .bebas-btn-primary{background:linear-gradient(135deg,#0f9f8f,#087c73);color:#fff!important;box-shadow:0 12px 25px rgba(15,118,110,.2)}
    .bebas-btn-light{background:#f1f5f9;color:#334155!important;border:1px solid #dbe5ec}
    .bebas-note{display:block;color:#64748b;font-size:12px;font-weight:750;line-height:1.6}
    @media(max-width:800px){.bebas-form-grid{grid-template-columns:1fr}.bebas-btn{width:100%;justify-content:center}}
</style>

<div class="bebas-form-card">
    <div class="bebas-form-grid">
        <div class="bebas-field">
            <label>Tanggal *</label>
            <input type="date" name="tanggal" class="bebas-control" value="{{ $value('tanggal', date('Y-m-d')) }}" required>
        </div>

        <div class="bebas-field">
            <label>Nama Penyetor *</label>
            <input type="text" name="nama_penyetor" class="bebas-control" value="{{ $value('nama_penyetor') }}" placeholder="Contoh: Ahmad / Hamba Allah" required>
        </div>
    </div>

    <div class="bebas-field" style="margin-top:14px;">
        <label>Uraian / Keperluan *</label>
        <input type="text" name="uraian" class="bebas-control" value="{{ $value('uraian') }}" placeholder="Contoh: Infaq pembangunan / Kitab Fathul Qorib 2 pcs" required>
        <small class="bebas-note">Untuk kitab satuan, tulis nama kitab dan jumlahnya di uraian.</small>
    </div>

    <div class="bebas-field" style="margin-top:14px;">
        <label>Nominal *</label>
        <input type="number" name="nominal" class="bebas-control" value="{{ $value('nominal', 0) }}" min="0" required>
        <small class="bebas-note">Nominal bebas. Tidak memakai tarif normal dan tidak memakai logika tagihan bulanan.</small>
    </div>

    <div class="bebas-actions">
        <button type="submit" class="bebas-btn bebas-btn-primary">
            {{ $isEdit ? '💾 Simpan Perubahan' : '💾 Simpan & Cetak Bukti' }}
        </button>
        <a href="{{ route('pembayaran-lain.bebas.index') }}" class="bebas-btn bebas-btn-light">← Kembali</a>
    </div>
</div>
