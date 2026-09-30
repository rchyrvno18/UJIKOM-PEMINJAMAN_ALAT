@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Peminjaman')
@section('header-title', 'Ringkasan Aktivitas Sistem')

@section('content')
    <!-- Alert Selamat Datang -->
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm">
        Selamat datang, <strong class="font-semibold">{{ auth()->user()->name }}</strong>! Anda login sebagai hak akses
        <span class="uppercase font-bold text-emerald-900">{{ auth()->user()->role }}</span>.
    </div>

    <!-- Kartu Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Total User</p>
            <p class="text-3xl font-bold text-gray-800">{{ $totalUser }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Total Alat</p>
            <p class="text-3xl font-bold text-gray-800">{{ $totalAlat }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Total Kategori</p>
            <p class="text-3xl font-bold text-gray-800">{{ $totalKategori }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Peminjaman Aktif</p>
            <p class="text-3xl font-bold text-blue-600">{{ $peminjamanAktif }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Menunggu Persetujuan</p>
            <p class="text-3xl font-bold text-amber-600">{{ $peminjamanDiajukan }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Alat Stok Habis</p>
            <p class="text-3xl font-bold text-red-600">{{ $alatStokHabis }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex items-center justify-between">
        <div>
            <h3 class="font-semibold text-gray-800">Log Aktivitas</h3>
            <p class="text-sm text-gray-500">Riwayat aktivitas sistem sekarang ada di halaman terpisah.</p>
        </div>
        <a href="{{ route('admin.log-aktivitas.index') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            Lihat Log Aktivitas
        </a>
    </div>
@endsection