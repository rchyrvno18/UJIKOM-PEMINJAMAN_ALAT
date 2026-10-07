<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Alat - Peminjam</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-900">

    <!-- NAVBAR -->
    <nav class="bg-blue-600 sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-6 h-16 flex justify-between items-center">
            <h1 class="text-white text-[15px] font-semibold tracking-tight">Panel Peminjam</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('peminjam.riwayat') }}"
                   class="text-white/90 text-sm font-medium px-4 py-2 rounded-full hover:bg-white/10 transition">
                    Riwayat Pinjam
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

    <main class="max-w-6xl mx-auto px-6 py-10 pb-32">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-8">
            <div>
                <h2 class="text-[26px] font-bold text-gray-900 leading-tight">Katalog Alat</h2>
                <p class="text-sm text-gray-500 mt-1">Pilih alat yang ingin kamu pinjam, lalu ajukan di bawah.</p>
            </div>

            <form action="{{ route('peminjam.katalog') }}" method="GET" class="w-full sm:w-72">
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                    </svg>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari nama alat..."
                        class="w-full bg-white border border-gray-200 rounded-full pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
            </form>
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

        <form id="form-ajukan" action="{{ route('peminjam.ajukan') }}" method="POST">
            @csrf

            @if($alats->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 py-20 text-center text-gray-400 text-sm">
                    Tidak ada alat yang tersedia{{ $search ? " untuk pencarian \"$search\"" : '' }}.
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                    @foreach($alats as $alat)
                    @php
                        $kondisiDot = match(strtolower($alat->status_kondisi ?? '')) {
                            'baik' => 'bg-emerald-500',
                            'rusak ringan' => 'bg-amber-500',
                            'rusak berat' => 'bg-red-500',
                            default => 'bg-gray-300',
                        };
                    @endphp
                    <div class="item-card group bg-white rounded-2xl border border-gray-100 overflow-hidden transition hover:shadow-[0_8px_24px_rgba(0,0,0,0.06)] hover:-translate-y-0.5 duration-200 has-[:checked]:ring-2 has-[:checked]:ring-blue-500 has-[:checked]:border-blue-500 has-[:checked]:shadow-[0_8px_24px_rgba(37,99,235,0.12)]">

                        <div class="relative aspect-square bg-gray-50">
                            @if($alat->gambar)
                                <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                            @endif

                            <!-- Checkbox custom (bulat, pojok kanan atas) -->
                            <label class="absolute top-2.5 right-2.5">
                                <input type="checkbox" name="alat_id[]" value="{{ $alat->id }}"
                                       class="chk-item peer sr-only">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-white/90 backdrop-blur border border-gray-200 shadow-sm peer-checked:bg-blue-600 peer-checked:border-blue-600 transition">
                                    <svg class="w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </span>
                            </label>
                        </div>

                        <div class="p-4">
                            <p class="font-semibold text-[13.5px] text-gray-900 leading-snug line-clamp-2 min-h-[2.5em]">
                                {{ $alat->nama_alat }}
                            </p>

                            @if($alat->deskripsi)
                                <p class="text-xs text-gray-400 leading-snug line-clamp-2 mt-1">
                                    {{ $alat->deskripsi }}
                                </p>
                            @endif

                            <div class="flex items-center gap-1.5 mt-2.5 text-xs text-gray-500">
                                <span>{{ $alat->kategori->nama_kategori ?? '-' }}</span>
                                <span class="text-gray-300">&middot;</span>
                                <span class="flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $kondisiDot }}"></span>
                                    {{ $alat->status_kondisi ?? 'Kondisi -' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                                <span class="text-xs text-gray-400">Stok {{ $alat->stok }}</span>

                                <div class="flex items-center border border-gray-200 rounded-full overflow-hidden">
                                    <button type="button" class="qty-minus w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">−</button>
                                    <input type="number" name="jumlah[]" value="1" min="1" max="{{ $alat->stok }}"
                                           class="qty-input w-8 text-center text-xs font-medium border-0 focus:ring-0 p-0">
                                    <button type="button" class="qty-plus w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($alats->hasPages())
                <div class="mt-8">
                    {{ $alats->links() }}
                </div>
                @endif
            @endif

            <!-- BAR BAWAH -->
            @if($alats->isNotEmpty())
            <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur border-t border-gray-100 z-20">
                <div class="max-w-6xl mx-auto px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="flex-1 flex items-center gap-3">
                        <span id="selected-count" class="text-sm text-gray-500 whitespace-nowrap">0 alat dipilih</span>
                        <div class="hidden sm:block w-px h-5 bg-gray-200"></div>
                        <label class="text-sm font-medium text-gray-700 whitespace-nowrap hidden sm:block">Rencana Kembali</label>
                        <input
                            type="date"
                            name="tgl_kembali_plan"
                            form="form-ajukan"
                            required
                            min="{{ now()->toDateString() }}"
                            class="flex-1 sm:flex-none sm:w-52 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                    <button type="submit" form="form-ajukan"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-full text-sm font-semibold transition">
                        Ajukan Peminjaman
                    </button>
                </div>
            </div>
            @endif
        </form>
    </main>

    <script>
        // Stepper +/- untuk jumlah pinjam
        document.querySelectorAll('.item-card').forEach(card => {
            const input = card.querySelector('.qty-input');
            const max = parseInt(input.max || 9999);

            card.querySelector('.qty-plus').addEventListener('click', () => {
                input.value = Math.min(max, (parseInt(input.value) || 1) + 1);
            });
            card.querySelector('.qty-minus').addEventListener('click', () => {
                input.value = Math.max(1, (parseInt(input.value) || 1) - 1);
            });
        });

        // Hitung jumlah alat yang dipilih
        const checkboxes = document.querySelectorAll('.chk-item');
        const counter = document.getElementById('selected-count');
        function updateCount() {
            const total = document.querySelectorAll('.chk-item:checked').length;
            counter.textContent = total + ' alat dipilih';
        }
        checkboxes.forEach(chk => chk.addEventListener('change', updateCount));
    </script>
</body>
</html>