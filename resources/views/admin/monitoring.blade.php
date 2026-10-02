@extends('layouts.app')

@section('title', 'Monitoring Capaian - SIM Monitoring SPMB')
@section('page-title', 'Monitoring Capaian')
@section('page-subtitle', 'Pantau seluruh progress tugas panitia SPMB')

@section('content')

{{-- Filter Bar --}}
<div class="bg-white rounded-xl border border-slate-200/80 p-3.5 mb-6 shadow-xs">
    <form method="GET" action="{{ route('admin.monitoring') }}" class="flex flex-col sm:flex-row gap-2.5">
        {{-- Filter Divisi --}}
        <select name="divisi_id" class="flex-1 px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors">
            <option value="">Semua Divisi</option>
            @foreach($divisiList as $d)
                <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>
            @endforeach
        </select>

        {{-- Filter Status --}}
        <select name="status" class="flex-1 px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors">
            <option value="">Semua Status Kategori</option>
            <option value="belum_dikerjakan" {{ request('status') === 'belum_dikerjakan' ? 'selected' : '' }}>Belum Dikerjakan</option>
            <option value="sedang_dikerjakan" {{ request('status') === 'sedang_dikerjakan' ? 'selected' : '' }}>Sedang Dikerjakan</option>
            <option value="selesai" {{ request('status') === 'selesai' || request('status') === 'sudah_dikerjakan' ? 'selected' : '' }}>Sudah Dikerjakan</option>
        </select>

        {{-- Pencarian --}}
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tugas..."
                class="w-full pl-9 pr-3.5 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-xs text-slate-800 placeholder-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors">
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-sm shadow-blue-600/20 transition-colors shrink-0">
            Filter
        </button>
        @if(request()->hasAny(['divisi_id', 'status', 'search']))
            <a href="{{ route('admin.monitoring') }}" class="px-3.5 py-2 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors shrink-0 text-center">
                Reset
            </a>
        @endif
    </form>
</div>

{{-- Data Table --}}
<div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-xs">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/60 border-b border-slate-200/80">
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Tugas</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Divisi</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">PIC</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Kategori Status</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Prioritas</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Progres (%)</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Tenggat</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($tugas as $t)
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="px-5 py-3.5">
                        <p class="text-xs font-medium text-slate-900">{{ Str::limit($t->judul_tugas, 35) }}</p>
                        <p class="text-xs text-slate-500 font-medium mt-0.5 capitalize">{{ $t->tipe_target }}</p>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-xs text-slate-600">{{ $t->divisi->nama_divisi }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-xs text-slate-600">{{ $t->penanggungjawab?->nama_lengkap ?? '—' }}</span>
                    </td>
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
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-semibold text-slate-900">{{ $t->progres_persen }}%</span>
                            @if($t->tipe_target === 'numerik' && $t->target_jumlah)
                                <span class="text-xs text-slate-500 font-medium font-mono">({{ $t->jumlah_tercapai }}/{{ $t->target_jumlah }})</span>
                            @endif
                        </div>
                        <div class="w-20 bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $t->progres_persen >= 100 ? 'bg-emerald-600' : 'bg-blue-600' }}"
                                 style="width: {{ $t->progres_persen }}%"></div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        @if($t->tenggat_waktu)
                            <span class="text-xs {{ $t->tenggat_waktu->isPast() && $t->status !== 'selesai' ? 'text-red-600 font-semibold' : 'text-slate-600' }}">
                                {{ $t->tenggat_waktu->format('d M Y') }}
                            </span>
                        @else
                            <span class="text-xs text-slate-500 font-medium">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <a href="{{ route('admin.review', $t) }}"
                           class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200/80 rounded-md transition-colors">
                            Review
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-8 text-center text-slate-500 font-medium text-xs">
                        Tidak ada tugas yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($tugas->hasPages())
    <div class="px-5 py-3 border-t border-slate-200/80">
        {{ $tugas->links() }}
    </div>
    @endif
</div>

@endsection
