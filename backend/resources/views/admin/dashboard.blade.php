@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Peminjaman Alat')
@section('header-title', 'Halaman Dashboard Admin')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
    #gudang-wrap .f-display { font-family: 'Barlow Condensed', sans-serif; letter-spacing: 0.01em; }
    #gudang-wrap .f-data { font-family: 'IBM Plex Mono', monospace; }
    #gudang-wrap .stat-icon { width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;
                               background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; }
</style>

<div id="gudang-wrap">

    <div class="mb-6 px-4 py-3 text-sm flex items-center justify-between bg-white border border-gray-200 rounded-lg">
        <span class="text-gray-700">Selamat datang, <strong class="text-gray-900">{{ auth()->user()->name }}</strong> — masuk sebagai
            <span class="f-data text-xs text-gray-500">{{ strtoupper(auth()->user()->role) }}</span>
        </span>
        <span class="f-data text-xs text-gray-400">{{ now()->translatedFormat('d M Y, H:i') }}</span>
    </div>

    <!-- HERO: Ketersediaan Stok -->
    @php
        $stokTersedia = max($totalAlat - $alatStokHabis, 0);
        $persenTersedia = $totalAlat > 0 ? round(($stokTersedia / $totalAlat) * 100) : 0;
    @endphp
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6 px-6 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
        <div>
            <div class="text-xs uppercase tracking-wide f-data text-gray-400">Ketersediaan Alat Saat Ini</div>
            <div class="f-display font-bold mt-1 text-gray-900" style="font-size: 56px; line-height: 1;">
                {{ $persenTersedia }}<span class="text-blue-600" style="font-size: 28px;">%</span>
            </div>
            <div class="text-sm mt-1 text-gray-500">
                {{ $stokTersedia }} dari {{ $totalAlat }} jenis alat masih punya stok tersedia
            </div>
        </div>
        <div class="w-full sm:w-72">
            <div class="h-3 w-full overflow-hidden rounded-full bg-gray-100">
                <div class="h-full rounded-full bg-emerald-500" style="width: {{ $persenTersedia }}%;"></div>
            </div>
            <div class="flex justify-between text-xs mt-2 f-data">
                <span class="text-emerald-600">Tersedia</span>
                <span class="{{ $alatStokHabis > 0 ? 'text-red-600' : 'text-gray-400' }}">Habis ({{ $alatStokHabis }})</span>
            </div>
        </div>
    </div>

    <!-- MANIFEST STRIP -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-8 overflow-hidden">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6">
            @php
                $stats = [
                    ['label' => 'User Terdaftar', 'value' => $totalUser, 'alert' => false,
                     'icon' => '<path d="M12 12a4 4 0 100-8 4 4 0 000 8zM4 20c0-4 3.5-6 8-6s8 2 8 6" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linecap="round"/>'],
                    ['label' => 'Total Alat', 'value' => $totalAlat, 'alert' => false,
                     'icon' => '<path d="M3 7l9-4 9 4-9 4-9-4zM3 7v10l9 4 9-4V7" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linejoin="round"/>'],
                    ['label' => 'Kategori', 'value' => $totalKategori, 'alert' => false,
                     'icon' => '<path d="M4 4h7v7H4V4zm9 0h7v7h-7V4zM4 13h7v7H4v-7zm9 0h7v7h-7v-7z" stroke="currentColor" stroke-width="1.6" fill="none"/>'],
                    ['label' => 'Sedang Dipinjam', 'value' => $peminjamanAktif, 'alert' => false,
                     'icon' => '<path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linecap="round" stroke-linejoin="round"/>'],
                    ['label' => 'Menunggu Persetujuan', 'value' => $peminjamanDiajukan, 'alert' => $peminjamanDiajukan > 0, 'color' => 'amber',
                     'icon' => '<circle cx="12" cy="12" r="8.2" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="M12 7.5V12l3 2" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linecap="round"/>'],
                    ['label' => 'Stok Habis', 'value' => $alatStokHabis, 'alert' => $alatStokHabis > 0, 'color' => 'red',
                     'icon' => '<path d="M12 4l9 16H3L12 4z" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linejoin="round"/><path d="M12 10v4M12 17h.01" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>'],
                ];
            @endphp
            @foreach($stats as $i => $stat)
                @php $c = $stat['color'] ?? 'gray'; @endphp
                <div class="px-5 py-5 {{ $i > 0 ? 'border-l border-gray-100' : '' }} flex items-start gap-3">
                    <div class="stat-icon flex-shrink-0 {{ $stat['alert'] ? 'text-'.$c.'-600' : 'text-gray-400' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24">{!! $stat['icon'] !!}</svg>
                    </div>
                    <div>
                        <div class="f-data text-2xl font-semibold {{ $stat['alert'] ? 'text-'.$c.'-600' : 'text-gray-800' }}">
                            {{ str_pad($stat['value'], 2, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="text-xs mt-0.5 text-gray-500">{{ $stat['label'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- PERLU PERHATIAN -->
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="f-display text-xl text-gray-900">Perlu Perhatian</h3>
                <span class="f-data text-xs text-gray-400">live</span>
            </div>
            <div class="divide-y divide-gray-100">
                @php
                    $items = [
                        ['label' => 'Alat dengan stok habis', 'desc' => 'Perlu restok atau tunggu pengembalian',
                         'value' => $alatStokHabis, 'active' => $alatStokHabis > 0, 'color' => 'red',
                         'icon' => '<path d="M12 4l9 16H3L12 4z" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linejoin="round"/><path d="M12 10v4M12 17h.01" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>'],
                        ['label' => 'Pengajuan menunggu persetujuan', 'desc' => 'Diteruskan ke petugas untuk diproses',
                         'value' => $peminjamanDiajukan, 'active' => $peminjamanDiajukan > 0, 'color' => 'amber',
                         'icon' => '<circle cx="12" cy="12" r="8.2" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="M12 7.5V12l3 2" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linecap="round"/>'],
                        ['label' => 'Alat sedang di luar (dipinjam)', 'desc' => 'Termasuk yang sudah lewat jatuh tempo',
                         'value' => $peminjamanAktif, 'active' => $peminjamanAktif > 0, 'color' => 'blue',
                         'icon' => '<path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linecap="round" stroke-linejoin="round"/>'],
                    ];
                @endphp
                @foreach($items as $item)
                @php $c = $item['color']; @endphp
                <div class="px-5 py-4 flex items-center justify-between border-l-4 {{ $item['active'] ? 'border-'.$c.'-500' : 'border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <div class="stat-icon {{ $item['active'] ? 'text-'.$c.'-600' : 'text-gray-400' }}">
                            <svg width="16" height="16" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                        </div>
                        <div>
                            <div class="text-sm text-gray-800 {{ $item['active'] ? 'font-semibold' : '' }}">{{ $item['label'] }}</div>
                            <div class="text-xs mt-0.5 text-gray-400">{{ $item['desc'] }}</div>
                        </div>
                    </div>
                    <div class="f-data text-xl font-semibold {{ $item['active'] ? 'text-'.$c.'-600' : 'text-gray-400' }}">
                        {{ $item['value'] }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- CATATAN / SHORTCUT -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm px-5 py-5 flex flex-col justify-between">
            <div>
                <div class="stat-icon mb-3 text-gray-400">
                    <svg width="18" height="18" viewBox="0 0 24 24"><path d="M4 19V6a2 2 0 012-2h9l5 5v10a2 2 0 01-2 2H6a2 2 0 01-2-2z" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linejoin="round"/><path d="M8 11h8M8 15h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                </div>
                <h3 class="f-display text-xl mb-2 text-gray-900">Log Aktivitas</h3>
                <p class="text-sm text-gray-500">
                    Setiap perubahan data — tambah, ubah, hapus — tercatat otomatis di log aktivitas sistem.
                </p>
            </div>
            <a href="{{ route('admin.log-aktivitas.index') }}"
               class="mt-5 inline-flex items-center justify-center gap-2 text-center text-sm font-semibold px-4 py-2.5 rounded-lg transition bg-blue-600 hover:bg-blue-700 text-white">
                Lihat Log Aktivitas
            </a>
        </div>
    </div>

</div>
@endsection