<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Alat - Peminjam</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <!-- NAVBAR -->
    <nav class="bg-blue-600 shadow-sm sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-white text-lg font-semibold">Panel Peminjam</h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('peminjam.riwayat') }}"
                   class="border border-white/70 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    Riwayat Pinjam
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

    <main class="max-w-6xl mx-auto px-6 py-8 pb-28">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Katalog Alat Tersedia</h2>

            <form action="{{ route('peminjam.katalog') }}" method="GET" class="flex gap-2 w-full sm:w-auto">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama alat..."
                    class="flex-1 sm:w-64 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Cari
                </button>
            </form>
        </div>

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

        <!-- Form membungkus seluruh grid + bar bawah -->
        <form id="form-ajukan" action="{{ route('peminjam.ajukan') }}" method="POST">
            @csrf

            @if($alats->isEmpty())
                <div class="bg-white rounded-xl border border-gray-200 py-16 text-center text-gray-500">
                    Tidak ada alat yang tersedia{{ $search ? " untuk pencarian \"$search\"" : '' }}.
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($alats as $alat)
                    <label class="relative bg-white rounded-xl border border-gray-200 hover:shadow-md transition overflow-hidden flex flex-col cursor-pointer has-[:checked]:ring-2 has-[:checked]:ring-blue-500 has-[:checked]:border-blue-500">

                        <!-- Checkbox pilih (pojok kiri atas) -->
                        <input type="checkbox" name="alat_id[]" value="{{ $alat->id }}"
                               class="peer absolute top-3 left-3 z-10 w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                        <!-- Gambar / placeholder alat -->
                        <div class="aspect-square bg-gray-100 flex items-center justify-center">
                            @if($alat->gambar)
                                <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            @endif
                        </div>

                        <div class="p-3 flex flex-col flex-1">
                            <p class="font-semibold text-gray-900 text-sm leading-snug line-clamp-2 mb-2">
                                {{ $alat->nama_alat }}
                            </p>

                            <div class="flex flex-wrap gap-1.5 mb-2">
                                <span class="text-[11px] font-medium bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">
                                    {{ $alat->kategori->nama_kategori ?? '-' }}
                                </span>
                                <span class="text-[11px] font-medium bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                    Stok {{ $alat->stok }}
                                </span>
                            </div>

                            <div class="mt-auto flex items-center justify-between gap-2 pt-2 border-t border-gray-100">
                                <span class="text-xs text-gray-500">Jumlah</span>
                                <input
                                    type="number"
                                    name="jumlah[]"
                                    min="1"
                                    max="{{ $alat->stok }}"
                                    value="1"
                                    onclick="event.preventDefault()"
                                    class="w-16 border border-gray-300 rounded-lg px-2 py-1 text-sm text-center focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>

                @if($alats->hasPages())
                <div class="mt-6">
                    {{ $alats->links() }}
                </div>
                @endif
            @endif

            <!-- BAR BAWAH (sticky, kaya checkout Shopee) -->
            @if($alats->isNotEmpty())
            <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-[0_-2px_10px_rgba(0,0,0,0.06)] z-20">
                <div class="max-w-6xl mx-auto px-6 py-3 flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="flex items-center gap-2 flex-1">
                        <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Rencana Kembali:</label>
                        <input
                            type="datetime-local"
                            name="tgl_kembali_plan"
                            form="form-ajukan"
                            required
                            min="{{ now()->format('Y-m-d\TH:i') }}"
                            class="flex-1 sm:flex-none sm:w-56 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                    <button type="submit" form="form-ajukan"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                        Ajukan Peminjaman
                    </button>
                </div>
            </div>
            @endif
        </form>
    </main>
</body>
</html>