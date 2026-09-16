<?php

namespace App\Observers;

use App\Models\Alat;
use App\Models\LogAktivitas;
use Illuminate\Support\Facedes\Auth;

class AlatObserver
{
    /**
     * Handle the Alat "created" event.
     */
    public function catatLog(string $pesan): void
    {
        if (Auth::check()) {
            LogAktivitas::create([
                'user_id'  => Auth::id(),
                'aktivitas' => $pesan,
            ]);
        }
    }

    /**
     * Handle the Alat "updated" event.
     */
    public function created(Alat $alat): void
    {
        $this->catatLog("Menambahkan master data alat baru: ($alat->nama_alat) (ID: {$alat->id})");
    }

    /**
     * Handle the Alat "deleted" event.
     */
    public function updated(Alat $alat): void
    {
        $perubahanArray     =     array_diff(array_keys($alat->getChanges()), ['updated_at']);

        if (!empty($perubahanArray)) {
            $perubahan = implode(', ', $perubahanArray);
            $this->catatLog("Memperbarui data alat  '{$alat->nama_alat}'(Kolom yang diubah: {$perubahan})");
        }
    }
    
    public function deleted(Alat $alat): void
    {
        $this->catatLog("Menghapus master data alat: {$alat->nama_alat} (ID: {$alat->id})");
    }
}
