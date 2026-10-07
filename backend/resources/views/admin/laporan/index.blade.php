@extends('layouts.app')

@section('title', 'Cetak Laporan - Panel Admin')
@section('header-title', 'Cetak Laporan Pengembalian')

@section('content')
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 print:hidden">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <form action="{{ route('admin.laporan.index') }}" method="GET" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="tgl_mulai" value="{{ $tglMulai }}"
                           class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" value="{{ $tglSelesai }}"
                           class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-lg transition">
                    Filter
                </button>
                <a href="{{ route('admin.laporan.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 text-sm rounded-lg transition">
                    Reset
                </a>
                <button type="button" onclick="window.print()"
                        class="ml-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-semibold rounded-lg transition">
                    Cetak / Print
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 mt-4">
        <div class="p-5 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Laporan Pengembalian Alat</h3>
            <p class="text-sm text-gray-500">
                Periode: {{ $tglMulai ?? 'Semua' }} s/d {{ $tglSelesai ?? 'Semua' }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Tgl Kembali</th>
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Petugas Verifikasi</th>
                        <th class="py-3 px-4 border-b">Kondisi</th>
                        <th class="py-3 px-4 border-b">Denda</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($pengembalians as $pengembalian)
                        <tr class="align-top">
                            <td class="py-3 px-4 border-b">{{ $pengembalian->tgl_kembali }}</td>
                            <td class="py-3 px-4 border-b font-medium text-gray-900">
                                {{ $pengembalian->peminjaman->user->name ?? 'User Dihapus' }}
                            </td>
                            <td class="py-3 px-4 border-b text-gray-600">
                                {{ $pengembalian->petugas->name ?? 'Sistem/Dihapus' }}
                            </td>
                            <td class="py-3 px-4 border-b">{{ ucfirst($pengembalian->kondisi_kembali) }}</td>
                            <td class="py-3 px-4 border-b text-red-600 font-semibold">
                                Rp {{ number_format($pengembalian->denda, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">Tidak ada data pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($pengembalians->count())
                    <tfoot>
                        <tr class="bg-gray-50 font-semibold text-gray-800">
                            <td colspan="4" class="py-3 px-4 border-t text-right">Total Denda</td>
                            <td class="py-3 px-4 border-t text-red-600">
                                Rp {{ number_format($pengembalians->sum('denda'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <style>
        @media print {
            aside, header form, .print\:hidden { display: none !important; }
        }
    </style>
@endsection