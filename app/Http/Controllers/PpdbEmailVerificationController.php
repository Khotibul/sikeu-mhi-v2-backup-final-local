<?php

namespace App\Http\Controllers;

use App\Services\PpdbEmailVerificationService;
use Illuminate\Http\Request;

class PpdbEmailVerificationController extends Controller
{
    public function __construct(private readonly PpdbEmailVerificationService $emailVerification)
    {
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email:rfc|max:190',
        ], [
            'email.required' => 'Email wajib diisi untuk membuka formulir PPDB.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $email = $this->emailVerification->normalizeEmail($validated['email']);

        try {
            $this->emailVerification->sendCode($email, $request->ip());
        } catch (\RuntimeException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Email verifikasi belum berhasil dikirim: ' . $e->getMessage());
        }

        return back()
            ->withInput(['email' => $email])
            ->with('verification_email', $email)
            ->with('success', 'Kode verifikasi sudah dikirim ke ' . $email . '. Masukkan kode atau klik link di email tersebut.');
    }

    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email:rfc|max:190',
            'verification_code' => 'required|digits:6',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'verification_code.required' => 'Kode verifikasi wajib diisi.',
            'verification_code.digits' => 'Kode verifikasi harus 6 digit.',
        ]);

        $email = $this->emailVerification->normalizeEmail($validated['email']);

        if (!$this->emailVerification->confirmCode($email, $validated['verification_code'])) {
            return back()
                ->withInput(['email' => $email])
                ->with('verification_email', $email)
                ->with('error', 'Kode verifikasi salah atau sudah kedaluwarsa.');
        }

        return redirect()
            ->route('ppdb-online.form')
            ->with('success', 'Email berhasil diverifikasi. Formulir PPDB sudah bisa diisi.');
    }

    public function verify(string $token)
    {
        if (!$this->emailVerification->verifyToken($token)) {
            return redirect()
                ->route('ppdb-online.form')
                ->with('error', 'Link verifikasi tidak valid atau sudah kedaluwarsa.');
        }

        return redirect()
            ->route('ppdb-online.form')
            ->with('success', 'Email berhasil diverifikasi. Formulir PPDB sudah bisa diisi.');
    }
}
