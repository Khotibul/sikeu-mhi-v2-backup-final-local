<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kode Verifikasi PPDB Online</title>
</head>

<body style="margin:0; padding:0; background:#f4f8fb; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <div style="max-width:560px; margin:0 auto; padding:28px 16px;">
        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:18px; overflow:hidden;">
            <div style="background:#0f766e; color:#ffffff; padding:22px;">
                <h1 style="margin:0; font-size:22px;">Verifikasi Email PPDB Online</h1>
                <p style="margin:8px 0 0; font-size:14px; line-height:1.6;">
                    Yayasan Pendidikan Pesantren Mamba'ul Khoiriyatil Islamiyah
                </p>
            </div>

            <div style="padding:24px;">
                <p style="margin:0 0 14px; font-size:15px; line-height:1.7;">
                    Gunakan kode berikut untuk membuka formulir PPDB online:
                </p>

                <div style="margin:18px 0; padding:18px; background:#e7faf7; border:1px dashed #0f9f92; border-radius:14px; text-align:center;">
                    <div style="font-size:32px; font-weight:800; letter-spacing:8px; color:#0f766e;">
                        {{ $code }}
                    </div>
                </div>

                <p style="margin:0 0 18px; font-size:14px; line-height:1.7;">
                    Atau klik tombol berikut untuk verifikasi otomatis:
                </p>

                <p style="margin:0 0 18px;">
                    <a href="{{ $verificationUrl }}"
                        style="display:inline-block; background:#e3456d; color:#ffffff; text-decoration:none; padding:13px 18px; border-radius:12px; font-weight:700;">
                        Verifikasi Email
                    </a>
                </p>

                <p style="margin:0; font-size:13px; line-height:1.7; color:#64748b;">
                    Kode dan link ini berlaku sampai {{ $expiresAt->format('d-m-Y H:i') }}.
                    Abaikan email ini jika Anda tidak meminta verifikasi PPDB.
                </p>
            </div>
        </div>
    </div>
</body>

</html>
