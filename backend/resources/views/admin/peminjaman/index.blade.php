@extends('layouts.app')

@section('title', 'Kelola Peminjaman - Panel Admin')
@section('header-title', 'Manajemen Transaksi Peminjaman')

@section('content')

    @if (session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

        <div class="p-5 border-b border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-bold text-gray-800">Daftar Transaksi Peminjaman</h3>

            <div class="flex w-full md:w-auto gap-3">
                <form action="{{ route('admin.peminjaman.index') }}" method="GET" class="flex w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama peminjam / status..."
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit"
                            class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 text-sm font-semibold transition">
                        Cari
                    </button>
                </form>

                <a href="{{ route('admin.peminjaman.create') }}"
                   class="whitespace-nowrap bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    + Tambah Peminjaman
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="py-3 px-6 border-b">Peminjam</th>
                        <th class="py-3 px-6 border-b">Alat yang Dipinjam</th>
                        <th class="py-3 px-6 border-b">Tgl Pinjam / Rencana Kembali</th>
                        <th class="py-3 px-6 border-b">Status</th>
                        <th class="py-3 px-6 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                    @forelse ($peminjamans as $peminjaman)
                        @php
                            $badgeColor = match($peminjaman->status) {
                                'dipinjam'  => 'bg-blue-100 text-blue-700',
                                'diajukan'  => 'bg-amber-100 text-amber-700',
                                'selesai'   => 'bg-emerald-100 text-emerald-700',
                                'telat'     => 'bg-red-100 text-red-700',
                                default     => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="py-4 px-6 font-medium text-gray-900">
                                {{ $peminjaman->user->name ?? 'User Dihapus' }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col gap-1">
                                    @foreach ($peminjaman->detailPinjam as $detail)
                                        <span>
                                            {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
                                            <span class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full ml-1">
                                                {{ $detail->jumlah }} pcs
                                            </span>
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-4 px-6 text-xs text-gray-600">
                                Pinjam: {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d M Y') }}<br>
                                Rencana: {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d M Y') }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColor }}">
                                    {{ ucfirst($peminjaman->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col gap-2 w-32">
                                    <form action="{{ route('admin.peminjaman.updateStatus', $peminjaman->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()"
                                            class="w-full text-xs font-medium rounded-lg px-2 py-1.5 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="diajukan" {{ $peminjaman->status == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                                            <option value="dipinjam" {{ $peminjaman->status == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                            <option value="selesai" {{ $peminjaman->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            <option value="telat" {{ $peminjaman->status == 'telat' ? 'selected' : '' }}>Telat</option>
                                        </select>
                                    </form>

                                    <form action="{{ route('admin.peminjaman.destroy', $peminjaman->id) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus data peminjaman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 px-6 text-center text-gray-500">
                                Belum ada data peminjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200">
            {{ $peminjamans->links() }}
        </div>
    </div>

@endsection