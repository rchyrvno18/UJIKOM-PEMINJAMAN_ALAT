<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // Menampilkan daftar pengajuan peminjaman dari siswa/peminjam
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
        ->where('status', 'diajukan')
        ->when($search, function ($query, $search) {
            return $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('petugas.peminjaman.index', compact('peminjamans', 'search'));
    }

    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            // Diubah menjadi detailPinjam (tanpa 's')
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);
            $peminjaman->update(['status' => 'dipinjam']);

            // Kurangi stok alat secara otomatis
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
            public function tolakPeminjaman($id)
        {
            try {
                $peminjaman = Peminjaman::findOrFail($id);

                if ($peminjaman->status == 'diajukan') {
                    $peminjaman->delete();
                    return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil ditolak.');
                }

                return redirect()->back()->with('error', 'Status peminjaman sudah berubah.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

    // Menampilkan histori pengembalian (read-only, untuk pemantauan petugas)
    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $pengembalians = Pengembalian::with(['peminjaman.user', 'petugas'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('peminjaman.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('petugas.pengembalian.index', compact('pengembalians', 'search'));
    }

    public function createPengembalian()
{
    $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
        ->where('status', 'menunggu_pengembalian')
        ->get();

    return view('petugas.pengembalian.create', compact('peminjamans'));
}

    public function storePengembalian(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'kondisi_kembali' => 'required|string|max:255',
            'denda' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($request->peminjaman_id);

            if ($peminjaman->status !== 'menunggu_pengembalian') {
                return redirect()->back()->with('error', 'Peminjaman ini belum diajukan pengembaliannya oleh peminjam.');
            }

            $telat = now()->startOfDay()->greaterThan(\Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay());

            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now()->toDateString(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $request->denda ?? 0,
                'petugas_id' => auth()->id(),
            ]);

            $peminjaman->update(['status' => $telat ? 'telat' : 'selesai']);

            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->increment('stok', $detail->jumlah);
            }

            DB::commit();
            return redirect()->route('petugas.pengembalian.index')->with('success', 'Pengembalian berhasil dicatat dan stok alat dipulihkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    // Menampilkan halaman cetak laporan
    public function indexLaporan(Request $request)
    {
        $tglMulai = $request->input('tgl_mulai');
        $tglSelesai = $request->input('tgl_selesai');

        $pengembalians = Pengembalian::with(['peminjaman.user', 'petugas'])
            ->when($tglMulai, fn($q, $v) => $q->whereDate('tgl_kembali', '>=', $v))
            ->when($tglSelesai, fn($q, $v) => $q->whereDate('tgl_kembali', '<=', $v))
            ->latest()
            ->get();

        return view('petugas.laporan.index', compact('pengembalians', 'tglMulai', 'tglSelesai'));
    }
}