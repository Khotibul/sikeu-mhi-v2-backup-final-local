<style>
    .siswa-card {
        background: #fff;
        border: 1px solid rgba(15, 118, 110, .14);
        border-radius: 24px;
        padding: 20px;
        box-shadow: 0 18px 55px rgba(15, 23, 42, .08);
        margin-bottom: 18px
    }

    .siswa-section-title {
        margin: 0 0 16px;
        color: var(--tosca-dark, #0f766e);
        font-size: 18px;
        font-weight: 950
    }

    .siswa-form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px
    }

    .siswa-form-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px
    }

    .siswa-field {
        margin-bottom: 14px
    }

    .siswa-field label {
        display: block;
        margin-bottom: 6px;
        color: #475569;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase
    }

    .siswa-control {
        width: 100%;
        min-height: 44px;
        border: 1px solid #d5e1e8;
        border-radius: 14px;
        padding: 10px 12px;
        outline: none;
        font-weight: 750;
        color: #0f172a;
        background: #fff
    }

    .siswa-control:focus {
        border-color: #0f9f8f;
        box-shadow: 0 0 0 4px rgba(15, 159, 143, .12)
    }

    .siswa-control[readonly] {
        background: #f1f5f9;
        color: #0f766e;
        font-weight: 950;
        cursor: not-allowed
    }

    textarea.siswa-control {
        min-height: 95px;
        resize: vertical
    }

    .siswa-note {
        display: block;
        margin-top: 5px;
        color: #64748b;
        font-size: 12px;
        font-weight: 750
    }

    .siswa-btn {
        border: none;
        border-radius: 14px;
        padding: 10px 14px;
        font-weight: 900;
        font-size: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        white-space: nowrap
    }

    .siswa-btn-primary {
        background: linear-gradient(135deg, #0f9f8f, #087c73);
        color: #fff !important;
        box-shadow: 0 12px 25px rgba(15, 118, 110, .2)
    }

    .siswa-btn-light {
        background: #f1f5f9;
        color: #334155 !important;
        border: 1px solid #dbe5ec
    }

    .siswa-form-actions {
        margin-top: 18px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap
    }

    .siswa-alert {
        border-radius: 18px;
        padding: 13px 16px;
        margin-bottom: 14px;
        font-weight: 800
    }

    .siswa-alert-error {
        background: #ffe4e6;
        color: #be123c;
        border: 1px solid #fecdd3
    }

    .siswa-photo-preview {
        margin-top: 8px;
        width: 90px;
        height: 90px;
        border-radius: 18px;
        object-fit: cover;
        border: 1px solid #dbe5ec;
        background: #f8fafc
    }

    @media(max-width:850px) {

        .siswa-form-grid,
        .siswa-form-grid-3 {
            grid-template-columns: 1fr
        }

        .siswa-btn {
            width: 100%
        }
    }
</style>
