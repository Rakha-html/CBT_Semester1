@extends('layouts.app')

@section('title', 'Dashboard - SIM Monitoring SPMB')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan capaian kinerja panitia SPMB')

@section('content')

{{-- Metric Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Total Tugas --}}
    <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 border border-blue-100">
                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-900 tracking-tight">{{ $totalTugas }}</p>
        <p class="text-xs text-slate-500 font-medium mt-1">Total Tugas</p>
    </div>

    {{-- Persentase Progres --}}
    <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-900 tracking-tight">{{ $persentaseProgres }}%</p>
        <p class="text-xs text-slate-500 font-medium mt-1">Progres Keseluruhan</p>
    </div>

    {{-- Review Pending --}}
    <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-amber-50 text-amber-700 border border-amber-200/60">
                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-900 tracking-tight">{{ $reviewPending }}</p>
        <p class="text-xs text-slate-500 font-medium mt-1">Menunggu Review</p>
    </div>

    {{-- Tugas Selesai --}}
    <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-700 border border-blue-200/60">
                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-900 tracking-tight">{{ $tugasSelesai }}</p>
        <p class="text-xs text-slate-500 font-medium mt-1">Tugas Selesai</p>
    </div>
</div>

{{-- Bar Chart Capaian per Divisi --}}
<div class="bg-white rounded-xl border border-slate-200/80 p-5 sm:p-6 mb-6 shadow-xs">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-slate-900">Capaian per Divisi</h3>
        <span class="text-xs text-slate-500 font-medium">Berdasarkan penyelesaian tugas</span>
    </div>
    <div class="space-y-3.5">
        @foreach($divisiCapaian as $dc)
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs font-medium text-slate-700 truncate pr-4">{{ $dc['nama'] }}</span>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs text-slate-500 font-medium">{{ $dc['tugas_selesai'] }}/{{ $dc['jumlah_tugas'] }} tugas</span>
                    <span class="text-xs font-semibold text-slate-900">{{ $dc['persentase'] }}%</span>
                </div>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 {{ $dc['persentase'] >= 75 ? 'bg-emerald-600' : ($dc['persentase'] >= 40 ? 'bg-blue-600' : 'bg-slate-500') }}"
                     style="width: {{ $dc['persentase'] }}%"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- 5 Tugas Terbaru --}}
<div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-xs">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200/80">
        <h3 class="text-sm font-semibold text-slate-900">Tugas Terbaru</h3>
        <a href="{{ route('admin.monitoring') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors">
            Lihat Semua &rarr;
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/60 border-b border-slate-200/80">
                    <th class="px-5 py-2.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">Tugas</th>
                    <th class="px-5 py-2.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">Divisi</th>
                    <th class="px-5 py-2.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">PIC</th>
                    <th class="px-5 py-2.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-2.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">Prioritas</th>
                    <th class="px-5 py-2.5 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Progres</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($tugasTerbaru as $t)
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="px-5 py-3.5">
                        <a href="{{ route('admin.review', $t) }}" class="text-xs font-medium text-slate-900 hover:text-blue-600 transition-colors">
                            {{ Str::limit($t->judul_tugas, 40) }}
                        </a>
                        @if($t->tenggat_waktu)
                            <p class="text-xs text-slate-500 font-medium mt-0.5">
                                Tenggat: {{ $t->tenggat_waktu->format('d M Y') }}
                                @if($t->tenggat_waktu->isPast() && $t->status !== 'selesai')
                                    <span class="text-red-600 font-semibold">&middot; Terlambat</span>
                                @endif
                            </p>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-xs text-slate-600">{{ $t->divisi->nama_divisi }}</td>
                    <td class="px-5 py-3.5 text-xs text-slate-600">{{ $t->penanggungjawab?->nama_lengkap ?? '—' }}</td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $t->badge_status['class'] }}">
                            {{ $t->badge_status['label'] }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $t->badge_prioritas['class'] }}">
                            {{ $t->badge_prioritas['label'] }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <span class="text-xs font-semibold text-slate-900">{{ $t->progres_persen }}%</span>
                            @if($t->tipe_target === 'numerik' && $t->target_jumlah)
                                <span class="text-xs text-slate-500 font-medium font-mono">({{ $t->jumlah_tercapai }}/{{ $t->target_jumlah }})</span>
                            @endif
                        </div>
                        <div class="w-20 bg-slate-100 rounded-full h-1.5 mt-1 ml-auto overflow-hidden">
                            <div class="h-full rounded-full {{ $t->progres_persen >= 100 ? 'bg-emerald-600' : 'bg-blue-600' }}" style="width: {{ $t->progres_persen }}%"></div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-slate-500 font-medium text-xs">Belum ada tugas yang dibuat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
