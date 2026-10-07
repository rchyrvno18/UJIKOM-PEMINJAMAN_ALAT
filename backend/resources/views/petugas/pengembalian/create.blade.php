@extends('layouts.app')

@section('title', 'Tambah Pengembalian - Panel Petugas')
@section('header-title', 'Form Pengembalian Alat')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<div class="max-w-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('petugas.pengembalian.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Pilih Transaksi Peminjaman (Status: Menunggu Pengembalian)</label>
            <select name="peminjaman_id" id="peminjaman_id" onchange="showAlatDetails(this)" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="" data-alat="">-- Pilih Data Peminjaman --</option>
                @foreach($peminjamans as $pjm)
                    @php
                        $listAlat = '';
                        foreach($pjm->detailPinjam as $detail) {
                            $nama_alat = $detail->alat->nama_alat ?? 'Alat Dihapus';
                            $listAlat .= "- {$nama_alat} (Jumlah: {$detail->jumlah} pcs)<br>";
                        }
                    @endphp
                    <option value="{{ $pjm->id }}" data-alat="{{ $listAlat }}" {{ old('peminjaman_id') == $pjm->id ? 'selected' : '' }}>
                        {{ $pjm->user->name }} - Tgl Pinjam: {{ \Carbon\Carbon::parse($pjm->tgl_pinjam)->format('d M Y') }}
                    </option>
                @endforeach
            </select>
            @error('peminjaman_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div id="info-alat-container" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg hidden">
            <p class="text-sm font-semibold text-blue-800 mb-2">📋 Alat yang harus dikembalikan:</p>
            <div id="daftar-alat" class="text-sm text-blue-700 font-medium ml-2"></div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Kondisi Alat Saat Dikembalikan</label>
            <select name="kondisi_kembali" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="baik" {{ old('kondisi_kembali') == 'baik' ? 'selected' : '' }}>Baik</option>
                <option value="rusak ringan" {{ old('kondisi_kembali') == 'rusak ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                <option value="rusak sedang" {{ old('kondisi_kembali') == 'rusak sedang' ? 'selected' : '' }}>Rusak Sedang</option>
                <option value="rusak berat" {{ old('kondisi_kembali') == 'rusak berat' ? 'selected' : '' }}>Rusak Berat</option>
            </select>
            @error('kondisi_kembali') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Denda Keterlambatan / Kerusakan (Rp) <span class="text-xs text-gray-400 font-normal">(Isi 0 jika tidak ada)</span></label>
            <input type="number" name="denda" value="{{ old('denda', 0) }}" min="0" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('denda') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('petugas.pengembalian.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Simpan Pengembalian</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    new TomSelect("#peminjaman_id", {
        create: false,
        placeholder: 'Cari nama peminjam atau tanggal pinjam...',
        sortField: { field: "text", direction: "asc" },
        onChange: function(value) {
            showAlatDetails(document.getElementById('peminjaman_id'));
        }
    });

    function showAlatDetails(selectElement) {
        const container = document.getElementById('info-alat-container');
        const daftarAlat = document.getElementById('daftar-alat');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const alatData = selectedOption.getAttribute('data-alat');

        if (alatData) {
            daftarAlat.innerHTML = alatData;
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
            daftarAlat.innerHTML = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const selectElement = document.getElementById('peminjaman_id');
        if(selectElement.value) {
            showAlatDetails(selectElement);
        }
    });
</script>
@endsection