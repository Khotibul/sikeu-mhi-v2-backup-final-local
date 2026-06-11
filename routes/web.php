<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KelasFormalController;
use App\Http\Controllers\KelasDiniyahController;
use App\Http\Controllers\PembayaranSppController;
use App\Http\Controllers\PembayaranLainController;
use App\Http\Controllers\PemasukanLainController;
use App\Http\Controllers\JenisPembayaranController;
use App\Http\Controllers\TunggakanController;
use App\Http\Controllers\LaporanPemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\RiwayatTransaksiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PpdbController;
use App\Http\Controllers\PpdbOnlineController;

/*
|--------------------------------------------------------------------------
| Redirect Awal
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return session()->has('admin_id')
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| PPDB Online Publik
|--------------------------------------------------------------------------
*/

Route::get('/ppdb-online', [PpdbOnlineController::class, 'form'])
    ->name('ppdb-online.form');

Route::post('/ppdb-online', [PpdbOnlineController::class, 'submit'])
    ->name('ppdb-online.submit');

Route::get('/ppdb-online/sukses/{id}', [PpdbOnlineController::class, 'sukses'])
    ->name('ppdb-online.sukses');

Route::get('/ppdb_register.php', function () {
    return redirect()->route('ppdb-online.form');
})->name('ppdb-online.legacy');

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');

/*
|--------------------------------------------------------------------------
| Area Admin
|--------------------------------------------------------------------------
*/

Route::middleware('admin.auth')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Dashboard & Logout
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:dashboard')
        ->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Atur Admin - Superadmin Saja
    |--------------------------------------------------------------------------
    */

    Route::resource('atur-admin', AdminController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('role:admin.manage');

    /*
    |--------------------------------------------------------------------------
    | Data Santri
    |--------------------------------------------------------------------------
    */

    Route::get('/siswa/export/csv', [SiswaController::class, 'exportCsv'])
        ->middleware('role:siswa.view')
        ->name('siswa.export');

    Route::get('/siswa/import/template', [SiswaController::class, 'templateImport'])
        ->middleware('role:siswa.create')
        ->name('siswa.template-import');

    Route::post('/siswa/import/csv', [SiswaController::class, 'importCsv'])
        ->middleware('role:siswa.create')
        ->name('siswa.import');

    Route::get('/siswa', [SiswaController::class, 'index'])
        ->middleware('role:siswa.view')
        ->name('siswa.index');

    Route::get('/siswa/create', [SiswaController::class, 'create'])
        ->middleware('role:siswa.create')
        ->name('siswa.create');

    Route::post('/siswa', [SiswaController::class, 'store'])
        ->middleware('role:siswa.create')
        ->name('siswa.store');

    Route::get('/siswa/{id}/edit', [SiswaController::class, 'edit'])
        ->middleware('role:siswa.edit')
        ->name('siswa.edit');

    Route::put('/siswa/{id}', [SiswaController::class, 'update'])
        ->middleware('role:siswa.edit')
        ->name('siswa.update');

    Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])
        ->middleware('role:siswa.delete')
        ->name('siswa.destroy');

    /*
    |--------------------------------------------------------------------------
    | Kelas Formal, Kelas Diniyah, Jenis Pembayaran
    |--------------------------------------------------------------------------
    */

    Route::get('/kelas-formal', [KelasFormalController::class, 'index'])
        ->middleware('role:kelas.view')
        ->name('kelas-formal.index');

    Route::get('/kelas-formal/create', [KelasFormalController::class, 'create'])
        ->middleware('role:kelas.manage')
        ->name('kelas-formal.create');

    Route::post('/kelas-formal', [KelasFormalController::class, 'store'])
        ->middleware('role:kelas.manage')
        ->name('kelas-formal.store');

    Route::get('/kelas-formal/{kelas_formal}/edit', [KelasFormalController::class, 'edit'])
        ->middleware('role:kelas.manage')
        ->name('kelas-formal.edit');

    Route::put('/kelas-formal/{kelas_formal}', [KelasFormalController::class, 'update'])
        ->middleware('role:kelas.manage')
        ->name('kelas-formal.update');

    Route::delete('/kelas-formal/{kelas_formal}', [KelasFormalController::class, 'destroy'])
        ->middleware('role:kelas.manage')
        ->name('kelas-formal.destroy');

    Route::get('/kelas-diniyah', [KelasDiniyahController::class, 'index'])
        ->middleware('role:kelas.view')
        ->name('kelas-diniyah.index');

    Route::get('/kelas-diniyah/create', [KelasDiniyahController::class, 'create'])
        ->middleware('role:kelas.manage')
        ->name('kelas-diniyah.create');

    Route::post('/kelas-diniyah', [KelasDiniyahController::class, 'store'])
        ->middleware('role:kelas.manage')
        ->name('kelas-diniyah.store');

    Route::get('/kelas-diniyah/{kelas_diniyah}/edit', [KelasDiniyahController::class, 'edit'])
        ->middleware('role:kelas.manage')
        ->name('kelas-diniyah.edit');

    Route::put('/kelas-diniyah/{kelas_diniyah}', [KelasDiniyahController::class, 'update'])
        ->middleware('role:kelas.manage')
        ->name('kelas-diniyah.update');

    Route::delete('/kelas-diniyah/{kelas_diniyah}', [KelasDiniyahController::class, 'destroy'])
        ->middleware('role:kelas.manage')
        ->name('kelas-diniyah.destroy');

    Route::get('/jenis-pembayaran', [JenisPembayaranController::class, 'index'])
        ->middleware('role:jenis-pembayaran.view')
        ->name('jenis-pembayaran.index');

    Route::post('/jenis-pembayaran', [JenisPembayaranController::class, 'store'])
        ->middleware('role:jenis-pembayaran.manage')
        ->name('jenis-pembayaran.store');

    Route::put('/jenis-pembayaran/{jenis_pembayaran}', [JenisPembayaranController::class, 'update'])
        ->middleware('role:jenis-pembayaran.manage')
        ->name('jenis-pembayaran.update');

    Route::delete('/jenis-pembayaran/{jenis_pembayaran}', [JenisPembayaranController::class, 'destroy'])
        ->middleware('role:jenis-pembayaran.manage')
        ->name('jenis-pembayaran.destroy');

    /*
    |--------------------------------------------------------------------------
    | Pembayaran SPP Formal & Pondok/Diniyah
    |--------------------------------------------------------------------------
    */

    Route::prefix('pembayaran-spp')
        ->name('pembayaran-spp.')
        ->group(function () {
            Route::get('/', [PembayaranSppController::class, 'index'])
                ->middleware('role:pembayaran.view')
                ->name('index');

            Route::get('/siswa/{id}', [PembayaranSppController::class, 'pilihSiswa'])
                ->middleware('role:pembayaran.view')
                ->name('siswa');

            Route::post('/siswa/{id}/formal', [PembayaranSppController::class, 'bayarFormal'])
                ->middleware('role:pembayaran.manage')
                ->name('bayar-formal');

            Route::post('/siswa/{id}/pondok', [PembayaranSppController::class, 'bayarPondok'])
                ->middleware('role:pembayaran.manage')
                ->name('bayar-pondok');

            /*
             | Route ini yang dipakai di Blade:
             | route('pembayaran-spp.siswa.bayar-gabungan', $siswa->id_siswa)
             |
             | Karena sudah ada prefix name('pembayaran-spp.'), nama di sini cukup:
             | ->name('siswa.bayar-gabungan')
             */
            Route::post('/siswa/{id}/bayar-gabungan', [PembayaranSppController::class, 'bayarGabungan'])
                ->middleware('role:pembayaran.manage')
                ->name('siswa.bayar-gabungan');

            Route::get('/siswa/{id}/cetak-gabungan-tanggal', [PembayaranSppController::class, 'cetakGabunganTanggal'])
                ->middleware('role:pembayaran.view,pembayaran.manage')
                ->name('siswa.cetak-gabungan-tanggal');

            Route::get('/kwitansi/cetak', [PembayaranSppController::class, 'kwitansi'])
                ->middleware('role:pembayaran.view')
                ->name('kwitansi');

            Route::get('/kwitansi/formal/{id}', [PembayaranSppController::class, 'kwitansiFormal'])
                ->middleware('role:pembayaran.view')
                ->name('kwitansi-formal');

            Route::get('/kwitansi/pondok/{id}', [PembayaranSppController::class, 'kwitansiPondok'])
                ->middleware('role:pembayaran.view')
                ->name('kwitansi-pondok');

            Route::post('/kwitansi/gabungan', [PembayaranSppController::class, 'kwitansiGabungan'])
                ->middleware('role:pembayaran.view')
                ->name('kwitansi-gabungan');

            Route::delete('/formal/{id}', [PembayaranSppController::class, 'hapusFormal'])
                ->middleware('role:pembayaran.delete')
                ->name('hapus-formal');

            Route::delete('/pondok/{id}', [PembayaranSppController::class, 'hapusPondok'])
                ->middleware('role:pembayaran.delete')
                ->name('hapus-pondok');
        });

    /*
    |--------------------------------------------------------------------------
    | Pembayaran Lain Santri + Setoran Bebas
    |--------------------------------------------------------------------------
    |
    | /pembayaran-lain        = Tagihan tetap santri
    | /pembayaran-lain/bebas  = Setoran bebas/infaq/kitab satuan
    |
    */

    Route::prefix('pembayaran-lain')
        ->name('pembayaran-lain.')
        ->group(function () {
            /*
            |--------------------------------------------------------------------------
            | Setoran Bebas
            |--------------------------------------------------------------------------
            */

            Route::get('/bebas', [PemasukanLainController::class, 'index'])
                ->middleware('role:pembayaran.view,setoran-bebas.view')
                ->name('bebas.index');

            Route::get('/bebas/create', [PemasukanLainController::class, 'create'])
                ->middleware('role:pembayaran.manage,setoran-bebas.manage')
                ->name('bebas.create');

            Route::post('/bebas', [PemasukanLainController::class, 'store'])
                ->middleware('role:pembayaran.manage,setoran-bebas.manage')
                ->name('bebas.store');

            Route::get('/bebas/{id}/edit', [PemasukanLainController::class, 'edit'])
                ->middleware('role:pembayaran.manage,setoran-bebas.manage')
                ->name('bebas.edit');

            Route::put('/bebas/{id}', [PemasukanLainController::class, 'update'])
                ->middleware('role:pembayaran.manage,setoran-bebas.manage')
                ->name('bebas.update');

            Route::delete('/bebas/{id}', [PemasukanLainController::class, 'destroy'])
                ->middleware('role:pembayaran.delete,setoran-bebas.manage')
                ->name('bebas.destroy');

            Route::get('/bebas/{id}/cetak', [PemasukanLainController::class, 'cetak'])
                ->middleware('role:pembayaran.view,setoran-bebas.view')
                ->name('bebas.cetak');

            /*
            |--------------------------------------------------------------------------
            | Tagihan Tetap Santri
            |--------------------------------------------------------------------------
            */

            Route::get('/', [PembayaranLainController::class, 'index'])
                ->middleware('role:pembayaran.view')
                ->name('index');

            Route::get('/siswa/{id}', [PembayaranLainController::class, 'siswa'])
                ->middleware('role:pembayaran.view')
                ->name('siswa');

            Route::post('/siswa/{id}/bayar', [PembayaranLainController::class, 'bayar'])
                ->middleware('role:pembayaran.manage')
                ->name('bayar');

            Route::delete('/hapus/{id}', [PembayaranLainController::class, 'hapus'])
                ->middleware('role:pembayaran.delete')
                ->name('hapus');

            Route::get('/kwitansi/{id}', [PembayaranLainController::class, 'kwitansi'])
                ->middleware('role:pembayaran.view')
                ->name('kwitansi');
        });

    /*
    |--------------------------------------------------------------------------
    | Laporan & Tunggakan
    |--------------------------------------------------------------------------
    */

    Route::get('/tunggakan', [TunggakanController::class, 'index'])
        ->middleware('role:tunggakan.view')
        ->name('tunggakan.index');

    Route::get('/laporan-pemasukan', [LaporanPemasukanController::class, 'index'])
        ->middleware('role:laporan.view')
        ->name('laporan-pemasukan.index');

    /*
    |--------------------------------------------------------------------------
    | Pengeluaran
    |--------------------------------------------------------------------------
    */

    Route::get('/pengeluaran', [PengeluaranController::class, 'index'])
        ->middleware('role:pengeluaran.view')
        ->name('pengeluaran.index');

    Route::get('/pengeluaran/create', [PengeluaranController::class, 'create'])
        ->middleware('role:pengeluaran.manage')
        ->name('pengeluaran.create');

    Route::post('/pengeluaran', [PengeluaranController::class, 'store'])
        ->middleware('role:pengeluaran.manage')
        ->name('pengeluaran.store');

    Route::get('/pengeluaran/{pengeluaran}/edit', [PengeluaranController::class, 'edit'])
        ->middleware('role:pengeluaran.manage')
        ->name('pengeluaran.edit');

    Route::put('/pengeluaran/{pengeluaran}', [PengeluaranController::class, 'update'])
        ->middleware('role:pengeluaran.manage')
        ->name('pengeluaran.update');

    Route::delete('/pengeluaran/{pengeluaran}', [PengeluaranController::class, 'destroy'])
        ->middleware('role:pengeluaran.delete')
        ->name('pengeluaran.destroy');

    Route::get('/pengeluaran/{id}/cetak', [PengeluaranController::class, 'cetak'])
        ->middleware('role:pengeluaran.view')
        ->name('pengeluaran.cetak');

    /*
    |--------------------------------------------------------------------------
    | Riwayat Transaksi Global
    |--------------------------------------------------------------------------
    */

    Route::prefix('riwayat-transaksi')
        ->name('riwayat-transaksi.')
        ->group(function () {
            Route::get('/', [RiwayatTransaksiController::class, 'index'])
                ->middleware('role:riwayat.view')
                ->name('index');

            Route::delete('/hapus-satuan', [RiwayatTransaksiController::class, 'hapusSatuan'])
                ->middleware('role:riwayat.delete')
                ->name('hapus-satuan');

            Route::delete('/hapus-banyak', [RiwayatTransaksiController::class, 'hapusBanyak'])
                ->middleware('role:riwayat.delete')
                ->name('hapus-banyak');
        });

    /*
    |--------------------------------------------------------------------------
    | PPDB Admin
    |--------------------------------------------------------------------------
    */

    Route::get('/ppdb', [PpdbController::class, 'index'])
        ->middleware('role:ppdb.view')
        ->name('ppdb.index');

    Route::post('/ppdb', [PpdbController::class, 'store'])
        ->middleware('role:ppdb.manage')
        ->name('ppdb.store');

    Route::put('/ppdb/{id}', [PpdbController::class, 'update'])
        ->middleware('role:ppdb.manage')
        ->name('ppdb.update');

    Route::delete('/ppdb/{id}', [PpdbController::class, 'destroy'])
        ->middleware('role:ppdb.manage')
        ->name('ppdb.destroy');

    Route::get('/ppdb/{id}/cetak', [PpdbController::class, 'cetak'])
        ->middleware('role:ppdb.view')
        ->name('ppdb.cetak');

    Route::post('/ppdb/{id}/terima-santri', [PpdbController::class, 'terimaSantri'])
        ->middleware('role:ppdb.manage')
        ->name('ppdb.terima-santri');

    Route::get('/ppdb/export/data', [PpdbController::class, 'export'])
        ->middleware('role:ppdb.view')
        ->name('ppdb.export');

    Route::get('/ppdb/{id}/berkas/{field}', [PpdbController::class, 'berkas'])
        ->middleware(['admin.auth', 'role:ppdb.view,ppdb.manage'])
        ->name('ppdb.berkas');
});
