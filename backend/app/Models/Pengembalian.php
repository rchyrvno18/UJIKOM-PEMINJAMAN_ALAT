<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Pengembalian extends Model
{
    protected $table = 'pengembalian';

    protected $fillable = [
        'peminjaman_id', 'tgl_kembali', 'kondisi_kembali', 'denda', 'petugas_id'
    ];

    protected function casts(): array {
        return [
            'tgl_kembali' => 'date:Y-m-d',
            'denda' => 'integer',
        ];
    }
    public function peminjaman(): BelongsTo {
        return $this->belongsTo(Peminjaman::class);
    }

    public function petugas(): BelongsTo {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function getHariTerlambatAttribute(): int
    {
        $rencana = $this->peminjaman?->tgl_kembali_plan;

        if (!$rencana || !$this->tgl_kembali) {
            return 0;
        }

        $selisihDetik = Carbon::parse($this->tgl_kembali)->startOfDay()->timestamp
                    - Carbon::parse($rencana)->startOfDay()->timestamp;

        return max(0, intdiv($selisihDetik, 86400));
    }
} 