<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PpdbEmailVerificationService
{
    private const SESSION_EMAIL = 'ppdb_online_verified_email';
    private const SESSION_ID = 'ppdb_online_email_verification_id';

    public function normalizeEmail(?string $email): string
    {
        return strtolower(trim((string) $email));
    }

    public function sendCode(string $email, ?string $ipAddress): void
    {
        $email = $this->normalizeEmail($email);
        $rateKey = 'ppdb_email_verification_rate_' . sha1($email . '|' . $ipAddress);

        if (!Cache::add($rateKey, true, 60)) {
            throw new \RuntimeException('Kode verifikasi baru saja dikirim. Mohon tunggu sekitar 1 menit sebelum meminta ulang.');
        }

        $code = (string) random_int(100000, 999999);
        $token = Str::random(64);
        $expiresAt = now()->addMinutes(20);

        $id = DB::table('ppdb_email_verifications')->insertGetId([
            'email' => $email,
            'code_hash' => hash('sha256', $code),
            'token_hash' => hash('sha256', $token),
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            Mail::send('emails.ppdb-verification', [
                'code' => $code,
                'verificationUrl' => route('ppdb-online.email-verification.verify', $token),
                'expiresAt' => $expiresAt,
            ], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Kode Verifikasi Email PPDB Online');
            });
        } catch (\Throwable $e) {
            DB::table('ppdb_email_verifications')->where('id', $id)->delete();
            Cache::forget($rateKey);

            throw $e;
        }
    }

    public function confirmCode(string $email, string $code): bool
    {
        $email = $this->normalizeEmail($email);
        $verification = DB::table('ppdb_email_verifications')
            ->where('email', $email)
            ->whereNull('verified_at')
            ->where('expires_at', '>=', now())
            ->orderByDesc('id')
            ->first();

        if (!$verification || !hash_equals((string) $verification->code_hash, hash('sha256', $code))) {
            return false;
        }

        $this->markEmailVerified((int) $verification->id, $email);

        return true;
    }

    public function verifyToken(string $token): bool
    {
        $verification = DB::table('ppdb_email_verifications')
            ->where('token_hash', hash('sha256', $token))
            ->whereNull('verified_at')
            ->where('expires_at', '>=', now())
            ->orderByDesc('id')
            ->first();

        if (!$verification) {
            return false;
        }

        $this->markEmailVerified((int) $verification->id, $verification->email);

        return true;
    }

    public function verifiedEmailFromSession(): ?string
    {
        $email = $this->normalizeEmail(session(self::SESSION_EMAIL));
        $verificationId = (int) session(self::SESSION_ID);

        if ($email === '' || $verificationId <= 0 || !Schema::hasTable('ppdb_email_verifications')) {
            return null;
        }

        $exists = DB::table('ppdb_email_verifications')
            ->where('id', $verificationId)
            ->where('email', $email)
            ->whereNotNull('verified_at')
            ->exists();

        return $exists ? $email : null;
    }

    private function markEmailVerified(int $id, string $email): void
    {
        $email = $this->normalizeEmail($email);

        DB::table('ppdb_email_verifications')
            ->where('id', $id)
            ->update([
                'verified_at' => now(),
                'updated_at' => now(),
            ]);

        session([
            self::SESSION_EMAIL => $email,
            self::SESSION_ID => $id,
        ]);
    }
}
