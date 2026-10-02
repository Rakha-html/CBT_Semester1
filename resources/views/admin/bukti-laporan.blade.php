@extends('layouts.app')

@section('title', 'Bukti Laporan Tugas - SIM Monitoring SPMB')
@section('page-title', 'Bukti Laporan Tugas')
@section('page-subtitle', 'Penerimaan dan verifikasi bukti deliverables dari panitia pelaksana SPMB')

@section('content')

{{-- Summary Metric Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200/80 p-4 text-center shadow-xs">
        <p class="text-2xl font-bold text-slate-900 tracking-tight">{{ $totalLaporan }}</p>
        <p class="text-xs text-slate-500 font-medium mt-1">Total Laporan Masuk</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200/80 p-4 text-center shadow-xs">
        <p class="text-2xl font-bold text-amber-600 tracking-tight">{{ $menungguVerifikasi }}</p>
        <p class="text-xs text-slate-500 font-medium mt-1">Menunggu Verifikasi</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200/80 p-4 text-center shadow-xs">
        <p class="text-2xl font-bold text-emerald-600 tracking-tight">{{ $disetujuiCount }}</p>
        <p class="text-xs text-slate-500 font-medium mt-1">Telah Disetujui</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200/80 p-4 text-center shadow-xs">
        <p class="text-2xl font-bold text-red-600 tracking-tight">{{ $perluRevisiCount }}</p>
        <p class="text-xs text-slate-500 font-medium mt-1">Perlu Revisi</p>
    </div>
</div>

{{-- Filter & Search Bar --}}
<div class="bg-white rounded-xl border border-slate-200/80 p-3.5 mb-6 shadow-xs">
    <form method="GET" action="{{ route('admin.bukti-laporan') }}" class="flex flex-col sm:flex-row gap-2.5">
        {{-- Filter Status Verifikasi --}}
        <select name="status_verifikasi" class="flex-1 px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors">
            <option value="">Semua Status Verifikasi</option>
            <option value="menunggu" {{ request('status_verifikasi') === 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
            <option value="disetujui" {{ request('status_verifikasi') === 'disetujui' ? 'selected' : '' }}>Telah Disetujui</option>
            <option value="perlu_revisi" {{ request('status_verifikasi') === 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
        </select>

        {{-- Filter Divisi --}}
        <select name="divisi_id" class="flex-1 px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors">
            <option value="">Semua Divisi</option>
            @foreach($divisiList as $d)
                <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>
            @endforeach
        </select>

        {{-- Pencarian --}}
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari panitia / tugas..."
                class="w-full pl-9 pr-3.5 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-xs text-slate-800 placeholder-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors">
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-sm shadow-blue-600/20 transition-colors shrink-0">
            Filter
        </button>
        @if(request()->hasAny(['status_verifikasi', 'divisi_id', 'search']))
            <a href="{{ route('admin.bukti-laporan') }}" class="px-3.5 py-2 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors shrink-0 text-center">
                Reset
            </a>
        @endif
    </form>
</div>

{{-- Submissions List --}}
@if($pengumpulanList->isEmpty())
    <div class="bg-white rounded-xl border border-slate-200/80 p-10 text-center shadow-xs">
        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 rounded-lg bg-slate-100 text-slate-500">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h3 class="text-sm font-semibold text-slate-900 mb-1">Belum Ada Bukti Laporan</h3>
        <p class="text-xs text-slate-500 font-medium">Tidak ada data bukti laporan tugas yang sesuai dengan kriteria filter.</p>
    </div>
@else
    {{-- Parent divide-y container for list items (no card-ception) --}}
    <div class="bg-white rounded-xl border border-slate-200/80 divide-y divide-slate-100 shadow-xs overflow-hidden">
        @foreach($pengumpulanList as $p)
        <div class="p-5 sm:p-6 hover:bg-slate-50/40 transition-colors">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3.5 border-b border-slate-100">
                {{-- Panitia Info --}}
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-50 border border-blue-100 text-blue-700 flex items-center justify-center font-semibold text-xs shrink-0">
                        {{ strtoupper(substr($p->pengirim->nama_lengkap, 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-semibold text-slate-900">{{ $p->pengirim->nama_lengkap }}</h4>
                            <span class="text-xs px-2 py-0.5 rounded-md font-medium bg-slate-100 text-slate-600 border border-slate-200/70">
                                {{ $p->tugas->divisi->nama_divisi }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            Dikirim: <span class="text-slate-700 font-semibold">{{ $p->dikirim_pada ? $p->dikirim_pada->format('d M Y, H:i') : '—' }}</span>
                            ({{ $p->dikirim_pada ? $p->dikirim_pada->diffForHumans() : '' }})
                        </p>
                    </div>
                </div>

                {{-- Status Badge & Review Link --}}
                <div class="flex items-center gap-2 shrink-0">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $p->badge_verifikasi['class'] }}">
                        {{ $p->badge_verifikasi['label'] }}
                    </span>
                    <a href="{{ route('admin.review', $p->tugas) }}"
                       class="text-xs text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-1 px-2 py-1 rounded-md hover:bg-blue-50 transition-colors">
                        Review Tugas &rarr;
                    </a>
                </div>
            </div>

            {{-- Task Info & Deliverables Details in Clean Grid without sub-cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 py-3.5">
                {{-- Left: Judul Tugas & Metadata --}}
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Tugas Terkait</span>
                    <h5 class="text-xs font-semibold text-slate-900 mb-1.5">{{ $p->tugas->judul_tugas }}</h5>
                    <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                        <span>Tipe: <span class="text-slate-700 capitalize font-medium">{{ $p->tugas->tipe_target }}</span></span>
                        <span>&middot;</span>
                        <span>Prioritas: <span class="capitalize font-medium">{{ $p->tugas->prioritas }}</span></span>
                        <span>&middot;</span>
                        <span>Progres: <span class="text-slate-900 font-semibold">{{ $p->tugas->progres_persen }}%</span></span>
                    </div>
                </div>

                {{-- Right: Bukti Laporan & Progres --}}
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Bukti Deliverables</span>

                    @if($p->tugas->tipe_target === 'numerik')
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-xs text-slate-600 font-medium">Progres Kuantitas:</span>
                            <span class="text-xs font-semibold text-slate-900 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200/80">
                                +{{ $p->jumlah_progres }} dokumen/item
                            </span>
                        </div>
                    @endif

                    @if($p->tautan_berkas)
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-slate-600 font-medium">Link Bukti:</span>
                            @if(Str::startsWith($p->tautan_berkas, ['http://', 'https://']))
                                <a href="{{ $p->tautan_berkas }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 text-xs font-medium text-slate-700 border border-slate-200 rounded-md hover:bg-slate-100 transition-colors shadow-xs truncate max-w-xs">
                                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    Buka Dokumen &nearr;
                                </a>
                            @else
                                <span class="text-xs font-mono bg-slate-50 px-2 py-0.5 rounded border border-slate-200 text-slate-700">
                                    {{ $p->tautan_berkas }}
                                </span>
                            @endif
                        </div>
                    @endif

                    @if($p->catatan_kendala)
                        <div class="mt-2 text-xs text-slate-600">
                            <span class="font-medium text-slate-700">Catatan Panitia:</span>
                            <p class="mt-0.5 italic text-slate-600 font-medium leading-relaxed">"{{ $p->catatan_kendala }}"</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Catatan Ketua jika sudah diverifikasi / revisi --}}
            @if($p->catatan_ketua)
                <div class="mb-3.5 p-2.5 rounded-lg bg-amber-50/70 border border-amber-200/80 text-xs">
                    <span class="font-medium text-amber-800 flex items-center gap-1 mb-0.5">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        Feedback Ketua SPMB:
                    </span>
                    <p class="text-amber-900 leading-relaxed">{{ $p->catatan_ketua }}</p>
                </div>
            @endif

            {{-- Action Buttons: Setujui / Minta Revisi (Soft low-contrast buttons per Rule 4) --}}
            <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <span class="text-xs text-slate-500 font-medium">
                    Aksi Verifikasi:
                </span>

                <div class="flex items-center gap-2">
                    {{-- Form Setujui --}}
                    <form method="POST" action="{{ route('admin.bukti-laporan.verifikasi', $p) }}" class="inline">
                        @csrf
                        <input type="hidden" name="aksi" value="disetujui">
                        <button type="submit"
                            onclick="return confirm('Apakah Anda yakin menyetujui bukti laporan tugas ini?')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/80 rounded-md transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            Setujui Laporan
                        </button>
                    </form>

                    {{-- Tombol Buka Modal Minta Revisi --}}
                    <button type="button" onclick="openRevisionModal({{ $p->id }})"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200/80 rounded-md transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        Minta Revisi
                    </button>
                </div>
            </div>

            {{-- Modal Minta Revisi --}}
            <div id="modal-revisi-{{ $p->id }}" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs hidden flex items-center justify-center p-4">
                <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-slate-200/80">
                    <div class="flex items-center justify-between mb-3.5">
                        <h4 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            Minta Revisi Laporan
                        </h4>
                        <button type="button" onclick="closeRevisionModal({{ $p->id }})" class="p-1 rounded-md text-slate-500 hover:text-slate-700 hover:bg-slate-100">
                            ✕
                        </button>
                    </div>

                    <p class="text-xs text-slate-600 font-medium mb-3.5">
                        Tuliskan instruksi perbaikan yang jelas kepada <strong>{{ $p->pengirim->nama_lengkap }}</strong> mengenai bukti deliverables ini.
                    </p>

                    <form method="POST" action="{{ route('admin.bukti-laporan.verifikasi', $p) }}">
                        @csrf
                        <input type="hidden" name="aksi" value="perlu_revisi">
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Instruksi Revisi <span class="text-red-500">*</span></label>
                            <textarea name="catatan_ketua" rows="4" required
                                class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-xs text-slate-800 placeholder-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 resize-none"
                                placeholder="Contoh: Format laporan belum sesuai template, tolong lengkapi bukti dokumentasi pada kolom C...">{{ $p->catatan_ketua }}</textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <button type="button" onclick="closeRevisionModal({{ $p->id }})"
                                class="px-3.5 py-2 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-3.5 py-2 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm shadow-blue-600/20 transition-colors">
                                Kirim Catatan Revisi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($pengumpulanList->hasPages())
        <div class="mt-4 bg-white rounded-xl border border-slate-200/80 p-3 shadow-xs">
            {{ $pengumpulanList->links() }}
        </div>
    @endif
@endif

<script>
    function openRevisionModal(id) {
        document.getElementById('modal-revisi-' + id).classList.remove('hidden');
    }
    function closeRevisionModal(id) {
        document.getElementById('modal-revisi-' + id).classList.add('hidden');
    }
</script>

@endsection
