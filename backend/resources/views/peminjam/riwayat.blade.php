<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - Peminjam</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-900">

    <!-- NAVBAR -->
    <nav class="bg-blue-600 sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-6 h-16 flex justify-between items-center">
            <h1 class="text-white text-[15px] font-semibold tracking-tight">Panel Peminjam</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('peminjam.katalog') }}"
                   class="text-white/90 text-sm font-medium px-4 py-2 rounded-full hover:bg-white/10 transition">
                    Katalog Alat
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-white text-blue-600 px-4 py-2 rounded-full text-sm font-semibold hover:bg-blue-50 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-10">

        <div class="mb-8">
            <h2 class="text-[26px] font-bold text-gray-900 leading-tight">Riwayat Peminjaman</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar pengajuan dan status peminjaman alat kamu.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-800 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-100 text-red-800 px-4 py-3 rounded-xl text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-gray-400 text-xs uppercase tracking-wide border-b border-gray-100">
                            <th class="py-3.5 px-6 font-medium">Alat yang Dipinjam</th>
                            <th class="py-3.5 px-6 font-medium">Tanggal Pinjam</th>
                            <th class="py-3.5 px-6 font-medium">Rencana Kembali</th>
                            <th class="py-3.5 px-6 font-medium text-center">Status</th>
                            <th class="py-3.5 px-6 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @forelse($peminjamans as $peminjaman)
                        @php
                            $statusStyle = match($peminjaman->status) {
                                'diajukan' => 'bg-amber-50 text-amber-700',
                                'dipinjam' => 'bg-blue-50 text-blue-700',
                                'menunggu_pengembalian' => 'bg-purple-50 text-purple-700',
                                'dikembalikan', 'selesai' => 'bg-emerald-50 text-emerald-700',
                                'telat' => 'bg-red-50 text-red-700',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="py-4 px-6">
                                <ul class="space-y-1">
                                    @foreach($peminjaman->detailPinjam as $detail)
                                        <li class="text-gray-800">
                                            <span class="font-medium">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                            <span class="text-xs text-gray-400">({{ $detail->jumlah }} unit)</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-4 px-6 text-gray-500">
                                {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d M Y') }}
                            </td>
                            <td class="py-4 px-6 text-gray-500">
                                {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d M Y') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-block px-3 py-1 text-xs font-medium rounded-full {{ $statusStyle }}">
                                    {{ ucwords(str_replace('_', ' ', $peminjaman->status)) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if(in_array($peminjaman->status, ['dipinjam', 'telat']))
                                    <form action="{{ route('peminjam.kembalikan', $peminjaman->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mengajukan pengembalian alat ini ke Petugas?')">
                                        @csrf
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full text-xs font-semibold transition">
                                            Kembalikan Alat
                                        </button>
                                    </form>
                                @elseif($peminjaman->status == 'diajukan')
                                    <form action="{{ route('peminjam.batalkan', $peminjaman->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-full text-xs font-semibold transition">
                                            Batalkan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">Tidak ada aksi</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center text-gray-400 text-sm">
                                <p>Kamu belum pernah meminjam alat apapun.</p>
                                <a href="{{ route('peminjam.katalog') }}" class="text-blue-600 hover:underline mt-2 inline-block font-medium">
                                    Lihat Katalog Alat
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($peminjamans->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $peminjamans->links() }}
            </div>
            @endif
        </div>
    </main>
</body>
</html>