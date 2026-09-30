<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - Peminjam</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <!-- NAVBAR -->
    <nav class="bg-blue-600 shadow-sm">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-white text-lg font-semibold">Panel Peminjam</h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('peminjam.katalog') }}"
                   class="border border-white/70 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    Katalog Alat
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-100 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Riwayat & Status Peminjaman Saya</h2>

        @if(session('success'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm">
                            <th class="py-3 px-6 font-semibold">Alat yang Dipinjam</th>
                            <th class="py-3 px-6 font-semibold">Tanggal Pinjam</th>
                            <th class="py-3 px-6 font-semibold">Rencana Kembali</th>
                            <th class="py-3 px-6 font-semibold text-center">Status</th>
                            <th class="py-3 px-6 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                        @forelse($peminjamans as $peminjaman)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6">
                                <ul class="list-disc pl-4 space-y-1">
                                    @foreach($peminjaman->detailPinjam as $detail)
                                        <li>
                                            <span class="font-medium text-gray-900">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                            <span class="text-xs text-gray-500">({{ $detail->jumlah }} unit)</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-4 px-6 text-gray-600">
                                {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d M Y') }}
                            </td>
                            <td class="py-4 px-6 text-gray-600">
                                {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d M Y H:i') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="px-3 py-1.5 text-xs font-semibold rounded-md
                                    @if($peminjaman->status == 'diajukan') bg-amber-100 text-amber-800
                                    @elseif($peminjaman->status == 'dipinjam') bg-blue-100 text-blue-800
                                    @elseif($peminjaman->status == 'menunggu_pengembalian') bg-purple-100 text-purple-800
                                    @elseif($peminjaman->status == 'dikembalikan') bg-emerald-100 text-emerald-800
                                    @elseif($peminjaman->status == 'telat') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ str_replace('_', ' ', $peminjaman->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if(in_array($peminjaman->status, ['dipinjam', 'telat']))
                                    <form action="{{ route('peminjam.kembalikan', $peminjaman->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mengajukan pengembalian alat ini ke Petugas?')">
                                        @csrf
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition">
                                            Kembalikan Alat
                                        </button>
                                    </form>
                                @elseif($peminjaman->status == 'diajukan')
                                    <form action="{{ route('peminjam.batalkan', $peminjaman->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-4 py-2 rounded-lg text-xs font-semibold transition">
                                            Batalkan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 italic">Tidak ada aksi</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-500">
                                <p>Anda belum pernah meminjam alat apapun.</p>
                                <a href="{{ route('peminjam.katalog') }}" class="text-blue-600 hover:underline mt-2 inline-block text-sm font-semibold">Lihat Katalog Alat</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($peminjamans->hasPages())
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $peminjamans->links() }}
            </div>
            @endif
        </div>
    </main>
</body>
</html>