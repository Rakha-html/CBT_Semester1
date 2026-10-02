@extends('layouts.app')

@section('title', 'Ekspor LPJ - SIM Monitoring SPMB')
@section('page-title', 'Laporan Pertanggungjawaban')
@section('page-subtitle', 'Rekapitulasi capaian kinerja panitia SPMB')

@section('content')

{{-- Filter & Action Bar --}}
<div class="bg-white rounded-xl border border-slate-200/80 p-3.5 mb-6 flex flex-col sm:flex-row items-center justify-between gap-2.5 print:hidden shadow-xs">
    <form method="GET" action="{{ route('admin.laporan') }}" class="flex flex-1 gap-2.5 w-full sm:w-auto">
        <select name="divisi_id" class="flex-1 max-w-xs px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors">
            <option value="">Semua Divisi</option>
            @foreach($divisiList as $d)
                <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-sm shadow-blue-600/20 transition-colors shrink-0">
            Filter
        </button>
    </form>
    <button onclick="window.print()" class="flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium rounded-lg border border-slate-200 transition-colors shrink-0 shadow-xs">
        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Cetak / Ekspor
    </button>
</div>

{{-- Print Header --}}
<div class="hidden print:block text-center mb-8">
    <h1 class="text-xl font-bold">LAPORAN PERTANGGUNGJAWABAN</h1>
    <h2 class="text-lg font-semibold">Panitia SPMB Tahun {{ date('Y') }}</h2>
    <p class="text-sm text-slate-500 font-medium mt-1">Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
    <hr class="mt-4 border-slate-300">
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200/80 p-4 text-center shadow-xs">
        <p class="text-2xl font-bold text-slate-900 tracking-tight">{{ $totalTugas }}</p>
        <p class="text-xs text-slate-500 font-medium mt-1">Total Tugas</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200/80 p-4 text-center shadow-xs">
        <p class="text-2xl font-bold text-emerald-600 tracking-tight">{{ $tugasSelesai }}</p>
        <p class="text-xs text-slate-500 font-medium mt-1">Sudah Dikerjakan</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200/80 p-4 text-center shadow-xs">
        <p class="text-2xl font-bold text-amber-600 tracking-tight">{{ $tugasBerjalan }}</p>
        <p class="text-xs text-slate-500 font-medium mt-1">Sedang Dikerjakan</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200/80 p-4 text-center shadow-xs">
        <p class="text-2xl font-bold text-slate-600 tracking-tight">{{ $tugasBelum }}</p>
        <p class="text-xs text-slate-500 font-medium mt-1">Belum Dikerjakan</p>
    </div>
</div>

{{-- Report Table --}}
<div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-xs">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/60 border-b border-slate-200/80">
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">No</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Tugas</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Divisi</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">PIC</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Tipe</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Target</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Progres (%)</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Kategori</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Prioritas</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Tenggat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($tugas as $index => $t)
                <tr class="hover:bg-slate-50/60 transition-colors print:hover:bg-transparent">
                    <td class="px-5 py-3 text-xs text-slate-500 font-medium font-mono">{{ $index + 1 }}</td>
                    <td class="px-5 py-3">
                        <p class="text-xs font-semibold text-slate-900">{{ $t->judul_tugas }}</p>
                    </td>
                    <td class="px-5 py-3 text-xs text-slate-600">{{ $t->divisi->nama_divisi }}</td>
                    <td class="px-5 py-3 text-xs text-slate-600">{{ $t->penanggungjawab?->nama_lengkap ?? '—' }}</td>
                    <td class="px-5 py-3 text-xs text-slate-600 capitalize">{{ $t->tipe_target }}</td>
                    <td class="px-5 py-3 text-xs text-slate-600 font-mono">{{ $t->target_jumlah ?? '—' }}</td>
                    <td class="px-5 py-3 text-xs font-semibold text-slate-900">
                        <span class="text-xs font-semibold text-blue-600">{{ $t->progres_persen }}%</span>
                        @if($t->tipe_target === 'numerik' && $t->target_jumlah)
                            <span class="text-xs font-medium text-slate-500 font-mono">({{ $t->jumlah_tercapai }}/{{ $t->target_jumlah }})</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $t->badge_status['class'] }} print:bg-transparent print:px-0">
                            {{ $t->badge_status['label'] }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $t->badge_prioritas['class'] }} print:bg-transparent print:px-0">
                            {{ $t->badge_prioritas['label'] }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-xs text-slate-600">{{ $t->tenggat_waktu?->format('d/m/Y') ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-5 py-8 text-center text-slate-500 font-medium text-xs">Tidak ada data tugas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Print Footer --}}
<div class="hidden print:block mt-12">
    <div class="flex justify-between">
        <div class="text-center">
            <p class="text-sm">Mengetahui,</p>
            <p class="text-sm font-semibold mt-16">Kepala Sekolah</p>
            <p class="text-xs text-slate-500 font-medium">NIP. _______________</p>
        </div>
        <div class="text-center">
            <p class="text-sm">{{ now()->format('d F Y') }}</p>
            <p class="text-sm">Ketua Panitia SPMB,</p>
            <p class="text-sm font-semibold mt-14">{{ Auth::user()->nama_lengkap }}</p>
        </div>
    </div>
</div>

@endsection
