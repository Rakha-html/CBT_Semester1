@extends('layouts.app')

@section('title', 'Review Tugas - SIM Monitoring SPMB')
@section('page-title', 'Review Tugas')
@section('page-subtitle', $tugas->judul_tugas)

@section('content')

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs mb-5">
    <a href="{{ route('admin.monitoring') }}" class="text-blue-600 hover:text-blue-700 font-semibold transition-colors">Monitoring</a>
    <svg class="w-3.5 h-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="text-slate-800 font-medium">Review</span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Detail Tugas (Left Col) --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Info Card --}}
        <div class="bg-white rounded-xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
            <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                <h3 class="text-base font-semibold text-slate-900">{{ $tugas->judul_tugas }}</h3>
                <div class="flex gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $tugas->badge_status['class'] }}">
                        {{ $tugas->badge_status['label'] }}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $tugas->badge_prioritas['class'] }}">
                        {{ $tugas->badge_prioritas['label'] }}
                    </span>
                </div>
            </div>

            @if($tugas->deskripsi)
                <p class="text-xs text-slate-600 leading-relaxed mb-4">{{ $tugas->deskripsi }}</p>
            @endif

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-3.5 border-t border-slate-100">
                <div>
                    <p class="text-xs text-slate-500 font-medium">Divisi</p>
                    <p class="text-xs font-semibold text-slate-900 mt-0.5">{{ $tugas->divisi->nama_divisi }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">PIC</p>
                    <p class="text-xs font-semibold text-slate-900 mt-0.5">{{ $tugas->penanggungjawab?->nama_lengkap ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Tipe Target</p>
                    <p class="text-xs font-semibold text-slate-900 mt-0.5 capitalize">{{ $tugas->tipe_target }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Tenggat Waktu</p>
                    <p class="text-xs font-semibold mt-0.5 {{ $tugas->tenggat_waktu?->isPast() && $tugas->status !== 'selesai' ? 'text-red-600' : 'text-slate-900' }}">
                        {{ $tugas->tenggat_waktu?->format('d M Y') ?? '—' }}
                    </p>
                </div>
            </div>

            <div class="mt-4 pt-3.5 border-t border-slate-100">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                        Progres Capaian Kerja (Satuan %)
                    </span>
                    <div class="flex items-center gap-2">
                        @if($tugas->tipe_target === 'numerik' && $tugas->target_jumlah)
                            <span class="text-xs text-slate-600 font-medium">Target: {{ $tugas->jumlah_tercapai }}/{{ $tugas->target_jumlah }}</span>
                        @endif
                        <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">
                            {{ $tugas->progres_persen }}%
                        </span>
                    </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 {{ $tugas->progres_persen >= 100 ? 'bg-emerald-600' : 'bg-blue-600' }}"
                         style="width: {{ $tugas->progres_persen }}%"></div>
                </div>
            </div>
        </div>

        {{-- Pengumpulan / Submissions --}}
        <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-xs">
            <div class="px-5 py-3.5 border-b border-slate-200/80">
                <h3 class="text-sm font-semibold text-slate-900">Pengumpulan Laporan</h3>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($tugas->pengumpulan->sortByDesc('dikirim_pada') as $p)
                <div class="px-5 py-4">
                    <div class="flex items-start justify-between gap-3 mb-2.5">
                        <div class="flex items-center gap-2.5">
                            <div class="flex items-center justify-center w-7 h-7 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-semibold">
                                {{ strtoupper(substr($p->pengirim->nama_lengkap, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-900">{{ $p->pengirim->nama_lengkap }}</p>
                                <p class="text-xs text-slate-500 font-medium">{{ $p->dikirim_pada->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $p->badge_verifikasi['class'] }}">
                            {{ $p->badge_verifikasi['label'] }}
                        </span>
                    </div>

                    {{-- Detail Pengumpulan --}}
                    <div class="pl-9.5 space-y-2">
                        @if($tugas->tipe_target === 'numerik' && $p->jumlah_progres > 0)
                            <p class="text-xs text-slate-600"><span class="font-medium text-slate-700">Jumlah progres:</span> {{ $p->jumlah_progres }}</p>
                        @endif

                        @if($p->tautan_berkas)
                            <p class="text-xs text-slate-600">
                                <span class="font-medium text-slate-700">Berkas:</span>
                                <a href="{{ $p->tautan_berkas }}" target="_blank" class="text-blue-600 hover:text-blue-700 font-semibold underline ml-1">{{ Str::limit($p->tautan_berkas, 50) }} &nearr;</a>
                            </p>
                        @endif

                        @if($p->catatan_kendala)
                            <div class="p-2.5 rounded-lg bg-amber-50/70 border border-amber-200/60 text-xs">
                                <p class="font-medium text-amber-800 mb-0.5">Catatan Kendala:</p>
                                <p class="text-amber-900">{{ $p->catatan_kendala }}</p>
                            </div>
                        @endif

                        @if($p->catatan_ketua)
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80 text-xs">
                                <p class="font-medium text-slate-700 mb-0.5">Catatan Ketua:</p>
                                <p class="text-slate-800">{{ $p->catatan_ketua }}</p>
                            </div>
                        @endif

                        {{-- Aksi Verifikasi (hanya untuk yang menunggu) --}}
                        @if($p->status_verifikasi === 'menunggu')
                            <div class="pt-3 mt-3 border-t border-slate-100">
                                <form method="POST" action="{{ route('admin.verifikasi', $p) }}" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan (Opsional)</label>
                                        <textarea name="catatan_ketua" rows="2" placeholder="Tulis catatan untuk panitia..."
                                            class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-xs text-slate-800 placeholder-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors resize-none"></textarea>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit" name="aksi" value="disetujui"
                                            class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/80 text-xs font-medium rounded-md transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Setujui
                                        </button>
                                        <button type="submit" name="aksi" value="perlu_revisi"
                                            class="flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200/80 text-xs font-medium rounded-md transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            Minta Revisi
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-slate-500 font-medium text-xs">
                    Belum ada pengumpulan untuk tugas ini.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Sidebar: Riwayat Aktivitas --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">Riwayat Aktivitas</h3>
            <div class="relative">
                <div class="absolute left-2.5 top-0 bottom-0 w-px bg-slate-200"></div>
                <div class="space-y-3.5">
                    @forelse($tugas->riwayatAktivitas->sortByDesc('created_at') as $r)
                    <div class="relative pl-7">
                        <div class="absolute left-1 top-1 w-2.5 h-2.5 rounded-full border-2 border-white
                            {{ match($r->jenis_aktivitas) {
                                'tugas_dibuat' => 'bg-blue-500',
                                'progres_dikirim' => 'bg-amber-500',
                                'verifikasi_disetujui' => 'bg-emerald-500',
                                'verifikasi_revisi' => 'bg-red-500',
                                default => 'bg-slate-500',
                            } }}"></div>
                        <p class="text-xs text-slate-700 leading-snug">{{ $r->keterangan }}</p>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $r->created_at->diffForHumans() }}</p>
                    </div>
                    @empty
                    <p class="text-xs text-slate-500 font-medium pl-7">Belum ada riwayat.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
